<?php

require_once "../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['student_id'])) {
    http_response_code(401);

    echo json_encode([
        'success' => false,
        'message' => 'Student session expired.'
    ]);

    exit;
}

$student_id = (int) $_SESSION['student_id'];
$action = $_POST['action'] ?? '';

if ($action === 'mark_read') {
    $notification_id = (int) ($_POST['notification_id'] ?? 0);

    if ($notification_id <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid notification.'
        ]);

        exit;
    }

    $stmt = $conn->prepare("
        UPDATE lms_notifications
        SET is_read = 1
        WHERE notification_id = ?
          AND student_id = ?
          AND is_read = 0
    ");

    if (!$stmt) {
        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to prepare notification update.'
        ]);

        exit;
    }

    $stmt->bind_param(
        "ii",
        $notification_id,
        $student_id
    );

    if (!$stmt->execute()) {
        $stmt->close();

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to mark notification as read.'
        ]);

        exit;
    }

    $stmt->close();

    $countStmt = $conn->prepare("
        SELECT COUNT(*) AS unread_count
        FROM lms_notifications
        WHERE student_id = ?
          AND is_read = 0
    ");

    if (!$countStmt) {
        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to count unread notifications.'
        ]);

        exit;
    }

    $countStmt->bind_param(
        "i",
        $student_id
    );

    $countStmt->execute();

    $countResult = $countStmt->get_result();
    $countData = $countResult->fetch_assoc();

    $countStmt->close();

    echo json_encode([
        'success' => true,
        'unread_count' => (int) ($countData['unread_count'] ?? 0)
    ]);

    exit;
}

if ($action === 'mark_all_read') {
    $stmt = $conn->prepare("
        UPDATE lms_notifications
        SET is_read = 1
        WHERE student_id = ?
          AND is_read = 0
    ");

    if (!$stmt) {
        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to prepare notification update.'
        ]);

        exit;
    }

    $stmt->bind_param(
        "i",
        $student_id
    );

    if (!$stmt->execute()) {
        $stmt->close();

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to mark notifications as read.'
        ]);

        exit;
    }

    $stmt->close();

    echo json_encode([
        'success' => true,
        'unread_count' => 0
    ]);

    exit;
}

http_response_code(400);

echo json_encode([
    'success' => false,
    'message' => 'Invalid action.'
]);