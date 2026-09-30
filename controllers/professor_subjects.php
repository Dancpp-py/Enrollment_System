<?php

require_once "../config/db.php";
require_once "../views/includes/auth.php";

header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$professor_id = requireProfessorLoginJson();

function jsonResponse(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

$professor_id = (int) ($_SESSION['professor_id'] ?? 0);

if ($professor_id <= 0) {
    jsonResponse([
        'success' => false,
        'message' => 'Your session has expired. Please log in again.',
    ], 401);
}

$action = $_GET['action'] ?? '';

if ($action !== 'get_students') {
    jsonResponse([
        'success' => false,
        'message' => 'Unknown request.',
    ], 400);
}

$schedule_id = (int) ($_GET['schedule_id'] ?? 0);

if ($schedule_id <= 0) {
    jsonResponse([
        'success' => false,
        'message' => 'No class was specified.',
    ], 400);
}

try {

    $stmt = $conn->prepare("
        SELECT
            cs.schedule_id,
            cs.section_id,
            c.semester,
            sec.school_year_id,
            sec.section_name,
            sec.grade_level,
            st.strand_name,
            s.subject_code,
            s.subject_name
        FROM class_schedules cs
        JOIN curriculum c   ON c.curriculum_id = cs.curriculum_id
        JOIN subjects   s   ON s.subject_id    = c.subject_id
        JOIN sections   sec ON sec.section_id  = cs.section_id
        JOIN strands    st  ON st.strand_id    = sec.strand_id
        WHERE cs.schedule_id  = ?
          AND cs.professor_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $schedule_id, $professor_id);
    $stmt->execute();
    $class = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$class) {
        jsonResponse([
            'success' => false,
            'message' => 'This class could not be found, or it is not assigned to you.',
        ], 404);
    }

    // -----------------------------------------------------------
    // Students enrolled in that section, for the same semester and
    // school year the schedule belongs to.
    // -----------------------------------------------------------
    $stmt = $conn->prepare("
        SELECT
            stu.student_id,
            stu.student_number,
            stu.last_name,
            stu.first_name,
            stu.middle_name,
            stu.suffix,
            stu.gender,
            stu.contact_number,
            stu.email
        FROM enrollments e
        JOIN students stu ON stu.student_id = e.student_id
        WHERE e.section_id     = ?
          AND e.semester       = ?
          AND e.school_year_id = ?
          AND e.status         = 'Confirmed'
          AND e.stage          = 'Enrolled'
        ORDER BY stu.last_name, stu.first_name
    ");
    $stmt->bind_param(
        "isi",
        $class['section_id'],
        $class['semester'],
        $class['school_year_id']
    );
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $students = [];

    foreach ($rows as $row) {
        $fullName = $row['last_name'] . ', ' . $row['first_name'];

        if (!empty($row['middle_name'])) {
            $fullName .= ' ' . mb_substr($row['middle_name'], 0, 1) . '.';
        }

        if (!empty($row['suffix'])) {
            $fullName .= ' ' . $row['suffix'];
        }

        $students[] = [
            'student_number' => $row['student_number'] ?: 'Not yet assigned',
            'full_name'      => $fullName,
            'gender'         => $row['gender'],
            'contact_number' => $row['contact_number'],
            'email'          => $row['email'],
        ];
    }

    $sectionLabel = $class['strand_name'] . ' '
        . str_replace('Grade ', '', $class['grade_level']) . '-'
        . $class['section_name'];

    jsonResponse([
        'success'  => true,
        'subject'  => $class['subject_code'] . ' — ' . $class['subject_name'],
        'section'  => $sectionLabel . ' • ' . $class['semester'],
        'students' => $students,
    ]);

} catch (Exception $e) {
    jsonResponse([
        'success' => false,
        'message' => 'Unable to load the student list right now.',
    ], 500);
}