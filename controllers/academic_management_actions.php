<?php
require_once "../config/db.php";
require_once '../views/includes/auth.php';
requireRole('Scheduler', 'Super Admin');
include '../views/includes/helpers.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$action = $_POST['action'] ?? '';

if ($action === 'create_section') {

    $strand_id   = (int) ($_POST['strand_id'] ?? 0);
    $grade_level = trim($_POST['grade_level'] ?? '');
    $section_name = trim($_POST['section_name'] ?? '');
    $capacity    = (int) ($_POST['capacity'] ?? 40);
    
    $validGrades = ['Grade 11', 'Grade 12'];

    if ($strand_id <= 0) {
        // die("Please select a valid strand.");
        showError(
        "Invalid Strand!",
        "Please select a valid strand before creating the section."
    );
    }

    if (!in_array($grade_level, $validGrades, true)) {
        // die("Please select a valid grade level.");
        showError(
        "Invalid Grade Level!",
        "Please select a valid grade level from the available options."
    );
    }

    if (empty($section_name)) {
        // die("Section name is required.");
        showError(
        "Section Name Required!",
        "Please enter a section name before proceeding with the registration."
    );
    }

    if (mb_strlen($section_name) > 50) {
        // die("Section name is too long (max 50 characters).");
        showError(
        "Section Name Too Long!",
        "The section name must not exceed 50 characters. Please enter a shorter and more appropriate section name."
    );
    }

    if ($capacity <= 0 || $capacity > 100) {
        // die("Capacity must be between 1 and 100.");
        showError(
        "Invalid Capacity!",
        "Section capacity must be a whole number between 1 and 100. Please enter a valid capacity and try again."
    );
    }

    $stmt = $conn->prepare("SELECT strand_id FROM strands WHERE strand_id = ?");
    $stmt->bind_param("i", $strand_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        // die("Selected strand does not exist.");
        showError(
        "Invalid Strand!",
        "The selected strand could not be found in the system. Please choose a valid strand and try again."
    );
    }
    $stmt->close();

    $syResult = $conn->query("SELECT school_year_id FROM school_years WHERE is_active = 1 LIMIT 1");
    
    if (!$syResult || $syResult->num_rows === 0) {
        // die("No active school year is set. Set one before creating sections.");
        showError(
        "No Active School Year!",
        "There is currently no active school year set in the system. Please activate a school year before creating a new section."
    );
    }

    $school_year_id = $syResult->fetch_assoc()['school_year_id'];

    $stmt = $conn->prepare("
        SELECT section_id FROM sections
        WHERE strand_id = ? AND grade_level = ? AND school_year_id = ? AND section_name = ?
    ");
    $stmt->bind_param("isis", $strand_id, $grade_level, $school_year_id, $section_name);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        // die("A section with this name already exists for this strand, grade level, and school year.");
        showError(
        "Section Already Exists!",
        "A section with the same name already exists for the selected strand, grade level, and school year. Please use a different section name."
    );

    }
    $stmt->close();

    $roomStmt = $conn->prepare("
        SELECT room_id
        FROM rooms
        WHERE room_id NOT IN (
            SELECT room_id FROM sections
            WHERE school_year_id = ? AND room_id IS NOT NULL
        )
        ORDER BY room_id ASC
        LIMIT 1
    ");
    $roomStmt->bind_param("i", $school_year_id);
    $roomStmt->execute();
    $roomResult = $roomStmt->get_result();

    if ($roomResult->num_rows === 0) {
        showError("No Rooms Available!", "Every room is already assigned to a section for this school year. Please add another room before creating additional sections.");
        exit;
    }

    $assignedRoomId = $roomResult->fetch_assoc()['room_id'];
    $roomStmt->close();

    $conn->begin_transaction();

    try {
         $stmt = $conn->prepare(
            "INSERT INTO sections (strand_id, grade_level, school_year_id, section_name, capacity, room_id)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("isisii", $strand_id, $grade_level, $school_year_id, $section_name, $capacity, $assignedRoomId);
        $stmt->execute();
        $section_id = $conn->insert_id;
        $stmt->close();

        $curStmt = $conn->prepare(
            "SELECT curriculum_id FROM curriculum WHERE strand_id = ? AND grade_level = ?"
        );
        $curStmt->bind_param("is", $strand_id, $grade_level);
        $curStmt->execute();
        $curRows = $curStmt->get_result();

        if ($curRows->num_rows === 0) {
            // throw new Exception("No curriculum entries found for this strand/grade level. Add subjects to the curriculum first.");
            showError(
            "Error!",
            "No curriculum entries found for this strand/grade level. Add subjects to the curriculum first."
    );
        }

        $schedStmt = $conn->prepare(
            "INSERT INTO class_schedules (curriculum_id, section_id, room_id) VALUES (?, ?, ?)"
        );
        while ($row = $curRows->fetch_assoc()) {
            $curriculum_id = $row['curriculum_id'];
            $schedStmt->bind_param("iii", $curriculum_id, $section_id, $assignedRoomId);
            $schedStmt->execute();
        }
        $schedStmt->close();
        $curStmt->close();

        
        $conn->commit();
            showSuccess(
            "Section Created!",
            "Created Succesfully!",
            "window.location.href = '../views/scheduler/academic-management.php';"
        );
        // header("Location: ../views/scheduler/academic-management.php");

    } catch (Exception $e) {
        $conn->rollback();
            die("Failed to create section: " . htmlspecialchars($e->getMessage()));
    }
}

if ($action === 'delete_section') {

    $section_id = (int) $_POST['section_id'];

    if ($section_id <= 0) {
        // die("No section selected to delete.");
        showError(
            "Error!",
            "No section selected to delete.",
        );
    }

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

        showError(
            "Section Not Found!",
            "The selected section does not exist or is not part of the active school year."
        );
    }

    $stmt->close();

    $conn->begin_transaction();

    try {

        $stmt = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM enrollments
            WHERE section_id = ?
        ");

        $stmt->bind_param("i", $section_id);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();

        if ($count > 0) {
            // throw new Exception("This section already has enrolled students.");
            showError(
                "Error.",
                "This section already has enrolled students.",
            );
        }

        $stmt = $conn->prepare("
            DELETE
            FROM class_schedules
            WHERE section_id = ?
        ");

        $stmt->bind_param("i", $section_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("
            DELETE
            FROM sections
            WHERE section_id = ?
        ");

        $stmt->bind_param("i", $section_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        
        // renderSwalConfirm(
        // 'warning',
        // 'Delete?',
        // 'Are you sure you want to delete?',
        // 'window.history.back();',
        // 'window.history.back();'
        // );

        showSuccess(
            "Deleted.",
            "Deleted Succesfully!",
            "window.location.href = '../views/scheduler/academic-management.php';"
        );
        
        // header("Location: ../views/scheduler/academic-management.php?deleted=1");
        exit();

    } catch (Exception $e) {

        $conn->rollback();

        die($e->getMessage());

    }

}

if ($action === 'set_schedule') {

    $schedule_id  = (int) ($_POST['schedule_id'] ?? 0);
    $section_id   = (int) ($_POST['section_id'] ?? 0);
    $semester     = trim($_POST['semester'] ?? '');
    $professor_id = (int) ($_POST['professor_id'] ?? 0);
    $start_time   = $_POST['start_time'] ?? '';
    $end_time     = $_POST['end_time'] ?? '';

    $validSemesters = ['1st Semester', '2nd Semester'];
    $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    if ($schedule_id <= 0 || $section_id <= 0) {
        // die("Invalid schedule or section reference.");
        showError(
        "Invalid Reference!",
        "The selected schedule or section reference is invalid. Please refresh the page and try again."
    );
    }

    if (!in_array($semester, $validSemesters, true)) {
        // die("Invalid semester.");
        showError(
        "Invalid Semester!",
        "Please select a valid semester from the available options before continuing."
    );
    }

    if ($professor_id <= 0) {
        // die("Please select a professor.");
        showError(
        "Professor Required!",
        "Please select a professor to assign to this class schedule."
    );
    }


    $timePattern = '/^([01]\d|2[0-3]):([0-5]\d)(:([0-5]\d))?$/';
    
    if (!preg_match($timePattern, $start_time) || !preg_match($timePattern, $end_time)) {
        // die("Start and end time must be valid times.");
        showError(
        "Invalid Time Format!",
        "Please enter valid start and end times using the correct time format."
    );
    }

    if (strtotime($start_time) >= strtotime($end_time)) {
        // die("Start time must be before end time.");
        showError(
        "Invalid Time Range!",
        "The start time must be earlier than the end time. Please adjust the schedule and try again."
    );
    }

    $daysChecked = $_POST['day_of_week'] ?? [];
    if (!is_array($daysChecked)) {
        $daysChecked = [$daysChecked];
    }
    $daysChecked = array_values(array_intersect($validDays, $daysChecked));

    if (empty($daysChecked)) {
        // die("Please select at least one day.");
        showError(
        "No Day Selected!",
        "Please select at least one day for the class schedule before proceeding."
    );
    }

    $day_of_week = implode(',', $daysChecked);

    if (mb_strlen($day_of_week) > 100) {
        // die("Too many days selected.");
        showError(
        "Too Many Days!",
        "Too many days have been selected for this schedule. Please review your selection and try again."
    );
    }

    $stmt = $conn->prepare("SELECT professor_id FROM professors WHERE professor_id = ?");
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        // die("Selected professor does not exist.");
        showError(
        "Professor Not Found!",
        "The selected professor could not be found in the system. Please choose a valid professor and try again."
    );
    }
    $stmt->close();

    $stmt = $conn->prepare("
        SELECT cs.schedule_id
        FROM class_schedules cs
        JOIN curriculum c
            ON cs.curriculum_id = c.curriculum_id
        JOIN sections sec
            ON cs.section_id = sec.section_id
        JOIN school_years sy
            ON sec.school_year_id = sy.school_year_id
        WHERE cs.schedule_id = ?
        AND cs.section_id = ?
        AND c.semester = ?
        AND sy.is_active = 1
    ");
    $stmt->bind_param("iis", $schedule_id, $section_id, $semester);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();

        showError(
            "Invalid Schedule Reference!",
            "The selected schedule does not belong to the selected section or is not part of the active school year."
        );
    }

    $stmt->close();
    
    $stmt = $conn->prepare("
        SELECT schedule_id, day_of_week FROM class_schedules
        WHERE schedule_id != ?
          AND (professor_id = ? OR room_id = ?)
          AND start_time < ? AND end_time > ?
    ");
    $stmt->bind_param("iiiss", $schedule_id, $professor_id, $section_id, $end_time, $start_time);
    $stmt->execute();
    $candidates = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($candidates as $row) {
        $existingDays = explode(',', $row['day_of_week']);
        $overlapDays = array_intersect($daysChecked, $existingDays);
        if (!empty($overlapDays)) {
            showError("Scheduling Conflict!", "This professor or section already has an overlapping class on " . implode(', ', $overlapDays) . ".");
            exit;
        }
    }

    $stmt = $conn->prepare(
        "UPDATE class_schedules
         SET professor_id = ?, day_of_week = ?, start_time = ?, end_time = ?
         WHERE schedule_id = ?"
    );
    $stmt->bind_param("isssi", $professor_id, $day_of_week, $start_time, $end_time, $schedule_id);
    $stmt->execute();
    $stmt->close();

    showSuccess(
            "Schedule Created!",
            "The schedule has been created!",
            "window.location.href = '../views/scheduler/academic-management.php';"
        );  
    
    header("Location: ../views/scheduler/academic-management?section_id=" . $section_id . "&semester=" . urlencode($semester));
    exit;
}

header("Location: ../views/scheduler/academic-management");
exit;