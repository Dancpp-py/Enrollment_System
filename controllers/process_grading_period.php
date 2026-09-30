<?php

require_once "../config/db.php";
require_once '../views/includes/auth.php';

// session_start();

requireRole('Registrar', 'Super Admin');
include '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$action = $_POST['action'] ?? '';

$VALID_SEMESTERS = ['1st Semester', '2nd Semester'];
$VALID_QUARTERS = ['First Quarter', 'Second Quarter'];

// ---------------------------------------------------------------
// SAVE (create or update) A GRADING PERIOD WINDOW
// ---------------------------------------------------------------
if ($action === 'save_period') {
    $school_year_id = (int) ($_SESSION['grading_period_school_year_id'] ?? 0);
    $semester = trim($_POST['semester'] ?? '');
    $quarter = trim($_POST['quarter'] ?? '');
    $opens_at = trim($_POST['opens_at'] ?? '');
    $closes_at = trim($_POST['closes_at'] ?? '');
    $is_open = isset($_POST['is_open']) ? 1 : 0;

    if ($school_year_id <= 0) {
        showError("Invalid School Year!", "Please select a valid school year.");
        exit;
    }

    if (!in_array($semester, $VALID_SEMESTERS, true)) {
        showError("Invalid Semester!", "Please select a valid semester.");
        exit;
    }

    if (!in_array($quarter, $VALID_QUARTERS, true)) {
        showError("Invalid Quarter!", "Please select a valid quarter.");
        exit;
    }

    if ($opens_at === '' || $closes_at === '') {
        showError("Dates Required!", "Please provide both an opening and a closing date.");
        exit;
    }

    // datetime-local submits YYYY-MM-DDTHH:MM
    $opensTimestamp = strtotime($opens_at);
    $closesTimestamp = strtotime($closes_at);

    if ($opensTimestamp === false || $closesTimestamp === false) {
        showError("Invalid Dates!", "Please provide valid opening and closing dates.");
        exit;
    }

    if ($opensTimestamp >= $closesTimestamp) {
        showError("Invalid Date Range!", "The opening date must be earlier than the closing date.");
        exit;
    }

    $opensFormatted = date('Y-m-d H:i:s', $opensTimestamp);
    $closesFormatted = date('Y-m-d H:i:s', $closesTimestamp);

    // Confirm the selected school year exists
    $stmt = $conn->prepare("
        SELECT school_year_id
        FROM school_years
        WHERE school_year_id = ?
    ");
    $stmt->bind_param("i", $school_year_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();
        showError("Invalid School Year!", "The selected school year could not be found.");
        exit;
    }

    $stmt->close();

    try {
        // One window per school year + semester + quarter.
        $stmt = $conn->prepare("
            INSERT INTO grading_periods
                (school_year_id, semester, quarter, opens_at, closes_at, is_open)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                opens_at = VALUES(opens_at),
                closes_at = VALUES(closes_at),
                is_open = VALUES(is_open)
        ");

        $stmt->bind_param(
            "issssi",
            $school_year_id,
            $semester,
            $quarter,
            $opensFormatted,
            $closesFormatted,
            $is_open
        );

        $stmt->execute();
        $stmt->close();

        showSuccess(
            "Grading Period Saved!",
            "The encoding window for {$quarter} ({$semester}) has been saved.",
            "window.location.href = '../views/admin/grading-periods.php';"
        );
        exit;

    } catch (Exception $e) {
        showError("Error!", "Failed to save the grading period.");
        exit;
    }
}

// ---------------------------------------------------------------
// TOGGLE A PERIOD OPEN/CLOSED WITHOUT TOUCHING ITS DATES
// ---------------------------------------------------------------
if ($action === 'toggle_period') {
    $grading_period_id = (int) ($_POST['grading_period_id'] ?? 0);
    $school_year_id = (int) ($_SESSION['grading_period_school_year_id'] ?? 0);

    if ($school_year_id <= 0) {
        showError("Invalid School Year!", "No school year is currently selected.");
        exit;
    }

    if ($grading_period_id <= 0) {
        showError("Invalid Request!", "No grading period was specified.");
        exit;
    }

    // Verify that this grading period belongs to the selected school year.
    $stmt = $conn->prepare("
        SELECT is_open
        FROM grading_periods
        WHERE grading_period_id = ?
        AND school_year_id = ?
    ");
    $stmt->bind_param("ii", $grading_period_id, $school_year_id);
    $stmt->execute();

    $period = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$period) {
        showError(
            "Invalid Grading Period!",
            "The selected grading period does not belong to the current school year."
        );
        exit;
    }

    $newState = ((int) $period['is_open'] === 1) ? 0 : 1;

    $stmt = $conn->prepare("
        UPDATE grading_periods
        SET is_open = ?
        WHERE grading_period_id = ?
        AND school_year_id = ?
    ");
    $stmt->bind_param("iii", $newState, $grading_period_id, $school_year_id);
    $stmt->execute();
    $stmt->close();

    showSuccess(
        $newState === 1 ? "Encoding Opened!" : "Encoding Closed!",
        $newState === 1
            ? "Teachers can now encode grades for this quarter, as long as the current date falls within its window."
            : "Teachers can no longer encode grades for this quarter.",
        "window.location.href = '../views/admin/grading-periods.php';"
    );
    exit;
}

// ---------------------------------------------------------------
// IF NO ACTION, REDIRECT TO GRADING PERIODS
// ---------------------------------------------------------------
header("Location: ../views/admin/grading-periods.php");
exit;