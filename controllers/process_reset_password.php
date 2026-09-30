<?php
require_once "../config/db.php";
require '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/admin/admin-login");
    exit();
}
 
function fail($token, $message) {
    header("Location: ../views/admin/reset-password?token=" . urlencode($token) . "&error=" . urlencode($message));
    exit();
}

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (empty($token)) {
    // header("Location: ../views/admin/forgot-password.php?error=" . urlencode("Missing reset token."));
    // exit();
    showError(
        "Error!",
        "Missing reset token."
    );
}
 
if (strlen($password) < 8) {
    // fail($token, "Password must be at least 8 characters long.");
    showError(
        "Error!",
        "Password must be at least 8 characters long.."
    );
}
 
if ($password !== $confirmPassword) {
    // fail($token, "Passwords do not match.");
    showError(
        "Error!",
        "Passwords do not match."
    );
}

$conn->begin_transaction();

try{
    $stmt = $conn->prepare("SELECT reset_id, admin_id, expires_at, used FROM admin_password_resets WHERE token = ? LIMIT 1 FOR UPDATE");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $reset = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$reset) {
        throw new Exception("Invalid reset link.");
    }
 
    if ((int)$reset['used'] === 1) {
        throw new Exception("This reset link has already been used.");
    }
 
    if (strtotime($reset['expires_at']) <= time()) {
        throw new Exception("This reset link has expired. Please request a new one.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
    $stmt->bind_param("si", $hashedPassword, $reset['admin_id']);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE admin_password_resets SET used = 1 WHERE reset_id = ?");
    $stmt->bind_param("i", $reset['reset_id']);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    header("Location: ../views/admin/admin-login?success=1");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    fail($token, $e->getMessage());
}
?>