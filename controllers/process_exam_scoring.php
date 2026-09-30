<?php
session_start();
require_once "../config/db.php";
require_once '../views/includes/auth.php';
requireRole('Registrar', 'Super Admin');
require '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/admin/admin-exam-scoring");
    exit();
}

$enrollment_id = $_SESSION['exam_scoring_enrollment_id'] ?? null;
$action = $_POST['action'] ?? null;

if (empty($enrollment_id) || empty($action)) {
    // die("Invalid request.");
    showError(
            "Invalid Request",
            "The requested operation could not be completed because the required information is missing. Please return to the previous page and try again."
        );
}

if (!in_array($action, ['Approve', 'Reject'])) {
    // die("Invalid action.");
    showError(
            "Invalid Action.",
            "Please try again."
        );
}

$math = $_POST['math'] ?? null;
$english = $_POST['english'] ?? null;
$filipino = $_POST['filipino'] ?? null;
$science = $_POST['science'] ?? null;

foreach (['math' => $math, 'english' => $english, 'filipino' => $filipino, 'science' => $science] as $label => $value) {
    if ($value === null || $value === '' || !ctype_digit((string)$value) || (int)$value < 0 || (int)$value > 100) {
        // die("Invalid or missing {$label} score. Scores must be whole numbers from 0 to 100.");
        showError(
            "Invalid Score",
            "Invalid or missing {$label} score. Scores must be whole numbers from 0 to 100."
        );
    }
}

$math = (int)$math;
$english = (int)$english;
$filipino = (int)$filipino;
$science = (int)$science;

$conn->begin_transaction();

try {

    $stmt = $conn->prepare("
   SELECT
    e.enrollment_id,
    e.status,
    e.stage,
    e.grade_level,
    e.semester,

    s.student_id,
    s.first_name,
    s.last_name,
    s.student_type,
    s.email,

    st.strand_name,
    sy.school_year

FROM enrollments e

INNER JOIN students s
    ON e.student_id = s.student_id

LEFT JOIN strands st
    ON e.strand_id = st.strand_id

LEFT JOIN school_years sy
    ON e.school_year_id = sy.school_year_id

WHERE e.enrollment_id = ?
LIMIT 1
");

    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // throw new Exception("Enrollment not found.");
        showError(
        "Enrollment Not Found",
        "The selected enrollment record could not be located in the system. It may have been removed or is no longer available."
    );
    }

    $enrollment = $result->fetch_assoc();
    $stmt->close();

    // ---if ($enrollment['status'] !== 'Confirmed' || $enrollment['stage'] !== 'Exam Scheduled') {
    //     throw new Exception("This application is not currently awaiting exam scoring.");
    // }

    $average = round(($math + $english + $filipino + $science) / 4, 2);
    $examStatus = ($average >= 85) ? 'Passed' : 'Failed';

    $stmt = $conn->prepare("
        INSERT INTO entrance_exam_results
            (enrollment_id, math_score, english_score, filipino_score, science_score, average_score, exam_status, scored_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            math_score = VALUES(math_score),
            english_score = VALUES(english_score),
            filipino_score = VALUES(filipino_score),
            science_score = VALUES(science_score),
            average_score = VALUES(average_score),
            exam_status = VALUES(exam_status),
            scored_at = NOW()
    ");

    $stmt->bind_param("iiiiids", $enrollment_id, $math, $english, $filipino, $science, $average, $examStatus);
    $stmt->execute();
    $stmt->close();

    if ($action === 'Reject') {

        $stmt = $conn->prepare("
            UPDATE enrollments
            SET status = 'Rejected'
            WHERE enrollment_id = ?
        ");

        $stmt->bind_param("i", $enrollment_id);
        $stmt->execute();
        $stmt->close();

        $studentName = $enrollment['first_name'] . ' ' . $enrollment['last_name'];

        require_once "send_exam_failed_email.php";

        sendExamFailedEmail(
            $enrollment['email'],
            $studentName,
            $enrollment['student_type'],
            $enrollment['grade_level'],
            $enrollment['strand_name'],
            $enrollment['school_year'],
            $average
        );

$conn->commit();

unset($_SESSION['exam_scoring_enrollment_id']);

        showSuccess(
            "Rejected.",
            "Rejected Succesfully!",
            "window.location.href = '../views/admin/admin-exam-scoring.php';"
        );
        
        //header("Location: ../views/admin/admin-exam-scoring.php?success=1");
        exit();
    }

    // action === 'Approve'
    $stmt = $conn->prepare("
        UPDATE enrollments
        SET status = 'Confirmed',
            stage = 'Enrollment Review'
        WHERE enrollment_id = ?
    ");

    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();
    $stmt->close();

    $studentName = $enrollment['first_name'] . ' ' . $enrollment['last_name'];

require_once "send_exam_passed_email.php";

sendExamPassedEmail(
    $enrollment['email'],
    $studentName,
    $enrollment['student_type'],
    $enrollment['grade_level'],
    $enrollment['strand_name'],
    $enrollment['school_year'],
    $average
);

    $conn->commit();

    header("Location: ../views/admin/admin-exam-scoring?success=1");
    exit();

} catch (Exception $e) {

    $conn->rollback();
    die("Error: " . $e->getMessage());
}