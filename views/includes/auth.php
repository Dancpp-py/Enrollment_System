<?php

//admin
function requireLogin() {
    if (empty($_SESSION['admin_id'])) {
        header("Location: /enrollment_system/views/admin/admin-login");
        exit;
    }
}

function requireRole(...$allowedRoles) {
    requireLogin();

    if (!in_array($_SESSION['admin_role'], $allowedRoles, true)) {
        http_response_code(403);
        die("You do not have permission to perform this action.");
    }
}

//student
function requireStudentLogin() {
    if (empty($_SESSION['student_id'])) {
        header("Location: /enrollment_system/student-teacher-login");
        exit;
    }
}

//teacher
function requireProfessorLogin() {
    if (empty($_SESSION['professor_id'])) {
        header("Location: /enrollment_system/student-teacher-login");
        exit;
    }
}

function currentProfessorId() {
    requireProfessorLogin();

    return (int) $_SESSION['professor_id'];
}

function requireProfessorLoginJson() {
    if (empty($_SESSION['professor_id'])) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Your session has expired. Please log in again.',
        ]);
        exit;
    }

    return (int) $_SESSION['professor_id'];
}

?>