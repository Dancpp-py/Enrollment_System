<?php

require_once "../config/db.php";
require_once "../views/includes/helpers.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../student-teacher-login.php");
    exit;
}

function fail($token, $message) {
    header("Location: ../views/professor/professor-set-password?token=" . urlencode($token) . "&error=" . urlencode($message));
    exit;
}

$rawToken         = $_POST['token'] ?? '';
$password         = $_POST['password'] ?? '';
$passwordConfirm  = $_POST['password_confirm'] ?? '';

if (empty($rawToken)) {
    showError("Invalid Request", "This setup link is missing required information.");
    exit;
}

if (mb_strlen($password) < 8) {
    fail($rawToken, "Password must be at least 8 characters.");
}

if ($password !== $passwordConfirm) {
    fail($rawToken, "Passwords do not match.");
}

$hashedToken = hash('sha256', $rawToken);

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        SELECT reset_id, professor_id, expires_at, used
        FROM professor_password_resets
        WHERE token = ?
        FOR UPDATE
    ");
    $stmt->bind_param("s", $hashedToken);
    $stmt->execute();
    $reset = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$reset) {
        throw new Exception("Invalid setup link.");
    }

    if ((int) $reset['used'] === 1) {
        throw new Exception("This setup link has already been used.");
    }

    if (strtotime($reset['expires_at']) < time()) {
        throw new Exception("This setup link has expired.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE professors SET password = ? WHERE professor_id = ?");
    $stmt->bind_param("si", $hashedPassword, $reset['professor_id']);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE professor_password_resets SET used = 1 WHERE reset_id = ?");
    $stmt->bind_param("i", $reset['reset_id']);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    showSuccess(
        "Account Activated!",
        "Your password has been set. You can now log in.",
        "window.location.href = '../student-teacher-login';"
    );
    exit;

} catch (Exception $e) {
    $conn->rollback();
    fail($rawToken, $e->getMessage());
}