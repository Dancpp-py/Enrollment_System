<?php

session_start();

require_once "../config/db.php";
require_once '../views/includes/auth.php';
requireRole('Admissions', 'Super Admin');
require '../views/includes/helpers.php';


/* ===========================================
   GET ENROLLMENT ID FROM SESSION
=========================================== */

$enrollment_id = $_SESSION['application_review_enrollment_id'] ?? null;
$action = $_POST['action'] ?? null;

if (empty($enrollment_id) || !is_int($enrollment_id) || empty($action)) {
    die("Invalid request.");
}


/* ===========================================
   VALIDATE ACTION
=========================================== */

if (!in_array($action, ['Approve', 'Reject'], true)) {
    die("Invalid action.");
}


$conn->begin_transaction();

try {

    /* ===========================================
       GET APPLICATION
    =========================================== */

    $stmt = $conn->prepare("
        SELECT
            e.enrollment_id,
            e.status,
            e.student_id,
            s.student_number,
            s.exam_number,
            s.student_type
        FROM enrollments e
        INNER JOIN students s
            ON e.student_id = s.student_id
        WHERE e.enrollment_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {

        $stmt->close();
        throw new Exception("Enrollment not found.");
    }

    $student = $result->fetch_assoc();

    $stmt->close();


    /* ===========================================
       CHECK APPLICATION STATUS
    =========================================== */

    if ($student['status'] !== 'Pending') {

        throw new Exception(
            "This application is no longer available for review."
        );
    }


    /* ===========================================
       REJECT APPLICATION
    =========================================== */

    if ($action === "Reject") {

        $stmt = $conn->prepare("
            UPDATE enrollments
            SET status = 'Rejected'
            WHERE enrollment_id = ?
              AND status = 'Pending'
        ");

        $stmt->bind_param("i", $enrollment_id);
        $stmt->execute();

        if ($stmt->affected_rows !== 1) {
            $stmt->close();
            throw new Exception("Application could not be rejected.");
        }

        $stmt->close();

        $conn->commit();

        /* Clear selected application */
        unset($_SESSION['application_review_enrollment_id']);

        showSuccess(
            "Rejected.",
            "Rejected Successfully!",
            "window.location.href = '../views/admin/admin-dashboard.php';"
        );

        exit();
    }


    /* ===========================================
       APPROVE APPLICATION
    =========================================== */

    if (empty($student['exam_number'])) {

        /* ===========================================
           GET ACTIVE SCHOOL YEAR
        =========================================== */

        $result = $conn->query("
            SELECT school_year
            FROM school_years
            WHERE is_active = 1
            LIMIT 1
        ");

        if ($result->num_rows === 0) {

            throw new Exception(
                "There is currently no active school year set in the system. Please activate a school year before proceeding."
            );
        }

        $schoolYear = $result->fetch_assoc()['school_year'];

        $year = substr($schoolYear, 0, 4);


        /* ===========================================
           GET LAST EXAM NUMBER
        =========================================== */

        $stmt = $conn->prepare("
            SELECT exam_number
            FROM students
            WHERE exam_number LIKE CONCAT('EX', ?, '-%')
            ORDER BY exam_number DESC
            LIMIT 1
        ");

        $stmt->bind_param("s", $year);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $lastExam = $result->fetch_assoc()['exam_number'];

            $sequence = (int) substr($lastExam, 7);

            $sequence++;

        } else {

            $sequence = 1;
        }

        $examNumber = "EX" . $year . "-" .
            str_pad($sequence, 4, "0", STR_PAD_LEFT);

        $stmt->close();


        /* ===========================================
           ASSIGN EXAM NUMBER
        =========================================== */

        $stmt = $conn->prepare("
            UPDATE students
            SET exam_number = ?
            WHERE student_id = ?
        ");

        $stmt->bind_param(
            "si",
            $examNumber,
            $student['student_id']
        );

        $stmt->execute();
        $stmt->close();
    }


    /* ===========================================
       CONFIRM ENROLLMENT
    =========================================== */

    $stmt = $conn->prepare("
        UPDATE enrollments
        SET
            status = 'Confirmed',
            stage = 'Exam Scheduled'
        WHERE enrollment_id = ?
          AND status = 'Pending'
    ");

    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();

    if ($stmt->affected_rows !== 1) {

        $stmt->close();

        throw new Exception(
            "Application could not be approved."
        );
    }

    $stmt->close();


    /* ===========================================
       GENERATE EXAM NOTICE
    =========================================== */

    $enrollmentID = $enrollment_id;

    include "generate_exam_notice.php";


    /* ===========================================
       COMMIT TRANSACTION
    =========================================== */

    $conn->commit();


    /* Clear selected application */
    unset($_SESSION['application_review_enrollment_id']);


    header("Location: ../views/admin/admin-dashboard?success=1");
    exit();


} catch (Exception $e) {

    $conn->rollback();

    die("Error: " . $e->getMessage());
}