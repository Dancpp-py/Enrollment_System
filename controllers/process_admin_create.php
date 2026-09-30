<?php
require_once "../config/db.php";
require_once "../views/includes/auth.php";
include '../views/includes/helpers.php';
requireRole('Super Admin');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/admin/admin-create-account");
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

$validRoles = ['Registrar', 'Admissions', 'Scheduler'];

if (empty($username) || empty($password) || empty($role)) {
    // fail("All fields are required.");
    showError(
        "Incomplete Form",
        "Please complete all required fields before creating the administrator account."
    );
}

if (mb_strlen($username) > 50) {
    // fail("Username is too long.");
    showError(
        "Username Too Long",
        "The username must not exceed 50 characters. Please enter a shorter username and try again."
    );
}

if (mb_strlen($password) < 8) {
    // fail("Password must be at least 8 characters.");
    showError(
        "Weak Password",
        "The password must contain at least 8 characters. Please choose a stronger password and try again."
    );
}

if (!in_array($role, $validRoles, true)) {
    // fail("Invalid role selected.");
    showError(
        "Invalid Role",
        "Please select a valid administrator role from the available options before continuing."
    );
}

$stmt = $conn->prepare("SELECT admin_id FROM admins WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    // fail("That username is already taken.");
    showError(
    "Username Unavailable",
    "The username you entered is already in use. Please choose a different username and try again."
);
}
$stmt->close();

$stmt = $conn->prepare("SELECT admin_id FROM admins where email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    showError(
        "Email Unavailable",
        "That email address is already associated with another administrator account."
    );
    exit;
}
$stmt->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    INSERT INTO admins (username, email, role, password)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("ssss", $username, $email, $role, $hashedPassword);
$stmt->execute();
$stmt->close();

header("Location: ../views/admin/admin-management");
exit;