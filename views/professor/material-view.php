<?php

require_once "../../config/db.php";
require_once "../includes/auth.php";

$professor_id = currentProfessorId();

$material_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($material_id <= 0) {
    http_response_code(400);
    exit("Invalid material.");
}

/*
|--------------------------------------------------------------------------
| Get material and verify that it belongs to the logged-in professor
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        m.material_id,
        m.title,
        m.description,
        m.original_file_name,
        m.stored_file_name,
        m.file_path,
        m.file_type,
        m.file_size,
        m.created_at,
        m.updated_at,
        lc.lms_class_id,
        cs.professor_id
    FROM lms_materials m
    INNER JOIN lms_classes lc
        ON m.lms_class_id = lc.lms_class_id
    INNER JOIN class_schedules cs
        ON lc.schedule_id = cs.schedule_id
    WHERE m.material_id = ?
      AND cs.professor_id = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    exit("Database error.");
}

$stmt->bind_param("ii", $material_id, $professor_id);
$stmt->execute();

$result = $stmt->get_result();
$material = $result->fetch_assoc();

$stmt->close();

if (!$material) {
    http_response_code(404);
    exit("Material not found or you do not have permission to access it.");
}


/*
|--------------------------------------------------------------------------
| Locate physical file
|--------------------------------------------------------------------------
*/

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/enrollment_system/uploads/';

$filePath = $uploadDir . $material['stored_file_name'];

if (!is_file($filePath)) {
    http_response_code(404);
    exit("The file could not be found on the server.");
}



$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filePath);
finfo_close($finfo);

if (!$mimeType) {
    $mimeType = 'application/octet-stream';
}


$fileSize = filesize($filePath);


$inlineTypes = [
    'application/pdf',
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp',
    'text/plain'
];

$disposition = in_array($mimeType, $inlineTypes, true)
    ? 'inline'
    : 'attachment';



header('Content-Type: ' . $mimeType);
header('Content-Length: ' . $fileSize);
header(
    'Content-Disposition: ' .
    $disposition .
    '; filename="' .
    basename($material['original_file_name']) .
    '"'
);

header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($filePath);
exit;