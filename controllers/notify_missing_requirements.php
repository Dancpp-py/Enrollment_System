<?php
require_once "../config/db.php";
require_once "../views/includes/auth.php";
requireRole('Registrar', 'Admissions', 'Super Admin');

header('Content-Type: application/json');

require_once "send_missing_requirements_email.php";

$enrollment_id = isset($_POST['enrollment_id']) ? (int) $_POST['enrollment_id'] : 0;

if ($enrollment_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid enrollment ID.']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT
            e.enrollment_id,
            s.student_type,
            s.first_name,
            s.last_name,
            s.email
        FROM enrollments e
        INNER JOIN students s ON e.student_id = s.student_id
        WHERE e.enrollment_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$student) {
        throw new Exception("Enrollment not found.");
    }

    $requiredDocuments = [
        'PSA Birth Certificate',
        'Grade 10 Report Card (Form 138)',
        'Certificate of Good Moral',
        'Recent 2x2 ID Picture',
    ];

    if ($student['student_type'] === 'Transferee') {
        $requiredDocuments[] = 'Transcript of Records / Form 137';
    }

    $stmt = $conn->prepare("
        SELECT document_type
        FROM enrollment_documents
        WHERE enrollment_id = ?
    ");
    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();
    $uploadedTypes = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'document_type');
    $stmt->close();

    $missingDocuments = array_values(array_diff($requiredDocuments, $uploadedTypes));

    if (empty($missingDocuments)) {
        echo json_encode(['success' => false, 'message' => 'This student has already submitted all required documents.']);
        exit;
    }

    $studentName = trim($student['first_name'] . ' ' . $student['last_name']);

    sendMissingRequirementsEmail($student['email'], $studentName, $missingDocuments);

    echo json_encode(['success' => true, 'message' => 'Notification email sent successfully.']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}