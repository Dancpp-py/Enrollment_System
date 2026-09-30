<?php
require_once "../config/db.php";
require '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/admin/forgot-password");
    exit();
}

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    showError(
            "Invalid Login!",
            "Please enter a valid email address."
        );
}


// $genericSuccess = "If that email is registered, a password reset link has been sent.";

$stmt = $conn->prepare("SELECT admin_id, username FROM admins WHERe email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: ../views/admin/forgot-password?success=" . urlencode($genericSuccess));
    exit();
}

$admin = $result->fetch_assoc();
$stmt->close();

$adminId = (int)$admin['admin_id'];

$conn->begin_transaction();

try {
    $stmt =$conn->prepare("UPDATE admin_password_resets SET used = 1 WHERE admin_id =? AND used = 0");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $stmt->close();

    $token = bin2hex(random_bytes(32));
    $expiresAt = date("Y-m-d H:i:s", time() + 30 * 60);

    $stmt = $conn->prepare("INSERT INTO admin_password_resets (admin_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $adminId, $token, $expiresAt);
    $stmt->execute();
    $stmt->close();
    
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $resetLink = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/enrollment_system/views/admin/reset-password.php?token=' . urlencode($token);

    require_once "send_password_reset_email.php";
    sendPasswordResetEmail($email, $admin['username'], $resetLink);

    $conn->commit();

    header("Location: ../views/admin/forgot-password?success=" . urlencode($genericSuccess));
    exit();
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../views/admin/forgot-password?error=" . urlencode("Something went wrong. Please try again."));
    exit();
}

?>