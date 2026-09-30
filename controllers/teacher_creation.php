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

    // Email is UNIQUE on professors — check up front for a clean error
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
        $value = preg_replace('/[^a-z0-9]+/', '', $value); // strip spaces, punctuation, accents-as-typed
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

    // ---------------------------------------------------------------
    // Generate a random temporary password (shown once to the admin below)
    // ---------------------------------------------------------------
    $temporaryPassword = bin2hex(random_bytes(4)); // 8-character hex string
    $hashedPassword    = password_hash($temporaryPassword, PASSWORD_DEFAULT);

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            INSERT INTO professors
                (username, last_name, first_name, middle_name, contact_number, email, password, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->bind_param(
            "sssssss",
            $username,
            $last_name,
            $first_name,
            $middle_name,
            $contact_number,
            $email,
            $hashedPassword
        );
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        // Credentials are shown once here — there's no email step yet for
        // teacher accounts (unlike the student flow), so the admin needs
        // to relay these to the teacher directly.
        showSuccess(
            "Teacher Account Created!",
            "Username: {$username} | Temporary Password: {$temporaryPassword} | Please share these credentials with the teacher securely — they will not be shown again.",
            "window.location.href = '../views/admin/admin-professor-view';"
        );

        exit;

    } catch (Exception $e) {
        $conn->rollback();
        showError("Error!", "Failed to create the teacher account.");
        exit;
    }
}

header("Location: ../views/admin/admin-professor-view");
exit;