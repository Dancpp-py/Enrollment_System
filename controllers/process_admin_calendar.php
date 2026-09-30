<?php

require_once "../config/db.php";
require_once '../views/includes/auth.php';

requireRole('Scheduler', 'Super Admin');

include '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$action = $_POST['action'] ?? '';

if ($action === 'create_event') {
    $title = trim($_POST['title'] ?? '');
    $event_type = trim($_POST['event_type'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $start_time = trim($_POST['start_time'] ?? '');
    $end_time = trim($_POST['end_time'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $valid_event_types = [
        'Exam',
        'School Event',
        'Deadline',
        'Holiday',
        'Other'
    ];

    if ($title === '') {
        showError(
            "Invalid Event Title!",
            "Please enter an event title."
        );
        exit;
    }

    if (mb_strlen($title) > 200) {
        showError(
            "Title Too Long!",
            "The event title must not exceed 200 characters."
        );
        exit;
    }

    if (!in_array($event_type, $valid_event_types, true)) {
        showError(
            "Invalid Event Type!",
            "The selected event type is not valid."
        );
        exit;
    }

    if ($event_date === '') {
        showError(
            "Invalid Event Date!",
            "Please select an event date."
        );
        exit;
    }

    $date = DateTime::createFromFormat('Y-m-d', $event_date);

    if (!$date || $date->format('Y-m-d') !== $event_date) {
        showError(
            "Invalid Event Date!",
            "Please provide a valid calendar date."
        );
        exit;
    }

    if (mb_strlen($description) > 5000) {
        showError(
            "Description Too Long!",
            "The event description is too long."
        );
        exit;
    }

    if ($start_time !== '') {
        $start = DateTime::createFromFormat('H:i', $start_time);

        if (!$start || $start->format('H:i') !== $start_time) {
            showError(
                "Invalid Start Time!",
                "Please provide a valid start time."
            );
            exit;
        }
    } else {
        $start_time = null;
    }

    if ($end_time !== '') {
        $end = DateTime::createFromFormat('H:i', $end_time);

        if (!$end || $end->format('H:i') !== $end_time) {
            showError(
                "Invalid End Time!",
                "Please provide a valid end time."
            );
            exit;
        }
    } else {
        $end_time = null;
    }

    if ($start_time !== null && $end_time !== null) {
        if ($start_time >= $end_time) {
            showError(
                "Invalid Time Range!",
                "The end time must be later than the start time."
            );
            exit;
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO school_calendar (
            title,
            event_type,
            event_date,
            start_time,
            end_time,
            description
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssss",
        $title,
        $event_type,
        $event_date,
        $start_time,
        $end_time,
        $description
    );

    $stmt->execute();
    $stmt->close();

    showSuccess(
        "Event Added!",
        "The calendar event has been added successfully.",
        "window.location.href = '../views/admin/admin-calendar?saved=1';"
    );
    exit;
}

if ($action === 'update_event') {
    $calendar_id = (int) ($_POST['calendar_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $event_type = trim($_POST['event_type'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $start_time = trim($_POST['start_time'] ?? '');
    $end_time = trim($_POST['end_time'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $valid_event_types = [
        'Exam',
        'School Event',
        'Deadline',
        'Holiday',
        'Other'
    ];

    if ($calendar_id <= 0) {
        showError(
            "Invalid Request!",
            "No calendar event was specified."
        );
        exit;
    }

    $stmt = $conn->prepare("
        SELECT calendar_id
        FROM school_calendar
        WHERE calendar_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $calendar_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();

        showError(
            "Event Not Found!",
            "The selected calendar event could not be found."
        );
        exit;
    }

    $stmt->close();

    if ($title === '') {
        showError(
            "Invalid Event Title!",
            "Please enter an event title."
        );
        exit;
    }

    if (mb_strlen($title) > 200) {
        showError(
            "Title Too Long!",
            "The event title must not exceed 200 characters."
        );
        exit;
    }

    if (!in_array($event_type, $valid_event_types, true)) {
        showError(
            "Invalid Event Type!",
            "The selected event type is not valid."
        );
        exit;
    }

    if ($event_date === '') {
        showError(
            "Invalid Event Date!",
            "Please select an event date."
        );
        exit;
    }

    $date = DateTime::createFromFormat('Y-m-d', $event_date);

    if (!$date || $date->format('Y-m-d') !== $event_date) {
        showError(
            "Invalid Event Date!",
            "Please provide a valid calendar date."
        );
        exit;
    }

    if (mb_strlen($description) > 5000) {
        showError(
            "Description Too Long!",
            "The event description is too long."
        );
        exit;
    }

    if ($start_time !== '') {
        $start = DateTime::createFromFormat('H:i', $start_time);

        if (!$start || $start->format('H:i') !== $start_time) {
            showError(
                "Invalid Start Time!",
                "Please provide a valid start time."
            );
            exit;
        }
    } else {
        $start_time = null;
    }

    if ($end_time !== '') {
        $end = DateTime::createFromFormat('H:i', $end_time);

        if (!$end || $end->format('H:i') !== $end_time) {
            showError(
                "Invalid End Time!",
                "Please provide a valid end time."
            );
            exit;
        }
    } else {
        $end_time = null;
    }

    if ($start_time !== null && $end_time !== null) {
        if ($start_time >= $end_time) {
            showError(
                "Invalid Time Range!",
                "The end time must be later than the start time."
            );
            exit;
        }
    }

    $stmt = $conn->prepare("
        UPDATE school_calendar
        SET
            title = ?,
            event_type = ?,
            event_date = ?,
            start_time = ?,
            end_time = ?,
            description = ?
        WHERE calendar_id = ?
    ");

    $stmt->bind_param(
        "ssssssi",
        $title,
        $event_type,
        $event_date,
        $start_time,
        $end_time,
        $description,
        $calendar_id
    );

    $stmt->execute();
    $stmt->close();

    showSuccess(
        "Event Updated!",
        "The calendar event has been updated successfully.",
        "window.location.href = '../views/admin/admin-calendar?saved=1';"
    );
    exit;
}

if ($action === 'delete_event') {
    $calendar_id = (int) ($_POST['calendar_id'] ?? 0);

    if ($calendar_id <= 0) {
        showError(
            "Invalid Request!",
            "No calendar event was specified."
        );
        exit;
    }

    $stmt = $conn->prepare("
        SELECT calendar_id
        FROM school_calendar
        WHERE calendar_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $calendar_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();

        showError(
            "Event Not Found!",
            "The selected calendar event could not be found."
        );
        exit;
    }

    $stmt->close();

    $stmt = $conn->prepare("
        DELETE FROM school_calendar
        WHERE calendar_id = ?
    ");

    $stmt->bind_param("i", $calendar_id);
    $stmt->execute();
    $stmt->close();

    showSuccess(
        "Event Deleted!",
        "The calendar event has been removed successfully.",
        "window.location.href = '../views/admin/admin-calendar?deleted=1';"
    );
    exit;
}

header("Location: ../views/admin/admin-calendar");
exit;