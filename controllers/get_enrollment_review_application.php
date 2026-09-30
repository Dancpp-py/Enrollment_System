<?php
require_once "../config/db.php";
header('Content-Type: application/json');

if (empty($_SESSION['admin_id'])) {
    // throw new Exception("Unauthorized.");
    showError(
        "Error!",
        "Unauthorized."
    );
}

$enrollment_id = $_GET['enrollment_id'] ?? null;

if (empty($enrollment_id) || !ctype_digit((string)$enrollment_id)) {
    // throw new Exception("Invalid enrollment ID.");
    showError(
        "Error!",
        "Invalid enrollment ID.."
    );
}

$stmt = $conn->prepare("
    SELECT
        e.enrollment_id,
        e.status,
        e.stage,
        e.grade_level,
        s.first_name,
        s.last_name,
        s.email,
        s.student_type,
        s.exam_number,
        st.strand_name
    FROM enrollments e
    INNER JOIN students s ON e.student_id = s.student_id
    INNER JOIN strands st ON e.strand_id = st.strand_id
    WHERE e.enrollment_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $enrollment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // throw new Exception("Enrollment not found.");
    showError(
        "Error!",
        "Enrollment not found."
    );
}

$applicant = $result->fetch_assoc();
$stmt->close();

if ($applicant['status'] !== 'Confirmed' || $applicant['stage'] !== 'Enrollment Review') {
    // throw new Exception("This application is not currently awaiting enrollment review.");
    showError(
        "Error!",
        "This application is not currently awaiting enrollment review."
    );
}

$stmt = $conn->prepare("
    SELECT math_score, english_score, filipino_score, science_score, average_score, exam_status
    FROM entrance_exam_results
    WHERE enrollment_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $enrollment_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("
    SELECT document_type, file_path
    FROM enrollment_documents
    WHERE enrollment_id = ?
");

$stmt->bind_param("i", $enrollment_id);
$stmt->execute();
$docsResult = $stmt->get_result();

$documents = [];
while ($doc = $docsResult->fetch_assoc()) {
    $documents[] = [
        'type' => $doc['document_type'],
        'url'  => '/enrollment_system/' . $doc['file_path'],
    ];
}
$stmt->close();

$studentName = $applicant['last_name'] . ', ' . $applicant['first_name'];
$resultColor = $exam['exam_status'] === 'Passed' ? 'text-green-600' : ($exam['exam_status'] === 'Failed' ? 'text-red-600' : 'text-gray-400');
$average = $exam['average_score'] !== null ? number_format($exam['average_score'], 2) : '—';

?>
