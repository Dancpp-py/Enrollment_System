<?php

session_start();

require_once "../config/db.php";
require_once "../views/includes/auth.php";

requireRole('Scheduler', 'Super Admin');

$section_id = (int) ($_POST['section_id'] ?? 0);
$semester = trim($_POST['semester'] ?? '1st Semester');

$validSemesters = ['1st Semester', '2nd Semester'];

if ($section_id <= 0) {
    $_SESSION['academic_error'] = 'Invalid section selected.';
    header("Location: ../views/scheduler/academic-management");
    exit;
}

if (!in_array($semester, $validSemesters, true)) {
    $_SESSION['academic_error'] = 'Invalid semester selected.';
    header("Location: ../views/scheduler/academic-management");
    exit;
}

/*
 * Only allow sections from the active school year.
 */
$stmt = $conn->prepare("
    SELECT sec.section_id
    FROM sections sec
    JOIN school_years sy
        ON sec.school_year_id = sy.school_year_id
    WHERE sec.section_id = ?
      AND sy.is_active = 1
");
$stmt->bind_param("i", $section_id);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    $stmt->close();

    $_SESSION['academic_error'] = 'The selected section is not available.';
    header("Location: ../views/scheduler/academic-management");
    exit;
}

$stmt->close();

/*
 * Store the selected section and semester server-side.
 */
$_SESSION['academic_section_id'] = $section_id;
$_SESSION['academic_semester'] = $semester;

header("Location: ../views/scheduler/academic-management");
exit;