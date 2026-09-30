<?php

require_once "../config/db.php";
require_once '../views/includes/auth.php';
requireRole('Scheduler', 'Super Admin');
include '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$action = $_POST['action'] ?? '';

function validateSubjectFields($subject_code, $subject_name, $units) {
    if (empty($subject_code) || mb_strlen($subject_code) > 20) {
        showError(
            "Invalid Subject Code!",
            "Subject code is required and must not exceed 20 characters."
        );
    }

    if (empty($subject_name) || mb_strlen($subject_name) > 100) {
        showError(
            "Invalid Subject Name!",
            "Subject name is required and must not exceed 100 characters."
        );
    }

    if ($units <= 0 || $units > 20) {
        showError(
            "Invalid Units!",
            "Units must be a whole number between 1 and 20."
        );
    }
}

if ($action === 'create_subject') {

    $subject_code = trim($_POST['subject_code'] ?? '');
    $subject_name = trim($_POST['subject_name'] ?? '');
    $units = (int) ($_POST['units'] ?? 0);

    validateSubjectFields($subject_code, $subject_name, $units);

    $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_code = ?");
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        showError(
            "Subject Code Already Exists!",
            "A subject with this code already exists in the subject bank."
        );
    }
    $stmt->close();

    $stmt = $conn->prepare("
        INSERT INTO subjects (subject_code, subject_name, units)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("ssi", $subject_code, $subject_name, $units);
    $stmt->execute();
    $stmt->close();

    showSuccess(
        "Subject Added!",
        "The subject has been added to the subject bank.",
        "window.location.href = '../views/scheduler/subject-bank.php';"
    );
}

if ($action === 'update_subject') {

    $subject_id = (int) ($_POST['subject_id'] ?? 0);
    $subject_code = trim($_POST['subject_code'] ?? '');
    $subject_name = trim($_POST['subject_name'] ?? '');
    $units = (int) ($_POST['units'] ?? 0);

    if ($subject_id <= 0) {
        showError(
            "Invalid Request!",
            "No subject was specified."
        );
    }

    validateSubjectFields($subject_code, $subject_name, $units);

    $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        showError(
            "Subject Not Found!",
            "The selected subject could not be found."
        );
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_code = ? AND subject_id != ?");
    $stmt->bind_param("si", $subject_code, $subject_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        showError(
            "Subject Code Already Exists!",
            "Another subject already uses this code."
        );
    }
    $stmt->close();

    $stmt = $conn->prepare("
        UPDATE subjects
        SET subject_code = ?, subject_name = ?, units = ?
        WHERE subject_id = ?
    ");
    $stmt->bind_param("ssii", $subject_code, $subject_name, $units, $subject_id);
    $stmt->execute();
    $stmt->close();

    showSuccess(
        "Subject Updated!",
        "The subject has been updated.",
        "window.location.href = '../views/scheduler/subject-bank.php';"
    );
}

if ($action === 'delete_subject') {

    $subject_id = (int) ($_POST['subject_id'] ?? 0);

    if ($subject_id <= 0) {
        showError(
            "Invalid Request!",
            "No subject was specified."
        );
    }

    $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        showError(
            "Subject Not Found!",
            "The selected subject could not be found."
        );
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM curriculum WHERE subject_id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $inUse = (int) $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    if ($inUse > 0) {
        showError(
            "Cannot Delete Subject!",
            "This subject is currently assigned to one or more strand/grade/semester curricula. Remove it from those first."
        );
    }

    $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $stmt->close();

    showSuccess(
        "Subject Deleted!",
        "The subject has been removed from the subject bank.",
        "window.location.href = '../views/scheduler/subject-bank.php';"
    );
}

header("Location: ../views/scheduler/subject-bank");
exit;