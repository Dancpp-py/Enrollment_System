<?php

require_once "../config/db.php";
require_once "../views/includes/auth.php";
requireStudentLogin();
require_once "quiz_grading.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/student/student-quiz.php");
    exit;
}

$studentId = (int) $_SESSION['student_id'];
$attemptId = (int) ($_POST['attempt_id'] ?? 0);

if ($attemptId <= 0) {
    header("Location: ../views/student/student-quiz.php");
    exit;
}

// Verify this attempt belongs to the logged-in student and is still open.
$stmt = $conn->prepare("
    SELECT attempt_id, quiz_id, status
    FROM quiz_attempts
    WHERE attempt_id = ? AND student_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $attemptId, $studentId);
$stmt->execute();
$attempt = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$attempt) {
    header("Location: ../views/student/student-quiz.php");
    exit;
}

if ($attempt['status'] === 'Submitted') {
    // Already graded (e.g. double submit, or timer beat them to it) — just show the result.
    header("Location: ../views/student/student-quiz-result.php?attempt_id=" . $attemptId);
    exit;
}

// Pull "question_<id>" => "A"/"B"/"C"/"D" out of the POSTed form.
$answers = [];
foreach ($_POST as $key => $value) {
    if (strpos($key, 'question_') === 0) {
        $questionId = (int) substr($key, strlen('question_'));
        if ($questionId > 0) {
            $answers[$questionId] = $value;
        }
    }
}

try {
    finalizeQuizAttempt($conn, $attemptId, $answers);
} catch (Exception $e) {
    error_log("Quiz grading failed for attempt {$attemptId}: " . $e->getMessage());
    header("Location: ../views/student/student-quiz-v2.php?quiz_id=" . (int) $attempt['quiz_id'] . "&error=" . urlencode("Something went wrong submitting your quiz. Please try again."));
    exit;
}

header("Location: ../views/student/student-quiz-result.php?attempt_id=" . $attemptId);
exit;