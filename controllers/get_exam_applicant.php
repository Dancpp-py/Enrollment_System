<?php

require_once "../config/db.php";

header('Content-Type: application/json');

$enrollment_id = $_GET['enrollment_id'] ?? null;

if (empty($enrollment_id) || !ctype_digit((string)$enrollment_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid enrollment_id.']);
    exit();
}

$enrollment_id = (int)$enrollment_id;

$stmt = $conn->prepare("
    SELECT
        e.enrollment_id,
        e.grade_level,
        s.first_name,
        s.last_name,
        s.email,
        s.student_type,
        s.exam_number,
        st.strand_name
    FROM enrollments e
    INNER JOIN students s
        ON e.student_id = s.student_id
    INNER JOIN strands st
        ON e.strand_id = st.strand_id
    WHERE e.enrollment_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $enrollment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Enrollment not found.']);
    exit();
}

$applicant = $result->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("
    SELECT document_type, file_path
    FROM enrollment_documents
    WHERE enrollment_id = ?
    ORDER BY document_type
");

$stmt->bind_param("i", $enrollment_id);
$stmt->execute();
$docResult = $stmt->get_result();

$documents = [];
while ($doc = $docResult->fetch_assoc()) {
    $documents[] = $doc;
}
$stmt->close();

echo json_encode([
    'enrollment_id' => $applicant['enrollment_id'],
    'name' => $applicant['last_name'] . ', ' . $applicant['first_name'],
    'email' => $applicant['email'],
    'student_type' => $applicant['student_type'],
    'exam_number' => $applicant['exam_number'],
    'grade_level' => $applicant['grade_level'],
    'strand_name' => $applicant['strand_name'],
    'documents' => $documents,
]);