<?php

require_once "../config/db.php";
require_once '../views/includes/auth.php';
requireRole('Registrar', 'Super Admin');
include '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$action = $_POST['action'] ?? '';

if ($action === 'create_teacher') {

    $first_name     = trim($_POST['first_name'] ?? '');
    $last_name      = trim($_POST['last_name'] ?? '');
    $middle_name    = trim($_POST['middle_name'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $email          = trim($_POST['email'] ?? '');

    if ($first_name === '' || $last_name === '') {
        showError("Missing Information!", "First name and last name are required.");
        exit;
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        showError("Invalid Email!", "Please provide a valid email address for the teacher.");
        exit;
    }

    $stmt = $conn->prepare("SELECT professor_id FROM professors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        showError("Email Already Used!", "A teacher account with this email already exists.");
        exit;
    }
    $stmt->close();

    // ---------------------------------------------------------------
    // Generate a unique username: firstname.lastname, lowercase, with
    // a numeric suffix (2, 3, ...) if that base is already taken.
    // ---------------------------------------------------------------
    $slugify = function (string $value): string {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '', $value);
        return $value;
    };

    $baseUsername = $slugify($first_name) . '.' . $slugify($last_name);
    $username     = $baseUsername;
    $suffix       = 2;

    $stmt = $conn->prepare("SELECT professor_id FROM professors WHERE username = ?");
    while (true) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            break;
        }
        $username = $baseUsername . $suffix;
        $suffix++;
    }
    $stmt->close();

    $conn->begin_transaction();

    try {
        // password stays NULL until the professor sets it via the emailed link
        $stmt = $conn->prepare("
            INSERT INTO professors
                (username, last_name, first_name, middle_name, contact_number, email, password, is_active)
            VALUES (?, ?, ?, ?, ?, ?, NULL, 1)
        ");
        $stmt->bind_param(
            "ssssss",
            $username,
            $last_name,
            $first_name,
            $middle_name,
            $contact_number,
            $email
        );
        $stmt->execute();
        $professorId = $conn->insert_id;
        $stmt->close();

        // ---------------------------------------------------------------
        // Generate a one-time setup token. The raw token goes in the
        // emailed link; only its SHA-256 hash is stored, so a DB leak
        // alone can't be used to set the professor's password.
        // ---------------------------------------------------------------
        $rawToken    = bin2hex(random_bytes(32)); // 64-char hex string, sent in the email link
        $hashedToken = hash('sha256', $rawToken);  // 64-char hex string, stored in DB
        $expiresAt   = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $conn->prepare("
            INSERT INTO professor_password_resets (professor_id, token, expires_at, used)
            VALUES (?, ?, ?, 0)
        ");
        $stmt->bind_param("iss", $professorId, $hashedToken, $expiresAt);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        $professorName = trim($first_name . ' ' . $last_name);

        require_once "send_professor_invite_email.php";
        sendProfessorInviteEmail($email, $professorName, $username, $rawToken);

        showSuccess(
            "Invite Sent!",
            "An account setup email has been sent to {$email}. The link expires in 24 hours.",
            "window.location.href = '../views/admin/professor-create-account';"
        );
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Failed to create professor account: " . $e->getMessage());
        showError("Error!", "Failed to create the teacher account and send the invite.");
        exit;
    }
}

header("Location: ../views/admin/admin-dashboard");
exit;