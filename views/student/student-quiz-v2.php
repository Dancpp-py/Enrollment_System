<?php

require_once '../../config/db.php';
require_once '../includes/auth.php';
requireStudentLogin();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// NOTE: every query below uses $studentId (camelCase) — keep this the single
// source of truth so a stray $student_id typo doesn't silently bind NULL again.
$studentId = (int) $_SESSION['student_id'];
$quizId    = (int) ($_GET['quiz_id'] ?? 0);

if ($quizId <= 0) {
    header("Location: student-quiz.php");
    exit;
}

// ---------------------------------------------------------------
// Active school year + the student's current confirmed enrollment
// ---------------------------------------------------------------
$syStmt = $conn->prepare("SELECT school_year_id FROM school_years WHERE is_active = 1 LIMIT 1");
$syStmt->execute();
$schoolYearId = $syStmt->get_result()->fetch_assoc()['school_year_id'] ?? 0;
$syStmt->close();

$enrollStmt = $conn->prepare("
    SELECT section_id, grade_level, semester, strand_id
    FROM enrollments
    WHERE student_id = ? AND school_year_id = ? AND status = 'Confirmed'
    ORDER BY enrollment_id DESC
    LIMIT 1
");
$enrollStmt->bind_param("ii", $studentId, $schoolYearId);
$enrollStmt->execute();
$enrollment = $enrollStmt->get_result()->fetch_assoc();
$enrollStmt->close();

if (!$enrollment) {
    header("Location: student-quiz.php");
    exit;
}

// ---------------------------------------------------------------
// Load the quiz and confirm the student is actually eligible for it
// (published + matches their current class on section/grade/semester/strand)
// ---------------------------------------------------------------
$stmt = $conn->prepare("
    SELECT
        q.quiz_id, q.title, q.time_limit, q.opens_at, q.closes_at, q.status,
        q.professor_id, sub.subject_name, s.section_name, st.strand_name,
        p.first_name AS prof_first, p.last_name AS prof_last
    FROM quizzes q
    INNER JOIN class_schedules cs ON cs.schedule_id = q.schedule_id
    INNER JOIN curriculum c ON c.curriculum_id = cs.curriculum_id
    INNER JOIN subjects sub ON sub.subject_id = c.subject_id
    INNER JOIN sections s ON s.section_id = cs.section_id
    INNER JOIN strands st ON st.strand_id = s.strand_id
    INNER JOIN professors p ON p.professor_id = q.professor_id
    WHERE q.quiz_id = ?
      AND q.status = 'Published'
      AND cs.section_id = ?
      AND c.grade_level = ?
      AND c.semester = ?
      AND c.strand_id = ?
    LIMIT 1
");
$stmt->bind_param(
    "iisss",
    $quizId,
    $enrollment['section_id'],
    $enrollment['grade_level'],
    $enrollment['semester'],
    $enrollment['strand_id']
);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$quiz) {
    // Not published, not their class, or doesn't exist — nothing to show.
    header("Location: student-quiz.php");
    exit;
}

$now = new DateTime();

if (!empty($quiz['opens_at']) && $now < new DateTime($quiz['opens_at'])) {
    header("Location: student-quiz.php?error=" . urlencode("This quiz is not open yet."));
    exit;
}

// ---------------------------------------------------------------
// Find or create the attempt
// ---------------------------------------------------------------
$stmt = $conn->prepare("
    SELECT *, TIMESTAMPDIFF(SECOND, started_at, NOW()) AS elapsed_seconds
    FROM quiz_attempts
    WHERE quiz_id = ? AND student_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $quizId, $studentId);
$stmt->execute();
$attempt = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($attempt && $attempt['status'] === 'Submitted') {
    header("Location: student-quiz-result.php?attempt_id=" . (int) $attempt['attempt_id']);
    exit;
}

if (!$attempt) {
    if (!empty($quiz['closes_at']) && $now > new DateTime($quiz['closes_at'])) {
        header("Location: student-quiz.php?error=" . urlencode("This quiz's deadline has passed."));
        exit;
    }

    $insert = $conn->prepare("INSERT INTO quiz_attempts (quiz_id, student_id, status, started_at) VALUES (?, ?, 'In Progress', NOW())");
    $insert->bind_param("ii", $quizId, $studentId);
    $insert->execute();
    $attemptId = $insert->insert_id;
    $insert->close();

    $stmt = $conn->prepare("
        SELECT *, TIMESTAMPDIFF(SECOND, started_at, NOW()) AS elapsed_seconds
        FROM quiz_attempts
        WHERE attempt_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $attemptId);
    $stmt->execute();
    $attempt = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// ---------------------------------------------------------------
// Compute remaining time. If it's already run out, finalize immediately.
// ---------------------------------------------------------------
$remainingSeconds = null;

if ($quiz['time_limit']) {
    $elapsed = (int) $attempt['elapsed_seconds']; // computed by MySQL, no PHP timezone involved
    $remainingSeconds = max(0, ((int) $quiz['time_limit'] * 60) - $elapsed);

    if ($remainingSeconds <= 0) {
        require_once "../../controllers/quiz_grading.php";
        finalizeQuizAttempt($conn, (int) $attempt['attempt_id'], []);
        header("Location: student-quiz-result.php?attempt_id=" . (int) $attempt['attempt_id']);
        exit;
    }
}

// ---------------------------------------------------------------
// Load questions
// ---------------------------------------------------------------
$stmt = $conn->prepare("
    SELECT question_id, question_order, question_text, choice_a, choice_b, choice_c, choice_d
    FROM quiz_questions
    WHERE quiz_id = ?
    ORDER BY question_order ASC, question_id ASC
");
$stmt->bind_param("i", $quizId);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$professorName = 'Mr./Ms. ' . $quiz['prof_last'];
$totalQuestions = count($questions);
$timeLimitLabel = $quiz['time_limit'] ? $quiz['time_limit'] . ' Minutes' : 'Untimed';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-slate-100">

<main class="min-h-screen bg-slate-50">
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="space-y-6">
            <div class="overflow-hidden rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] text-white shadow-xl">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-amber-300">
                                <i class="bi bi-pencil-square"></i>
                                Taking Quiz
                            </div>
                            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">
                                <?= htmlspecialchars($quiz['title']) ?>
                            </h1>
                            <p class="mt-2 text-sm text-blue-100">
                                <?= htmlspecialchars($quiz['subject_name']) ?>
                            </p>
                            <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-xs text-blue-100">
                                <span class="inline-flex items-center gap-1.5"><i class="bi bi-question-circle"></i> <?= $totalQuestions ?> Questions</span>
                                <span class="inline-flex items-center gap-1.5"><i class="bi bi-stopwatch"></i> <?= htmlspecialchars($timeLimitLabel) ?></span>
                                <span class="inline-flex items-center gap-1.5"><i class="bi bi-person"></i> <?= htmlspecialchars($professorName) ?></span>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <div class="rounded-2xl border border-white/15 bg-white/10 px-6 py-4 text-center backdrop-blur-sm">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-blue-200">
                                    <?= $remainingSeconds === null ? 'Time Limit' : 'Time Remaining' ?>
                                </p>

                                <?php
                                    if ($remainingSeconds === null) {
                                        $timerDisplay = '∞';
                                    } else {
                                        $h = floor($remainingSeconds / 3600);
                                        $m = floor(($remainingSeconds % 3600) / 60);
                                        $s = $remainingSeconds % 60;
                                        $timerDisplay = $h > 0 ? sprintf('%d:%02d:%02d', $h, $m, $s) : sprintf('%02d:%02d', $m, $s);
                                    }
                                ?>
                                <p id="quizTimer" class="mt-1 text-3xl font-extrabold tracking-wide text-amber-300">
                                    <?= htmlspecialchars($timerDisplay) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-1.5 bg-white/10">
                    <div id="quizProgress" class="h-full bg-amber-400 transition-all duration-300" style="width: <?= $totalQuestions ? round(100 / $totalQuestions) : 0 ?>%;"></div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Quiz Progress</p>
                        <p class="mt-1 text-sm font-extrabold text-[#0A1931]">
                            Question <span id="currentQuestionNumber">1</span> of <span id="totalQuestionNumber"><?= $totalQuestions ?></span>
                        </p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xs text-slate-400">Answered</p>
                        <p class="mt-1 text-sm font-extrabold text-[#1E4DB7]">
                            <span id="answeredCount">0</span> / <span id="answeredTotal"><?= $totalQuestions ?></span>
                        </p>
                    </div>
                </div>
                <div id="questionIndicators" class="mt-5 flex flex-wrap gap-2">
                    <?php foreach ($questions as $i => $q): ?>
                        <button type="button" class="question-indicator <?= $i === 0 ? 'active' : '' ?>" data-question="<?= $i ?>"><?= $i + 1 ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <form id="quizForm" action="../../controllers/process_quiz_submit.php" method="POST">
                <input type="hidden" name="attempt_id" value="<?= (int) $attempt['attempt_id'] ?>">

                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#1E4DB7]">
                                <i class="bi bi-question-lg text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Question <span id="questionHeadingNumber">1</span>
                                </p>
                                <p class="text-xs text-slate-500">Select one answer.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 sm:p-6">
                        <?php foreach ($questions as $i => $q): ?>
                            <div class="quiz-question <?= $i === 0 ? '' : 'hidden' ?>" data-question-index="<?= $i ?>">
                                <h2 class="text-lg font-bold leading-relaxed text-[#0A1931] sm:text-xl">
                                    <?= htmlspecialchars($q['question_text']) ?>
                                </h2>
                                <div class="mt-6 space-y-3">
                                    <?php foreach (['A' => 'choice_a', 'B' => 'choice_b', 'C' => 'choice_c', 'D' => 'choice_d'] as $letter => $col): ?>
                                        <label class="answer-option">
                                            <input type="radio" name="question_<?= (int) $q['question_id'] ?>" value="<?= $letter ?>" class="answer-radio">
                                            <span class="answer-letter"><?= $letter ?></span>
                                            <span class="answer-text"><?= htmlspecialchars($q[$col]) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <button type="button" id="previousQuestion" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40" disabled>
                            <i class="bi bi-arrow-left"></i> Previous
                        </button>
                        <button type="button" id="nextQuestion" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#1E4DB7] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#0A1931]">
                            Next <i class="bi bi-arrow-right"></i>
                        </button>
                        <button type="button" id="submitQuiz" class="hidden inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                            <i class="bi bi-check-circle"></i> Submit Quiz
                        </button>
                    </div>
                </section>
            </form>

            <div class="flex gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <i class="bi bi-exclamation-triangle-fill mt-0.5 shrink-0 text-amber-600"></i>
                <div>
                    <p class="text-xs font-bold text-amber-800">Important</p>
                    <p class="mt-1 text-xs leading-relaxed text-amber-700">
                        Make sure you review your answers before submitting.
                        Once submitted, the quiz cannot be changed.
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<div id="submitModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-[#1E4DB7]">
            <i class="bi bi-send-check-fill text-2xl"></i>
        </div>
        <h2 class="mt-5 text-xl font-extrabold text-[#0A1931]">Submit Quiz?</h2>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Are you sure you want to submit your quiz? You will not be able
            to change your answers after submission.
        </p>
        <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <button type="button" id="cancelSubmit" class="rounded-xl border border-slate-200 px-5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                Continue Quiz
            </button>
            <button type="button" id="confirmSubmit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                <i class="bi bi-check-circle mr-1"></i> Submit Quiz
            </button>
        </div>
    </div>
</div>

<style>
    .question-indicator { display: inline-flex; height: 34px; width: 34px; align-items: center; justify-content: center; border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff; color: #64748b; font-size: 12px; font-weight: 700; transition: all 0.2s ease; }
    .question-indicator:hover { background: #f8fafc; }
    .question-indicator.active { border-color: #0A1931; background: #0A1931; color: #ffffff; }
    .question-indicator.answered { border-color: #1E4DB7; background: #eff6ff; color: #1E4DB7; }
    .question-indicator.active.answered { border-color: #0A1931; background: #0A1931; color: #ffffff; }
    .answer-option { display: flex; width: 100%; cursor: pointer; align-items: center; gap: 14px; border: 1px solid #e2e8f0; border-radius: 14px; padding: 15px; background: #ffffff; transition: all 0.2s ease; }
    .answer-option:hover { border-color: #93c5fd; background: #f8fbff; }
    .answer-option.selected { border-color: #1E4DB7; background: #eff6ff; }
    .answer-radio { position: absolute; opacity: 0; pointer-events: none; }
    .answer-letter { display: flex; height: 38px; width: 38px; flex-shrink: 0; align-items: center; justify-content: center; border-radius: 10px; background: #f1f5f9; color: #475569; font-size: 13px; font-weight: 800; transition: all 0.2s ease; }
    .answer-option:hover .answer-letter { background: #dbeafe; color: #1E4DB7; }
    .answer-option.selected .answer-letter { background: #1E4DB7; color: #ffffff; }
    .answer-text { color: #334155; font-size: 14px; line-height: 1.5; }
    .answer-option.selected .answer-text { color: #0A1931; font-weight: 600; }
</style>

<script>
    // Server-computed values the JS timer/auto-submit logic needs.
    window.QUIZ_REMAINING_SECONDS = <?= $remainingSeconds === null ? 'null' : (int) $remainingSeconds ?>;
</script>
<script src="/enrollment_system/assets/js/student-quiz-v2.js"></script>
</body>
</html>