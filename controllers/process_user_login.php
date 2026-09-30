<?php

require_once "../config/db.php";


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../views/includes/helpers.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../student-teacher-login");
    exit();
}

$role     = $_POST['role'] ?? '';
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($role) || $username === '' || $password === '') {

    $_SESSION['error_title'] = "Incomplete Login!";
    $_SESSION['error_message'] = "Please fill in all fields.";

    header("Location: ../student-teacher-login");
    exit();
}

if ($role === 'Student') {

    $stmt = $conn->prepare("
        SELECT student_id, first_name, last_name, password
        FROM students
        WHERE student_number = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // empty($student['password']) covers students who don't have an account
    // yet (e.g. not officially enrolled, so no credentials were ever issued).
    if (!$student || empty($student['password']) || !password_verify($password, $student['password'])) {

        $_SESSION['error_title'] = "Login Failed!";
        $_SESSION['error_message'] = "Invalid student number or password.";

        header("Location: ../student-teacher-login");
        exit();
    }

    session_regenerate_id(true);

    $_SESSION['student_id']   = $student['student_id'];
    $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];

    $_SESSION['success_title'] = "Login Successful!";
    $_SESSION['success_message'] = "Welcome back!";

    header("Location: ../views/student/student-dashboard");
    exit();

} elseif ($role === 'Teacher') {
$stmt = $conn->prepare("
        SELECT professor_id, first_name, last_name, password, is_active
        FROM professors
        WHERE username = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $professor = $stmt->get_result()->fetch_assoc();
    $stmt->close();
 
    // empty($professor['password']) covers the seeded professor rows that
    // have no account credentials issued yet.
    if (!$professor || empty($professor['password']) || !password_verify($password, $professor['password'])) {
        $_SESSION['login_error'] = "Invalid username or password.";
        header("Location: ../student-teacher-login");
        exit();
    }
 
    if ((int) $professor['is_active'] !== 1) {
        $_SESSION['login_error'] = "This account has been deactivated. Please contact the registrar.";
        header("Location: ../student-teacher-login");
        exit();
    }
 
    $_SESSION['professor_id']   = $professor['professor_id'];
    $_SESSION['professor_name'] = $professor['first_name'] . ' ' . $professor['last_name'];
 
    header("Location: ../views/professor/professor-dashboard");
    exit();
} else {

    $_SESSION['error_title'] = "Invalid Role!";
    $_SESSION['error_message'] = "Invalid role selected.";

    header("Location: ../student-teacher-login");
    exit();
}