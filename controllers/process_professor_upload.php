<?php

require_once "../config/db.php";
require_once "../views/includes/auth.php";

$professor_id = currentProfessorId();

include "../views/includes/helpers.php";

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/enrollment_system/uploads/';

$allowedExtensions = [
    'ppt',
    'pptx',
    'pdf',
    'doc',
    'docx',
    'xls',
    'xlsx',
    'jpg',
    'jpeg',
    'png',
    'zip'
];

$maxFileSize = 25 * 1024 * 1024; // 25 MB

function redirectWithMessage($type, $message)
{
    header(
        "Location: ../views/professor/professor-upload.php?" .
        http_build_query([
            'message_type' => $type,
            'message' => $message
        ])
    );
    exit;
}

function getFileExtension($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/*
|--------------------------------------------------------------------------
| Synchronize LMS Classes
|--------------------------------------------------------------------------
*/

$syncLmsClassesStmt = $conn->prepare("
    INSERT IGNORE INTO lms_classes (
        schedule_id,
        status
    )
    SELECT
        cs.schedule_id,
        'Active'
    FROM class_schedules cs
    INNER JOIN sections sec
        ON sec.section_id = cs.section_id
    INNER JOIN school_years sy
        ON sy.school_year_id = sec.school_year_id
    LEFT JOIN lms_classes existing_lms
        ON existing_lms.schedule_id = cs.schedule_id
    WHERE cs.professor_id = ?
      AND sy.is_active = 1
      AND existing_lms.lms_class_id IS NULL
");

if (!$syncLmsClassesStmt) {
    die("Failed to prepare LMS class synchronization query.");
}

$syncLmsClassesStmt->bind_param("i", $professor_id);

if (!$syncLmsClassesStmt->execute()) {
    $syncLmsClassesStmt->close();
    die("Failed to synchronize assigned LMS classes.");
}

$syncLmsClassesStmt->close();

/*
|--------------------------------------------------------------------------
| Upload Material
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_material'])) {
    $lms_class_id = (int) ($_POST['lms_class_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($lms_class_id <= 0) {
        showError(
            'Error',
            'Please select a subject and section.',
            'window.history.back();'
        );
    }

    if ($title === '') {
        showError(
            'Error',
            'Material title is required.',
            'window.history.back();'
        );
    }

    if (!isset($_FILES['material_file'])) {
        showError(
            'Error',
            'Please select a file.',
            'window.history.back();'
        );
    }

    $file = $_FILES['material_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        showError(
            'Error',
            'There was a problem uploading the file.',
            'window.history.back();'
        );
    }

    if ($file['size'] <= 0) {
        showError(
            'Error',
            'The selected file is empty.',
            'window.history.back();'
        );
    }

    if ($file['size'] > $maxFileSize) {
        showError(
            'Error',
            'The file is too large. Maximum allowed size is 25 MB.',
            'window.history.back();'
        );
    }

    $originalFileName = basename($file['name']);
    $extension = getFileExtension($originalFileName);

    if (!in_array($extension, $allowedExtensions, true)) {
        showError(
            'Error',
            'This file type is not allowed.',
            'window.history.back();'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Verify Professor Owns LMS Class
    |--------------------------------------------------------------------------
    */

    $classStmt = $conn->prepare("
        SELECT
            lc.lms_class_id
        FROM lms_classes lc
        INNER JOIN class_schedules cs
            ON cs.schedule_id = lc.schedule_id
        WHERE lc.lms_class_id = ?
          AND cs.professor_id = ?
          AND lc.status = 'Active'
        LIMIT 1
    ");

    $classStmt->bind_param(
        "ii",
        $lms_class_id,
        $professor_id
    );

    $classStmt->execute();

    $classResult = $classStmt->get_result();

    if ($classResult->num_rows === 0) {
        $classStmt->close();

        showError(
            'Error',
            'You are not authorized to upload material for this class.',
            'window.history.back();'
        );
    }

    $classStmt->close();

    /*
    |--------------------------------------------------------------------------
    | Validate Upload Directory
    |--------------------------------------------------------------------------
    */

    if (!is_dir($uploadDir)) {
        showError(
            'Error',
            'The upload directory does not exist.',
            'window.history.back();'
        );
    }

    if (!is_writable($uploadDir)) {
        showError(
            'Error',
            'The upload directory is not writable.',
            'window.history.back();'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Save Physical File
    |--------------------------------------------------------------------------
    */

    $storedFileName = bin2hex(random_bytes(16)) . '.' . $extension;
    $destination = $uploadDir . $storedFileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        showError(
            'Error',
            'Failed to save the uploaded file.',
            'window.history.back();'
        );
    }

    $filePath = 'uploads/' . $storedFileName;
    $fileType = $file['type'] ?? 'application/octet-stream';
    $fileSize = (int) $file['size'];

    /*
    |--------------------------------------------------------------------------
    | Insert Material
    |--------------------------------------------------------------------------
    */

    $insertStmt = $conn->prepare("
        INSERT INTO lms_materials (
            lms_class_id,
            title,
            description,
            original_file_name,
            stored_file_name,
            file_path,
            file_type,
            file_size,
            uploaded_by
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $insertStmt->bind_param(
        "issssssii",
        $lms_class_id,
        $title,
        $description,
        $originalFileName,
        $storedFileName,
        $filePath,
        $fileType,
        $fileSize,
        $professor_id
    );

    if (!$insertStmt->execute()) {
        $insertStmt->close();

        if (file_exists($destination)) {
            unlink($destination);
        }

        showError(
            'Error',
            'The material could not be saved to the database.',
            'window.history.back();'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Get Newly Created Material ID
    |--------------------------------------------------------------------------
    */

    $material_id = (int) $insertStmt->insert_id;

    $insertStmt->close();

    /*
    |--------------------------------------------------------------------------
    | CREATE STUDENT NOTIFICATIONS
    |--------------------------------------------------------------------------
    |
    | Create one notification for every currently enrolled student
    | belonging to the section/class of this material.
    |
    | INSERT IGNORE prevents duplicate notifications because
    | lms_notifications has:
    |
    | UNIQUE KEY (
    |     student_id,
    |     notification_type,
    |     reference_id
    | )
    |
    */

    $notificationStmt = $conn->prepare("
        INSERT IGNORE INTO lms_notifications (
            student_id,
            lms_class_id,
            notification_type,
            reference_id,
            title,
            message
        )
        SELECT
            e.student_id,
            lm.lms_class_id,
            'Material',
            lm.material_id,
            'New Learning Material',
            CONCAT(
                'Your professor uploaded a new material: ',
                lm.title
            )
        FROM lms_materials lm
        INNER JOIN lms_classes lc
            ON lc.lms_class_id = lm.lms_class_id
        INNER JOIN class_schedules cs
            ON cs.schedule_id = lc.schedule_id
        INNER JOIN sections sec
            ON sec.section_id = cs.section_id
        INNER JOIN school_years sy
            ON sy.school_year_id = sec.school_year_id
        INNER JOIN enrollments e
            ON e.section_id = sec.section_id
            AND e.school_year_id = sy.school_year_id
            AND e.status = 'Confirmed'
            AND e.stage = 'Enrolled'
        WHERE lm.material_id = ?
          AND lc.status = 'Active'
    ");

    if ($notificationStmt) {
        $notificationStmt->bind_param(
            "i",
            $material_id
        );

        $notificationStmt->execute();
        $notificationStmt->close();
    }

    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    showSuccess(
        'Success!',
        'Learning material uploaded successfully.',
        'window.history.back();'
    );
}

/*
|--------------------------------------------------------------------------
| Update Material
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_material'])) {
    $material_id = (int) ($_POST['material_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($material_id <= 0) {
        showError(
            'Error',
            'Invalid material.',
            'window.history.back();'
        );
    }

    if ($title === '') {
        showError(
            'Error',
            'Material title is required.',
            'window.history.back();'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Find Existing Material
    |--------------------------------------------------------------------------
    */

    $materialStmt = $conn->prepare("
        SELECT
            lm.material_id,
            lm.lms_class_id,
            lm.original_file_name,
            lm.stored_file_name,
            lm.file_path,
            lm.file_type,
            lm.file_size
        FROM lms_materials lm
        INNER JOIN lms_classes lc
            ON lc.lms_class_id = lm.lms_class_id
        INNER JOIN class_schedules cs
            ON cs.schedule_id = lc.schedule_id
        WHERE lm.material_id = ?
          AND lm.uploaded_by = ?
          AND cs.professor_id = ?
        LIMIT 1
    ");

    $materialStmt->bind_param(
        "iii",
        $material_id,
        $professor_id,
        $professor_id
    );

    $materialStmt->execute();

    $materialResult = $materialStmt->get_result();

    if ($materialResult->num_rows === 0) {
        $materialStmt->close();

        showError(
            'Error',
            'Material not found or you are not authorized to edit it.',
            'window.history.back();'
        );
    }

    $existingMaterial = $materialResult->fetch_assoc();

    $materialStmt->close();

    /*
    |--------------------------------------------------------------------------
    | Preserve Existing File Information
    |--------------------------------------------------------------------------
    */

    $newStoredFileName = $existingMaterial['stored_file_name'];
    $newOriginalFileName = $existingMaterial['original_file_name'];
    $newFilePath = $existingMaterial['file_path'];
    $newFileType = $existingMaterial['file_type'];
    $newFileSize = (int) $existingMaterial['file_size'];

    $oldPhysicalFile = $uploadDir . $existingMaterial['stored_file_name'];
    $newPhysicalFile = null;

    /*
    |--------------------------------------------------------------------------
    | Replacement File
    |--------------------------------------------------------------------------
    */

    if (
        isset($_FILES['replacement_file']) &&
        $_FILES['replacement_file']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        $replacement = $_FILES['replacement_file'];

        if ($replacement['error'] !== UPLOAD_ERR_OK) {
            showError(
                'Error',
                'There was a problem uploading the replacement file.',
                'window.history.back();'
            );
        }

        if ($replacement['size'] <= 0) {
            showError(
                'Error',
                'The replacement file is empty.',
                'window.history.back();'
            );
        }

        if ($replacement['size'] > $maxFileSize) {
            showError(
                'Error',
                'The replacement file is too large. Maximum allowed size is 25 MB.',
                'window.history.back();'
            );
        }

        $replacementOriginalName = basename($replacement['name']);
        $replacementExtension = getFileExtension($replacementOriginalName);

        if (!in_array($replacementExtension, $allowedExtensions, true)) {
            showError(
                'Error',
                'This replacement file type is not allowed.',
                'window.history.back();'
            );
        }

        $newStoredFileName = bin2hex(random_bytes(16)) . '.' . $replacementExtension;
        $newPhysicalFile = $uploadDir . $newStoredFileName;

        if (!move_uploaded_file(
            $replacement['tmp_name'],
            $newPhysicalFile
        )) {
            showError(
                'Error',
                'Failed to save the replacement file.',
                'window.history.back();'
            );
        }

        $newOriginalFileName = $replacementOriginalName;
        $newFilePath = 'uploads/' . $newStoredFileName;
        $newFileType = $replacement['type'] ?? 'application/octet-stream';
        $newFileSize = (int) $replacement['size'];
    }

    /*
    |--------------------------------------------------------------------------
    | Update Database Record
    |--------------------------------------------------------------------------
    */

    $updateStmt = $conn->prepare("
        UPDATE lms_materials
        SET
            title = ?,
            description = ?,
            original_file_name = ?,
            stored_file_name = ?,
            file_path = ?,
            file_type = ?,
            file_size = ?
        WHERE material_id = ?
          AND uploaded_by = ?
    ");

    $updateStmt->bind_param(
        "ssssssiii",
        $title,
        $description,
        $newOriginalFileName,
        $newStoredFileName,
        $newFilePath,
        $newFileType,
        $newFileSize,
        $material_id,
        $professor_id
    );

    if (!$updateStmt->execute()) {
        $updateStmt->close();

        if (
            $newPhysicalFile !== null &&
            file_exists($newPhysicalFile)
        ) {
            unlink($newPhysicalFile);
        }

        showError(
            'Error',
            'The material could not be updated.',
            'window.history.back();'
        );
    }

    $updateStmt->close();

    /*
    |--------------------------------------------------------------------------
    | Remove Old Physical File
    |--------------------------------------------------------------------------
    */

    if (
        $newPhysicalFile !== null &&
        $oldPhysicalFile !== $newPhysicalFile &&
        file_exists($oldPhysicalFile)
    ) {
        unlink($oldPhysicalFile);
    }

    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    showSuccess(
        'Success!',
        'Learning material uploaded successfully.',
        'window.history.back();'
    );
}

/*
|--------------------------------------------------------------------------
| Delete Material
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_material'])) {
    $material_id = (int) ($_POST['material_id'] ?? 0);

    if ($material_id <= 0) {
        showError(
            'Error',
            'Invalid material.',
            'window.history.back();'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Find Material
    |--------------------------------------------------------------------------
    */

    $deleteLookup = $conn->prepare("
        SELECT
            lm.material_id,
            lm.stored_file_name
        FROM lms_materials lm
        INNER JOIN lms_classes lc
            ON lc.lms_class_id = lm.lms_class_id
        INNER JOIN class_schedules cs
            ON cs.schedule_id = lc.schedule_id
        WHERE lm.material_id = ?
          AND lm.uploaded_by = ?
          AND cs.professor_id = ?
        LIMIT 1
    ");

    $deleteLookup->bind_param(
        "iii",
        $material_id,
        $professor_id,
        $professor_id
    );

    $deleteLookup->execute();

    $deleteResult = $deleteLookup->get_result();

    if ($deleteResult->num_rows === 0) {
        $deleteLookup->close();

        showError(
            'Error',
            'Material not found or you are not authorized to delete it.',
            'window.history.back();'
        );
    }

    $materialToDelete = $deleteResult->fetch_assoc();

    $deleteLookup->close();

    $physicalFile = $uploadDir . $materialToDelete['stored_file_name'];

    /*
    |--------------------------------------------------------------------------
    | Delete Material Database Record
    |--------------------------------------------------------------------------
    */

    $deleteStmt = $conn->prepare("
        DELETE FROM lms_materials
        WHERE material_id = ?
          AND uploaded_by = ?
    ");

    $deleteStmt->bind_param(
        "ii",
        $material_id,
        $professor_id
    );

    if (!$deleteStmt->execute()) {
        $deleteStmt->close();

        showError(
            'Error',
            'The material could not be deleted.',
            'window.history.back();'
        );
    }

    $deleteStmt->close();

    /*
    |--------------------------------------------------------------------------
    | Remove Student Notifications
    |--------------------------------------------------------------------------
    |
    | The notification belongs to this material through reference_id.
    | Once the professor deletes the material, its old notification
    | should also disappear.
    |
    */

    $notificationDeleteStmt = $conn->prepare("
        DELETE FROM lms_notifications
        WHERE notification_type = 'Material'
          AND reference_id = ?
    ");

    if ($notificationDeleteStmt) {
        $notificationDeleteStmt->bind_param(
            "i",
            $material_id
        );

        $notificationDeleteStmt->execute();
        $notificationDeleteStmt->close();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Physical File
    |--------------------------------------------------------------------------
    */

    if (file_exists($physicalFile)) {
        unlink($physicalFile);
    }

    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    showSuccess(
        'Success!',
        'Learning material deleted successfully.',
        'window.location.href = "/enrollment_system/views/professor/professor-upload";'
    );
}