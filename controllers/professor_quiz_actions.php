<?php

require_once "../config/db.php";
require_once "../views/includes/auth.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

requireProfessorLogin();
require_once '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$professor_id = (int)($_SESSION['professor_id'] ?? 0);

if ($professor_id <= 0) {
    showError("Unauthorized!", "Your professor session could not be verified.");
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'save_quiz') {
    $quiz_id = (int)($_POST['quiz_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $schedule_id = (int)($_POST['lms_class_id'] ?? 0);
    $time_limit_raw = trim($_POST['time_limit'] ?? '');
    $isEdit = $quiz_id > 0;

    if ($title === '') {
        showError("Invalid Quiz Title!", "Please enter a title for the quiz.");
        exit;
    }

    if (mb_strlen($title) > 150) {
        showError("Quiz Title Too Long!", "The quiz title must not exceed 150 characters.");
        exit;
    }

    if ($schedule_id <= 0) {
        showError("Invalid Class!", "Please select a valid subject and section.");
        exit;
    }

    $time_limit = null;

    if ($time_limit_raw !== '') {
        if (!ctype_digit($time_limit_raw) || (int)$time_limit_raw < 1 || (int)$time_limit_raw > 300) {
            showError("Invalid Time Limit!", "The time limit must be between 1 and 300 minutes.");
            exit;
        }
        $time_limit = (int)$time_limit_raw;
    }

    $stmt = $conn->prepare("
        SELECT cs.schedule_id
        FROM class_schedules cs
        INNER JOIN sections sec ON sec.section_id = cs.section_id
        INNER JOIN school_years sy ON sy.school_year_id = sec.school_year_id
        WHERE cs.schedule_id = ?
          AND cs.professor_id = ?
          AND sy.is_active = 1
        LIMIT 1
    ");
    $stmt->bind_param("ii", $schedule_id, $professor_id);
    $stmt->execute();
    $scheduleExists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if (!$scheduleExists) {
        showError("Invalid Class Assignment!", "The selected subject/section is not assigned to you or is not part of the active school year.");
        exit;
    }

    if ($isEdit) {
        $stmt = $conn->prepare("
            SELECT quiz_id
            FROM quizzes
            WHERE quiz_id = ?
              AND professor_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("ii", $quiz_id, $professor_id);
        $stmt->execute();
        $quizExists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if (!$quizExists) {
            showError("Quiz Not Found!", "The selected quiz does not exist or does not belong to you.");
            exit;
        }
    }

    $questions = $_POST['question'] ?? [];
    $choicesA = $_POST['choice_a'] ?? [];
    $choicesB = $_POST['choice_b'] ?? [];
    $choicesC = $_POST['choice_c'] ?? [];
    $choicesD = $_POST['choice_d'] ?? [];
    $answers = $_POST['correct_answer'] ?? [];

    if (!is_array($questions) || !is_array($choicesA) || !is_array($choicesB) || !is_array($choicesC) || !is_array($choicesD) || !is_array($answers)) {
        showError("Invalid Question Data!", "The submitted question data is invalid.");
        exit;
    }

    $questionCount = count($questions);

    if ($questionCount < 1) {
        showError("No Questions!", "Please add at least one question to the quiz.");
        exit;
    }

    if (count($choicesA) !== $questionCount || count($choicesB) !== $questionCount || count($choicesC) !== $questionCount || count($choicesD) !== $questionCount || count($answers) !== $questionCount) {
        showError("Invalid Question Data!", "The question and answer choices do not match correctly.");
        exit;
    }

    $cleanQuestions = [];

    for ($i = 0; $i < $questionCount; $i++) {
        $questionText = trim((string)$questions[$i]);
        $choiceA = trim((string)$choicesA[$i]);
        $choiceB = trim((string)$choicesB[$i]);
        $choiceC = trim((string)$choicesC[$i]);
        $choiceD = trim((string)$choicesD[$i]);
        $correctAnswer = strtoupper(trim((string)$answers[$i]));

        if ($questionText === '') {
            showError("Invalid Question!", "Question " . ($i + 1) . " cannot be empty.");
            exit;
        }

        if ($choiceA === '' || $choiceB === '' || $choiceC === '' || $choiceD === '') {
            showError("Incomplete Choices!", "Question " . ($i + 1) . " must have all four choices.");
            exit;
        }

        if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'], true)) {
            showError("Invalid Correct Answer!", "Question " . ($i + 1) . " has an invalid correct-answer value.");
            exit;
        }

        $cleanQuestions[] = [
            'question_text' => $questionText,
            'choice_a' => $choiceA,
            'choice_b' => $choiceB,
            'choice_c' => $choiceC,
            'choice_d' => $choiceD,
            'correct_answer' => $correctAnswer
        ];
    }

    $conn->begin_transaction();

    try {
        if ($isEdit) {
            if ($time_limit === null) {
                $stmt = $conn->prepare("
                    UPDATE quizzes
                    SET title = ?,
                        schedule_id = ?,
                        time_limit = NULL
                    WHERE quiz_id = ?
                      AND professor_id = ?
                ");
                $stmt->bind_param("siii", $title, $schedule_id, $quiz_id, $professor_id);
            } else {
                $stmt = $conn->prepare("
                    UPDATE quizzes
                    SET title = ?,
                        schedule_id = ?,
                        time_limit = ?
                    WHERE quiz_id = ?
                      AND professor_id = ?
                ");
                $stmt->bind_param("siiii", $title, $schedule_id, $time_limit, $quiz_id, $professor_id);
            }

            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("
                DELETE FROM quiz_questions
                WHERE quiz_id = ?
            ");
            $stmt->bind_param("i", $quiz_id);
            $stmt->execute();
            $stmt->close();
        } else {
            if ($time_limit === null) {
                $stmt = $conn->prepare("
                    INSERT INTO quizzes (professor_id, schedule_id, title, time_limit, status)
                    VALUES (?, ?, ?, NULL, 'Draft')
                ");
                $stmt->bind_param("iis", $professor_id, $schedule_id, $title);
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO quizzes (professor_id, schedule_id, title, time_limit, status)
                    VALUES (?, ?, ?, ?, 'Draft')
                ");
                $stmt->bind_param("iisi", $professor_id, $schedule_id, $title, $time_limit);
            }

            $stmt->execute();
            $quiz_id = $stmt->insert_id;
            $stmt->close();
        }

        $questionStmt = $conn->prepare("
            INSERT INTO quiz_questions (quiz_id, question_text, choice_a, choice_b, choice_c, choice_d, correct_answer, question_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($cleanQuestions as $index => $question) {
            $questionOrder = $index + 1;
            $questionStmt->bind_param(
                "issssssi",
                $quiz_id,
                $question['question_text'],
                $question['choice_a'],
                $question['choice_b'],
                $question['choice_c'],
                $question['choice_d'],
                $question['correct_answer'],
                $questionOrder
            );
            $questionStmt->execute();
        }

        $questionStmt->close();
        $conn->commit();

        if ($isEdit) {
            unset($_SESSION['professor_quiz_id']);
        }

        showSuccess(
            $isEdit ? "Quiz Updated!" : "Quiz Created!",
            $isEdit ? "The quiz has been updated successfully." : "The quiz has been created successfully as a Draft.",
            "window.location.href = '../views/professor/professor-quiz';"
        );
        exit;

    } catch (Throwable $e) {
        $conn->rollback();
        showError("Save Failed!", "The quiz could not be saved. Please try again.");
        exit;
    }
}

if ($action === 'delete_quiz') {
    $quiz_id = (int)($_POST['quiz_id'] ?? 0);

    if ($quiz_id <= 0) {
        showError("Invalid Request!", "No valid quiz was specified.");
        exit;
    }

    $stmt = $conn->prepare("
        SELECT quiz_id
        FROM quizzes
        WHERE quiz_id = ?
          AND professor_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $quiz_id, $professor_id);
    $stmt->execute();
    $quizExists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if (!$quizExists) {
        showError("Quiz Not Found!", "The selected quiz does not exist or does not belong to you.");
        exit;
    }

    $stmt = $conn->prepare("
        DELETE FROM lms_notifications
        WHERE notification_type = 'Quiz'
          AND reference_id = ?
    ");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("
        DELETE FROM quizzes
        WHERE quiz_id = ?
          AND professor_id = ?
    ");
    $stmt->bind_param("ii", $quiz_id, $professor_id);
    $stmt->execute();
    $deleted = $stmt->affected_rows > 0;
    $stmt->close();

    if (!$deleted) {
        showError("Delete Failed!", "The quiz could not be deleted.");
        exit;
    }

    if ((int)($_SESSION['professor_quiz_id'] ?? 0) === $quiz_id) {
        unset($_SESSION['professor_quiz_id']);
    }

    showSuccess(
        "Quiz Deleted!",
        "The quiz has been deleted successfully.",
        "window.location.href = '../views/professor/professor-quiz';"
    );
    exit;
}

if ($action === 'toggle_status') {
    $quiz_id = (int)($_POST['quiz_id'] ?? 0);

    if ($quiz_id <= 0) {
        showError("Invalid Request!", "No valid quiz was specified.");
        exit;
    }

    $stmt = $conn->prepare("
        SELECT quiz_id, schedule_id, title, status
        FROM quizzes
        WHERE quiz_id = ?
          AND professor_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $quiz_id, $professor_id);
    $stmt->execute();
    $quiz = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$quiz) {
        showError("Quiz Not Found!", "The selected quiz does not exist or does not belong to you.");
        exit;
    }

    if (!in_array($quiz['status'], ['Draft', 'Published'], true)) {
        showError("Invalid Quiz Status!", "The quiz currently has an invalid status.");
        exit;
    }

    $newStatus = $quiz['status'] === 'Published' ? 'Draft' : 'Published';

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            UPDATE quizzes
            SET status = ?
            WHERE quiz_id = ?
              AND professor_id = ?
        ");
        $stmt->bind_param("sii", $newStatus, $quiz_id, $professor_id);
        $stmt->execute();
        $stmt->close();

        if ($newStatus === 'Published') {
            $notificationStmt = $conn->prepare("
                INSERT IGNORE INTO lms_notifications (student_id, lms_class_id, notification_type, reference_id, title, message)
                SELECT
                    e.student_id,
                    lc.lms_class_id,
                    'Quiz',
                    q.quiz_id,
                    'New Quiz Available',
                    CONCAT('Your professor published a new quiz: ', q.title)
                FROM quizzes q
                INNER JOIN class_schedules cs ON cs.schedule_id = q.schedule_id
                INNER JOIN sections sec ON sec.section_id = cs.section_id
                INNER JOIN school_years sy ON sy.school_year_id = sec.school_year_id
                INNER JOIN enrollments e ON e.section_id = sec.section_id
                    AND e.school_year_id = sy.school_year_id
                    AND e.status = 'Confirmed'
                    AND e.stage = 'Enrolled'
                INNER JOIN lms_classes lc ON lc.schedule_id = cs.schedule_id
                    AND lc.status = 'Active'
                WHERE q.quiz_id = ?
                  AND q.status = 'Published'
                  AND sy.is_active = 1
            ");
            $notificationStmt->bind_param("i", $quiz_id);
            $notificationStmt->execute();
            $notificationStmt->close();
        } else {
            $notificationStmt = $conn->prepare("
                DELETE FROM lms_notifications
                WHERE notification_type = 'Quiz'
                  AND reference_id = ?
            ");
            $notificationStmt->bind_param("i", $quiz_id);
            $notificationStmt->execute();
            $notificationStmt->close();
        }

        $conn->commit();

        showSuccess(
            "Quiz Status Updated!",
            "The quiz is now " . $newStatus . ".",
            "window.location.href = '../views/professor/professor-quiz';"
        );
        exit;

    } catch (Throwable $e) {
        $conn->rollback();
        showError("Status Update Failed!", "The quiz status could not be updated. Please try again.");
        exit;
    }
}

header("Location: ../views/professor/professor-quiz");
exit;