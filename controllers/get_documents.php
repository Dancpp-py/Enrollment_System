<?php

require_once "../config/db.php";

if (!isset($_GET['id'])) {
    // throw new Exception("Enrollment ID is missing.");
    showError(
        "Error!",
        "Enrollment ID is missing."
    );
}

$enrollment_id = (int) $_GET['id'];

$stmt = $conn->prepare("
    SELECT
        document_type,
        file_path
    FROM enrollment_documents
    WHERE enrollment_id = ?
    ORDER BY document_type
");

$stmt->bind_param("i", $enrollment_id);
$stmt->execute();

$result = $stmt->get_result();

$documents = [];

while($row = $result->fetch_assoc()){
    $documents[] = $row;
}

$stmt->close();

header("Content-Type: application/json");
echo json_encode($documents);