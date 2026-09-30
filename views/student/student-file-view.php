<?php

require_once '../../config/db.php';
require_once '../includes/auth.php';

requireStudentLogin();

$student_id = (int) $_SESSION['student_id'];
$material_id = (int) ($_GET['id'] ?? 0);
$download = isset($_GET['download']) && $_GET['download'] === '1';


/*
|--------------------------------------------------------------------------
| Validate Material ID
|--------------------------------------------------------------------------
*/

if ($material_id <= 0) {
    http_response_code(400);
    die('Invalid material.');
}


/*
|--------------------------------------------------------------------------
| Get Material + Verify Student Access
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        lm.material_id,
        lm.lms_class_id,
        lm.title,
        lm.description,
        lm.original_file_name,
        lm.stored_file_name,
        lm.file_path,
        lm.file_type,
        lm.file_size,
        lm.created_at,

        lc.schedule_id,
        lc.status AS lms_class_status,

        cs.section_id,

        sub.subject_name,
        sub.subject_code,

        p.first_name AS prof_first,
        p.last_name AS prof_last

    FROM lms_materials lm

    INNER JOIN lms_classes lc
        ON lc.lms_class_id = lm.lms_class_id

    INNER JOIN class_schedules cs
        ON cs.schedule_id = lc.schedule_id

    INNER JOIN curriculum c
        ON c.curriculum_id = cs.curriculum_id

    INNER JOIN subjects sub
        ON sub.subject_id = c.subject_id

    INNER JOIN enrollments e
        ON e.section_id = cs.section_id

    LEFT JOIN professors p
        ON p.professor_id = cs.professor_id

    WHERE lm.material_id = ?
      AND e.student_id = ?
      AND e.status = 'Confirmed'
      AND e.stage = 'Enrolled'

    LIMIT 1
");

if (!$stmt) {
    http_response_code(500);
    die('Unable to prepare the request.');
}

$stmt->bind_param(
    "ii",
    $material_id,
    $student_id
);

$stmt->execute();

$material = $stmt->get_result()->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Material Not Found / Unauthorized
|--------------------------------------------------------------------------
*/

if (!$material) {
    http_response_code(404);
    die('Material not found or you are not authorized to access it.');
}


/*
|--------------------------------------------------------------------------
| Check LMS Class Status
|--------------------------------------------------------------------------
*/

if ($material['lms_class_status'] !== 'Active') {
    http_response_code(403);
    die('This LMS class is currently unavailable.');
}


/*
|--------------------------------------------------------------------------
| Locate Uploaded File
|--------------------------------------------------------------------------
|
| All uploaded LMS files are stored in:
|
| /enrollment_system/uploads/
|
*/

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/enrollment_system/uploads/';

$storedFileName = basename($material['stored_file_name']);

$filePath = $uploadDir . $storedFileName;


/*
|--------------------------------------------------------------------------
| Check Physical File
|--------------------------------------------------------------------------
*/

if (!is_file($filePath)) {
    http_response_code(404);
    die('The requested material file could not be found.');
}


/*
|--------------------------------------------------------------------------
| Determine Download Filename
|--------------------------------------------------------------------------
*/

$downloadName = $material['original_file_name'];

if (empty($downloadName)) {
    $downloadName = $material['title'];
}

$downloadName = basename($downloadName);


/*
|--------------------------------------------------------------------------
| Determine MIME Type
|--------------------------------------------------------------------------
*/

$fileType = $material['file_type'];

if (
    empty($fileType) ||
    $fileType === 'application/octet-stream'
) {
    $detectedType = mime_content_type($filePath);

    if ($detectedType) {
        $fileType = $detectedType;
    } else {
        $fileType = 'application/octet-stream';
    }
}


/*
|--------------------------------------------------------------------------
| Force Download
|--------------------------------------------------------------------------
*/

if ($download) {

    header('Content-Description: File Transfer');
    header('Content-Type: ' . $fileType);

    header(
        'Content-Disposition: attachment; filename="' .
        str_replace('"', '', $downloadName) .
        '"'
    );

    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: public');

    readfile($filePath);

    exit;
}


/*
|--------------------------------------------------------------------------
| Determine File Extension
|--------------------------------------------------------------------------
*/

$extension = strtolower(
    pathinfo($downloadName, PATHINFO_EXTENSION)
);


/*
|--------------------------------------------------------------------------
| Files That Can Be Viewed Directly in Browser
|--------------------------------------------------------------------------
*/

$inlineTypes = [
    'pdf'  => 'application/pdf',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
    'webp' => 'image/webp',
    'txt'  => 'text/plain',
];


/*
|--------------------------------------------------------------------------
| Display Supported Files Inline
|--------------------------------------------------------------------------
*/

if (isset($inlineTypes[$extension])) {

    $fileType = $inlineTypes[$extension];

    header('Content-Type: ' . $fileType);

    header(
        'Content-Disposition: inline; filename="' .
        str_replace('"', '', $downloadName) .
        '"'
    );

    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    readfile($filePath);

    exit;
}


/*
|--------------------------------------------------------------------------
| Unsupported Inline File
|--------------------------------------------------------------------------
|
| PPT, PPTX, DOC, DOCX, XLS, XLSX, ZIP, etc.
| will be downloaded instead of displayed.
|--------------------------------------------------------------------------
*/

header('Content-Description: File Transfer');
header('Content-Type: ' . $fileType);

header(
    'Content-Disposition: attachment; filename="' .
    str_replace('"', '', $downloadName) .
    '"'
);

header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

readfile($filePath);

exit;