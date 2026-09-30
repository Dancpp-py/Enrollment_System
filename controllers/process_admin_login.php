<?php
require_once "../config/db.php";
require '../views/includes/helpers.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/admin/admin-login");
    exit;
}

function fail($message) {
    header("Location: ../views/admin/admin-login?error=" . urlencode($message));
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    showError(
            "Incomplete Login!",
            "Make sure all required fields are completed before signing in."
        );
}

$stmt = $conn->prepare("
    SELECT admin_id, username, password, role, is_active
    FROM admins
    WHERE username = ?
    LIMIT 1
");

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    showError(
            "Login Failed!",
            "Please check your credentials and try again."
        );
}

$admin = $result->fetch_assoc();
$stmt->close();

if (!password_verify($password, $admin['password'])) {
    showError(
            "Login Failed!",
            "Please verify your login details and try again."
        );
}

if ((int)$admin['is_active'] === 0) {
    showError(
            "Account Deactivated!",
            "This admin account has been deactivated. Please contact a Super Admin if you believe this is a mistake."
        );
}

session_regenerate_id(true);

$_SESSION['admin_id']       = $admin['admin_id'];
$_SESSION['admin_username'] = $admin['username'];
$_SESSION['admin_role']     = $admin['role'];


switch ($admin['role']) {
    case 'Registrar':
        header("Location: ../views/admin/admin-exam-scoring");
        break;
    case 'Admissions':
        header("Location: ../views/admin/admin-dashboard");
        break;
    case 'Scheduler':
        header("Location: ../views/scheduler/academic-management");
        break;
    default:
        header("Location: ../views/admin/admin-dashboard-summary");
        break;
}
exit;