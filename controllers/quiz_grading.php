<?php
/**
 * finalizeQuizAttempt()
 *
 * Grades and closes out a quiz attempt. Safe to call at most once per
 * attempt in practice, since the caller should only invoke this while the
 * attempt is still 'In Progress' (both call sites check that first).
 *
 * @param mysqli $conn
 * @param int    $attemptId
 * @param array  $answers   [question_id => 'A'|'B'|'C'|'D'], missing/blank
 *                           entries are treated as unanswered.
 * @return array{correct_count:int, total_items:int, score:float}
 */
function finalizeQuizAttempt(mysqli $conn, int $attemptId, array $answers): array
{
    $attemptStmt = $conn->prepare("SELECT quiz_id FROM quiz_attempts WHERE attempt_id = ? LIMIT 1");
    $attemptStmt->bind_param("i", $attemptId);
    $attemptStmt->execute();
    $attemptRow = $attemptStmt->get_result()->fetch_assoc();
    $attemptStmt->close();

    if (!$attemptRow) {
        throw new Exception("Attempt not found.");
    }

    $quizId = (int) $attemptRow['quiz_id'];

    $qStmt = $conn->prepare("SELECT question_id, correct_answer FROM quiz_questions WHERE quiz_id = ?");
    $qStmt->bind_param("i", $quizId);
    $qStmt->execute();
    $questions = $qStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $qStmt->close();

    $totalItems   = count($questions);
    $correctCount = 0;

    $conn->begin_transaction();

    try {
        $insertAnswer = $conn->prepare("
            INSERT INTO quiz_attempt_answers (attempt_id, question_id, selected_answer, is_correct)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE selected_answer = VALUES(selected_answer), is_correct = VALUES(is_correct)
        ");

        foreach ($questions as $question) {
            $questionId = (int) $question['question_id'];
            $selected   = $answers[$questionId] ?? null;

            if (!in_array($selected, ['A', 'B', 'C', 'D'], true)) {
                $selected = null;
            }

            $isCorrect = ($selected !== null && $selected === $question['correct_answer']) ? 1 : 0;
            if ($isCorrect) {
                $correctCount++;
            }

            $insertAnswer->bind_param("iisi", $attemptId, $questionId, $selected, $isCorrect);
            $insertAnswer->execute();
        }
        $insertAnswer->close();

        $score = $totalItems > 0 ? round(($correctCount / $totalItems) * 100, 2) : 0.00;

        $update = $conn->prepare("
            UPDATE quiz_attempts
            SET status = 'Submitted', submitted_at = NOW(), total_items = ?, correct_count = ?, score = ?
            WHERE attempt_id = ?
        ");
        $update->bind_param("iidi", $totalItems, $correctCount, $score, $attemptId);
        $update->execute();
        $update->close();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

    return [
        'correct_count' => $correctCount,
        'total_items'   => $totalItems,
        'score'         => $score,
    ];
}