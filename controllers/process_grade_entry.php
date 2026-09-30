<?php

require_once "../config/db.php";
include '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../views/includes/auth.php';

$professor_id = currentProfessorId();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/professor/professor-grade-entry.php");
    exit();
}

// ---------------------------------------------------------------
// The schedule is taken from the server-side session.
// Do NOT trust a schedule_id submitted by the browser.
// ---------------------------------------------------------------

$schedule_id = (int) (
    $_SESSION['professor_grade_schedule_id'] ?? 0
);

$enrollment_id = (int) (
    $_POST['enrollment_id'] ?? 0
);

$midterm_raw = $_POST['midterm_grade'] ?? '';
$finals_raw = $_POST['finals_grade'] ?? '';

if ($schedule_id <= 0 || $enrollment_id <= 0) {
    showError(
        "Invalid Request!",
        "Missing student or class reference."
    );
    exit;
}

// ---------------------------------------------------------------
// Validate grade values.
// Both grades are individually optional, but at least one must
// be present. Anything provided must be numeric and between 0-100.
// ---------------------------------------------------------------

$validateGrade = function ($raw, string $label) {

    if ($raw === '' || $raw === null) {
        return null;
    }

    if (!is_numeric($raw)) {
        showError(
            "Invalid Grade!",
            "{$label} must be a number."
        );
        exit;
    }

    $value = (float) $raw;

    if ($value < 0 || $value > 100) {
        showError(
            "Invalid Grade!",
            "{$label} must be between 0 and 100."
        );
        exit;
    }

    return round($value, 2);
};

$midterm = $validateGrade(
    $midterm_raw,
    "First Quarter grade"
);

$finals = $validateGrade(
    $finals_raw,
    "Second Quarter grade"
);

if ($midterm === null && $finals === null) {
    showError(
        "No Grades Entered!",
        "Please enter at least one grade before submitting."
    );
    exit;
}

// ---------------------------------------------------------------
// Verify that the schedule belongs to the logged-in professor,
// is from the active school year, and determine its section and
// semester.
// ---------------------------------------------------------------

$stmt = $conn->prepare("
    SELECT
        cs.schedule_id,
        cs.professor_id,
        cs.section_id,
        c.semester,
        sec.school_year_id
    FROM class_schedules cs
    INNER JOIN curriculum c
        ON c.curriculum_id = cs.curriculum_id
    INNER JOIN sections sec
        ON sec.section_id = cs.section_id
    INNER JOIN school_years sy
        ON sy.school_year_id = sec.school_year_id
    WHERE cs.schedule_id = ?
      AND cs.professor_id = ?
      AND sy.is_active = 1
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $schedule_id,
    $professor_id
);

$stmt->execute();

$scheduleContext = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$scheduleContext) {
    showError(
        "Not Allowed!",
        "This class is not assigned to you or is no longer active."
    );
    exit;
}

// ---------------------------------------------------------------
// Verify that the student belongs to the SAME section as the
// professor's selected schedule.
// ---------------------------------------------------------------

$stmt = $conn->prepare("
    SELECT
        enrollment_id,
        section_id,
        school_year_id,
        status,
        stage
    FROM enrollments
    WHERE enrollment_id = ?
      AND section_id = ?
      AND school_year_id = ?
      AND status = 'Confirmed'
      AND stage = 'Enrolled'
    LIMIT 1
");

$stmt->bind_param(
    "iii",
    $enrollment_id,
    $scheduleContext['section_id'],
    $scheduleContext['school_year_id']
);

$stmt->execute();

$enrollmentContext = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$enrollmentContext) {
    showError(
        "Not Allowed!",
        "This student is not enrolled in the selected class."
    );
    exit;
}

// ---------------------------------------------------------------
// Grading period gate.
// A teacher can only encode while the matching quarter is open
// for this school year and semester.
// ---------------------------------------------------------------

$checkPeriodOpen = function (string $quarter) use ($conn, $scheduleContext) {

    $stmt = $conn->prepare("
        SELECT
            is_open,
            opens_at,
            closes_at
        FROM grading_periods
        WHERE school_year_id = ?
          AND semester = ?
          AND quarter = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        "iss",
        $scheduleContext['school_year_id'],
        $scheduleContext['semester'],
        $quarter
    );

    $stmt->execute();

    $period = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if (!$period) {
        return false;
    }

    $now = new DateTime();

    $opensAt = new DateTime($period['opens_at']);
    $closesAt = new DateTime($period['closes_at']);

    $withinWindow =
        $now >= $opensAt &&
        $now <= $closesAt;

    return
        ((int) $period['is_open'] === 1) &&
        $withinWindow;
};

if (
    $midterm !== null &&
    !$checkPeriodOpen('First Quarter')
) {
    showError(
        "Grading Period Closed!",
        "The First Quarter grading period isn't currently open."
    );
    exit;
}

if (
    $finals !== null &&
    !$checkPeriodOpen('Second Quarter')
) {
    showError(
        "Grading Period Closed!",
        "The Second Quarter grading period isn't currently open."
    );
    exit;
}

// ---------------------------------------------------------------
// Save grades.
// ---------------------------------------------------------------

$conn->begin_transaction();

try {

    // Pull existing grades so that a partial submission doesn't
    // erase the other quarter.

    $stmt = $conn->prepare("
        SELECT
            first_quarter,
            second_quarter
        FROM grades
        WHERE enrollment_id = ?
          AND schedule_id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        "ii",
        $enrollment_id,
        $schedule_id
    );

    $stmt->execute();

    $existing = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    $firstQuarter =
        $midterm !== null
            ? $midterm
            : ($existing['first_quarter'] ?? null);

    $secondQuarter =
        $finals !== null
            ? $finals
            : ($existing['second_quarter'] ?? null);

    // -----------------------------------------------------------
    // Calculate final grade only when both quarters exist.
    // -----------------------------------------------------------

    $finalGrade = null;
    $remarks = null;

    if (
        $firstQuarter !== null &&
        $secondQuarter !== null
    ) {
        $finalGrade = round(
            (
                (float) $firstQuarter +
                (float) $secondQuarter
            ) / 2,
            2
        );

        $remarks =
            $finalGrade >= 75
                ? 'Passed'
                : 'Failed';

    } else {
        $remarks = 'Incomplete';
    }

    // -----------------------------------------------------------
    // Insert or update the grade record.
    // -----------------------------------------------------------

    $stmt = $conn->prepare("
        INSERT INTO grades
            (
                enrollment_id,
                schedule_id,
                first_quarter,
                second_quarter,
                final_grade,
                remarks,
                encoded_by
            )
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            first_quarter = VALUES(first_quarter),
            second_quarter = VALUES(second_quarter),
            final_grade = VALUES(final_grade),
            remarks = VALUES(remarks),
            encoded_by = VALUES(encoded_by)
    ");

    $stmt->bind_param(
        "iiddssi",
        $enrollment_id,
        $schedule_id,
        $firstQuarter,
        $secondQuarter,
        $finalGrade,
        $remarks,
        $professor_id
    );

    $stmt->execute();

    $stmt->close();

    $conn->commit();

    showSuccess(
        "Grades Saved!",
        "The student grades have been recorded.",
        "window.location.href = '../views/professor/professor-grade-entry-v2';"
    );

    exit;

} catch (Exception $e) {

    $conn->rollback();

    showError(
        "Error!",
        "Failed to save the grades."
    );

    exit;
}