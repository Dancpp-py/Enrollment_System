<?php
require_once "../config/db.php";
require_once '../views/includes/auth.php';
requireRole('Super Admin');
include '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/admin/admin-management");
    exit();
}

$target_admin_id = (int) ($_POST['admin_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($target_admin_id <= 0) {
    showError(
        "Invalid Request!",
        "No admin account was specified"
    );
}

if (!in_array($action, ['activate', 'deactivate'], true)) {
    showError(
        "Invalid Action!",
        "The requested action is not recognized"
    );
}

if ($action === 'deactivate' && $target_admin_id === (int) $_SESSION['admin_id']) {
    showError(
        "Not Allowed!",
        "You cannot deactivate your own account while logged in as it."
    );
}

$stmt = $conn->prepare("SELECT admin_id, username, role, is_active FROM admins WHERE admin_id = ?");
$stmt->bind_param("i", $target_admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    showError(
        "Admin Not Found!",
        "The selected admin account could not be found."
    );
}

$targetAdmin = $result->fetch_assoc();
$stmt->close();
 
$newStatus = $action === 'activate' ? 1 : 0;
 
if ((int)$targetAdmin['is_active'] === $newStatus) {
    showError(
        "No Change.",
        "This account is already " . ($newStatus ? "active" : "inactive") . "."
    );
}

if ($action === 'deactivate' && $targetAdmin['role'] === 'Super Admin') {
    $countResult = $conn->query("
        SELECT COUNT(*) AS total
        FROM admins
        WHERE role = 'Super Admin' AND is_active = 1
    ");
    $activeSuperAdmins = (int) $countResult->fetch_assoc()['total'];
 
    if ($activeSuperAdmins <= 1) {
        showError(
            "Not Allowed!",
            "This is the last active Super Admin account. Deactivating it would lock everyone out of admin management."
        );
    }
}

$stmt = $conn->prepare("UPDATE admins SET is_active = ? WHERE admin_id = ?");
$stmt->bind_param("ii", $newStatus, $target_admin_id);
$stmt->execute();
$stmt->close();
 
$_SESSION['success_title'] = $newStatus
    ? "Account Activated!"
    : "Account Deactivated!";

$_SESSION['success_message'] =
    $targetAdmin['username'] .
    "'s account has been " .
    ($newStatus ? "activated." : "deactivated.");

header("Location: ../views/admin/admin-management");
exit();
 
?>