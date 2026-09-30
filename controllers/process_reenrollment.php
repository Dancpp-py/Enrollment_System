<?php
require_once "../config/db.php";
require_once '../views/includes/auth.php';
require '../views/includes/helpers.php';
requireRole('Admissions', 'Super Admin');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/admin/admin-reenrollment");
    exit();
}

$enrollment_id = $_POST['enrollment_id'] ?? null;
$action        = $_POST['action'] ?? null;

if (empty($enrollment_id) || empty($action)) {
    die("Invalid request.");
}

if (!in_array($action, ['Approve', 'Reject'])) {
    die("Invalid action.");
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
        // throw new Exception("Enrollment not found.");
        showError(
        "Enrollment Not Found",
        "The selected enrollment record could not be found in the system. It may have been removed or is no longer available."
    );
    }

    $enrollment = $result->fetch_assoc();
    $stmt->close();

    if ($enrollment['student_type'] !== 'Existing') {
        // throw new Exception("This applicant is not an Existing student. Use the regular enrollment review flow instead.");
        showError(
        "Invalid Student Type",
        "This enrollment application is not for an existing student. Please use the regular enrollment review process instead."
    );
    }

    // if ($enrollment['status'] !== 'Pending' || $enrollment['stage'] !== 'Application Review') {
    //     // throw new Exception("This application is not currently awaiting re-enrollment review.");
    //     showError(
    //     "Review Not Available",
    //     "This application is not currently awaiting re-enrollment review. Please verify its current status before proceeding."
    // );
    // }

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

        header("Location: ../views/admin/admin-reenrollment?success=1");
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
            // throw new Exception("No active school year.");
            showError(
            "No Active School Year",
            "There is currently no active school year set in the system. Please activate a school year before proceeding."
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
        // throw new Exception(
        //     "No available section with open capacity for {$enrollment['strand_name']} {$enrollment['grade_level']}. " .
        //     "Please create a new section in Academic Management before approving this re-enrollment."
        // );
        showError(
        "No available section with open capacity for {$enrollment['strand_name']} {$enrollment['grade_level']}.",
        "Please create a new section in Academic Management before approving this re-enrollment."
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

        if ($scheduleComplete) {

            $enrollmentID          = $enrollment_id;
            $officialStudentNumber = $officialStudentNumber;

            require_once "generate_registration_form.php";
            generateRegistrationForm($enrollment_id, $officialStudentNumber);
        } else {

            require_once "send_officially_enrolled_email.php";

            sendOfficiallyEnrolledEmail(
                $enrollment['email'],
                $studentName,
                $officialStudentNumber,
                $sectionName
            );
        }

    } catch (Throwable $mailError) {
        error_log("Enrollment {$enrollment_id} confirmed, but notification failed: " . $mailError->getMessage());
    }

    header("Location: ../views/admin/admin-reenrollment?success=1");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}