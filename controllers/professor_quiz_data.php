<?php

$professor_id = (int)($_SESSION['professor_id'] ?? 0);
$professorClasses = [];

if ($professor_id > 0) {
    $classesStmt = $conn->prepare("
        SELECT cs.schedule_id, sub.subject_name, sec.section_name
        FROM class_schedules cs
        INNER JOIN curriculum c ON c.curriculum_id = cs.curriculum_id
        INNER JOIN subjects sub ON sub.subject_id = c.subject_id
        INNER JOIN sections sec ON sec.section_id = cs.section_id
        INNER JOIN school_years sy ON sy.school_year_id = sec.school_year_id
        WHERE cs.professor_id = ? AND sy.is_active = 1
        ORDER BY sub.subject_name, sec.section_name
    ");

    $classesStmt->bind_param("i", $professor_id);
    $classesStmt->execute();
    $professorClasses = $classesStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $classesStmt->close();
}