<?php
require_once "../config/db.php";
require_once '../views/includes/auth.php';
requireRole('Registrar', 'Super Admin');
require '../views/includes/helpers.php'; 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/admin/admin-enrollment-review-v2");
    exit();
}

$enrollment_id = $_POST['enrollment_id'] ?? null;
$action        = $_POST['action'] ?? null;

if (empty($enrollment_id) || empty($action)) {
    showError(
        "Invalid Request",
        "The requested operation could not be completed because the required information is missing. Please return to the previous page and try again."
    );
}

if (!in_array($action, ['Approve', 'Reject'])) {
    showError(
        "Invalid Action",
        "The selected action is not recognized by the system. Please choose a valid action and try again."
    );
}

$enrollment_id = (int)$enrollment_id;

$conn->begin_transaction();

try {

    $stmt = $conn->prepare("
        SELECT
            e.enrollment_id,
            e.status,
            e.stage,
            e.strand_id,
            e.grade_level,
            e.semester,
            e.school_year_id,
            s.student_id,
            s.student_number,
            s.student_type,
            s.first_name,
            s.last_name,
            s.email,
            st.strand_name
        FROM enrollments e
        INNER JOIN students s ON e.student_id = s.student_id
        INNER JOIN strands st ON e.strand_id = st.strand_id
        WHERE e.enrollment_id = ?
        LIMIT 1
        FOR UPDATE
    ");
    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        showError(
        "Enrollment Not Found",
        "The selected enrollment record could not be found in the system. It may have been removed or is no longer available."
    );
    }

    $enrollment = $result->fetch_assoc();
    $stmt->close();

    if ($enrollment['status'] !== 'Confirmed' || $enrollment['stage'] !== 'Enrollment Review') {
        showError(
        "Review Not Available",
        "This enrollment application is not currently awaiting enrollment review. Please verify its current status before proceeding."
    );
    }

    $studentName = $enrollment['first_name'] . ' ' . $enrollment['last_name'];

    if ($action === 'Reject') {

        $stmt = $conn->prepare("
            UPDATE enrollments
            SET status = 'Rejected'
            WHERE enrollment_id = ?
        ");
        $stmt->bind_param("i", $enrollment_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        require_once "send_enrollment_rejected_email.php";
        sendEnrollmentRejectedEmail($enrollment['email'], $studentName);

        header("Location: ../views/admin/admin-enrollment-review?success=1");
        exit();
    }

    if (empty($enrollment['student_number'])) {

        $result = $conn->query("
            SELECT school_year
            FROM school_years
            WHERE is_active = 1
            LIMIT 1
        ");

        if ($result->num_rows === 0) {
            showError(
            "No Active School Year",
            "There is currently no active school year set in the system. Please activate a school year before continuing."
    );
        }

        $schoolYear = $result->fetch_assoc()['school_year'];
        $year = substr($schoolYear, 0, 4);

        $stmt = $conn->prepare("
            SELECT student_number
            FROM students
            WHERE student_number LIKE CONCAT(?, '%')
            ORDER BY student_number DESC
            LIMIT 1
        ");
        $stmt->bind_param("s", $year);
        $stmt->execute();
        $lastRow = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $sequence = $lastRow ? ((int)substr($lastRow['student_number'], 4) + 1) : 1;
        $officialStudentNumber = $year . str_pad($sequence, 5, "0", STR_PAD_LEFT);

        $stmt = $conn->prepare("
            UPDATE students
            SET student_number = ?
            WHERE student_id = ?
        ");
        $stmt->bind_param("si", $officialStudentNumber, $enrollment['student_id']);
        $stmt->execute();
        $stmt->close();

    } else {
        $officialStudentNumber = $enrollment['student_number'];
    }

    $plainPassword = null; // null = no new credentials to email out

    $stmt = $conn->prepare("SELECT password FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $enrollment['student_id']);
    $stmt->execute();
    $existingPassword = $stmt->get_result()->fetch_assoc()['password'] ?? null;
    $stmt->close();

    if (empty($existingPassword)) {
        // Username = student number, password = last name (as agreed, weak but intentional for now)
        $plainPassword = $enrollment['last_name'];
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE students SET password = ? WHERE student_id = ?");
        $stmt->bind_param("si", $hashedPassword, $enrollment['student_id']);
        $stmt->execute();
        $stmt->close();
    }



    if ($enrollment['student_type'] !== 'Existing') {
        $stmt = $conn->prepare("
            UPDATE students
            SET student_type = 'Existing'
            WHERE student_id = ?
        ");
        $stmt->bind_param("i", $enrollment['student_id']);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $conn->prepare("
        SELECT
            sec.section_id,
            sec.section_name,
            sec.capacity,
            COUNT(e2.enrollment_id) AS enrolled_count
        FROM sections sec
        LEFT JOIN enrollments e2
            ON e2.section_id = sec.section_id AND e2.status = 'Confirmed'
        WHERE sec.strand_id = ?
          AND sec.grade_level = ?
          AND sec.school_year_id = ?
        GROUP BY sec.section_id
        HAVING enrolled_count < sec.capacity
        ORDER BY sec.section_id ASC
        LIMIT 1
    ");
    $stmt->bind_param(
        "isi",
        $enrollment['strand_id'],
        $enrollment['grade_level'],
        $enrollment['school_year_id']
    );
    $stmt->execute();
    $section = $stmt->get_result()->fetch_assoc();
    $stmt->close();

     if (!$section) {
          showError(
                "Error.",
                "No available section with open capacity for {$enrollment['strand_name']} {$enrollment['grade_level']}.",
            );
            
    }

    $sectionId   = (int)$section['section_id'];
    $sectionName = $section['section_name'];

    $stmt = $conn->prepare("
        UPDATE enrollments
        SET status = 'Confirmed',
            stage = 'Enrolled',
            section_id = ?
        WHERE enrollment_id = ?
    ");
    $stmt->bind_param("ii", $sectionId, $enrollment_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM curriculum
        WHERE strand_id = ? AND grade_level = ? AND semester = ?
    ");
    $stmt->bind_param("iss", $enrollment['strand_id'], $enrollment['grade_level'], $enrollment['semester']);
    $stmt->execute();
    $totalSubjects = (int)$stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS scheduled
        FROM class_schedules cs
        INNER JOIN curriculum c ON cs.curriculum_id = c.curriculum_id
        WHERE cs.section_id = ?
          AND c.strand_id = ? AND c.grade_level = ? AND c.semester = ?
    ");
    $stmt->bind_param("iiss", $sectionId, $enrollment['strand_id'], $enrollment['grade_level'], $enrollment['semester']);
    $stmt->execute();
    $scheduledSubjects = (int)$stmt->get_result()->fetch_assoc()['scheduled'];
    $stmt->close();

    $scheduleComplete = $totalSubjects > 0 && $scheduledSubjects === $totalSubjects;

    $conn->commit();

    try {

        require_once "paymongo_helper.php";
        $paymentLink = null;
        try {
            $paymentLink = getOrCreateEnrollmentPaymentLink($conn, $enrollment_id);
        } catch (Throwable $paymentError) {
            error_log("Enrollment {$enrollment_id} confirmed, but PayMongo link creation failed: " . $paymentError->getMessage());
        }

        if ($scheduleComplete) {

            $enrollmentID          = $enrollment_id;
            $officialStudentNumber = $officialStudentNumber;
            $studentPassword       = $plainPassword;

            require_once "generate_registration_form.php";
            generateRegistrationForm($enrollment_id, $officialStudentNumber, $plainPassword, $paymentLink);
        } else {

            require_once "send_officially_enrolled_email.php";

            sendOfficiallyEnrolledEmail(
                $enrollment['email'],
                $studentName,
                $sectionName,
                $officialStudentNumber,
                $plainPassword,
                $paymentLink
            );
        }

    } catch (Throwable $mailError) {
        error_log("Enrollment {$enrollment_id} confirmed, but notification failed: " . $mailError->getMessage());
    }

    header("Location: ../views/admin/admin-enrollment-review?success=1");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}