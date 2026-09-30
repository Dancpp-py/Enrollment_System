<?php
$selected_section_id = $_SESSION['academic_section_id'] ?? null;
$semester = $_SESSION['academic_semester'] ?? '1st Semester';

$validSemesters = ['1st Semester', '2nd Semester'];

if (!in_array($semester, $validSemesters, true)) {
    $semester = '1st Semester';
    $_SESSION['academic_semester'] = $semester;
}

$sectionsResult = $conn->query(
    "SELECT sec.section_id, sec.section_name, sec.grade_level, sec.capacity, st.strand_name
     FROM sections sec
     JOIN strands st ON sec.strand_id = st.strand_id
     JOIN school_years sy ON sec.school_year_id = sy.school_year_id
     WHERE sy.is_active = 1
     ORDER BY st.strand_name, sec.grade_level, sec.section_name"
);
$sections = $sectionsResult ? $sectionsResult->fetch_all(MYSQLI_ASSOC) : [];

if ($selected_section_id === null && count($sections) > 0) {
    $selected_section_id = $sections[0]['section_id'];
}

$section = null;
$scheduleRows = [];
if ($selected_section_id) {
    $stmt = $conn->prepare(
        "SELECT sec.section_id, sec.section_name, sec.grade_level, sec.strand_id,
                sec.capacity, st.strand_name
        FROM sections sec
        JOIN strands st ON sec.strand_id = st.strand_id
        JOIN school_years sy ON sec.school_year_id = sy.school_year_id
        WHERE sec.section_id = ?
        AND sy.is_active = 1"
    );
    $stmt->bind_param("i", $selected_section_id);
    $stmt->execute();
    $section = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($section) {
        $stmt = $conn->prepare(
            "SELECT cs.schedule_id, s.subject_code, s.subject_name, s.units,
                    cs.professor_id, cs.room_id, cs.day_of_week, cs.start_time, cs.end_time,
                    p.first_name AS prof_first, p.last_name AS prof_last, r.room_name
             FROM class_schedules cs
             JOIN curriculum c ON cs.curriculum_id = c.curriculum_id
             JOIN subjects s ON c.subject_id = s.subject_id
             LEFT JOIN professors p ON cs.professor_id = p.professor_id
             LEFT JOIN rooms r ON cs.room_id = r.room_id
             WHERE cs.section_id = ? AND c.semester = ?
             ORDER BY s.subject_name"
        );
        $stmt->bind_param("is", $selected_section_id, $semester);
        $stmt->execute();
        $scheduleRows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

$strands = $conn->query("SELECT strand_id, strand_name FROM strands ORDER BY strand_name")->fetch_all(MYSQLI_ASSOC);
$rooms = $conn->query("SELECT room_id, room_name FROM rooms ORDER BY room_name")->fetch_all(MYSQLI_ASSOC);
$professors = $conn->query("SELECT professor_id, first_name, last_name FROM professors ORDER BY last_name")->fetch_all(MYSQLI_ASSOC);

// function sem_link($section_id, $sem) {
//     return "academic-management.php?section_id=" . (int)$section_id . "&semester=" . urlencode($sem);
// }

function format_days($csv) {
    if (!$csv) return null;
    $abbrev = ['Monday'=>'Mon','Tuesday'=>'Tue','Wednesday'=>'Wed','Thursday'=>'Thu','Friday'=>'Fri','Saturday'=>'Sat'];
    $days = array_map(fn($d) => $abbrev[$d] ?? $d, explode(',', $csv));
    return implode(', ', $days);
}
?>