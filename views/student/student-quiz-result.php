<?php
require_once "../../config/db.php";
require_once '../includes/auth.php';
requireStudentLogin();
include '../includes/header.php';
include '../includes/student-sidebar.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$studentId = (int) $_SESSION['student_id'];
$attemptId = (int) ($_GET['attempt_id'] ?? 0);

$stmt = $conn->prepare("
    SELECT
        qa.attempt_id, qa.status, qa.started_at, qa.submitted_at,
        qa.correct_count, qa.total_items, qa.score,
        q.quiz_id, q.title, q.time_limit,
        sub.subject_name, s.section_name, st.strand_name,
        p.first_name AS prof_first, p.last_name AS prof_last
    FROM quiz_attempts qa
    INNER JOIN quizzes q ON q.quiz_id = qa.quiz_id
    INNER JOIN class_schedules cs ON cs.schedule_id = q.schedule_id
    INNER JOIN curriculum c ON c.curriculum_id = cs.curriculum_id
    INNER JOIN subjects sub ON sub.subject_id = c.subject_id
    INNER JOIN sections s ON s.section_id = cs.section_id
    INNER JOIN strands st ON st.strand_id = s.strand_id
    INNER JOIN professors p ON p.professor_id = q.professor_id
    WHERE qa.attempt_id = ? AND qa.student_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $attemptId, $studentId);
$stmt->execute();
$attempt = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$attempt) {
    header("Location: student-quiz.php");
    exit;
}

if ($attempt['status'] !== 'Submitted') {
    // Still in progress — send them back to finish it instead of viewing a result.
    header("Location: student-quiz-v2.php?quiz_id=" . (int) $attempt['quiz_id']);
    exit;
}

$stmt = $conn->prepare("
    SELECT qq.question_order, qq.question_text, qq.choice_a, qq.choice_b, qq.choice_c, qq.choice_d,
           qq.correct_answer, qaa.selected_answer, qaa.is_correct
    FROM quiz_questions qq
    LEFT JOIN quiz_attempt_answers qaa ON qaa.question_id = qq.question_id AND qaa.attempt_id = ?
    WHERE qq.quiz_id = ?
    ORDER BY qq.question_order ASC, qq.question_id ASC
");
$stmt->bind_param("ii", $attemptId, $attempt['quiz_id']);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$professorName = 'Mr./Ms. ' . $attempt['prof_last'];
$durationMinutes = $attempt['submitted_at']
    ? max(0, round((strtotime($attempt['submitted_at']) - strtotime($attempt['started_at'])) / 60))
    : null;

$choiceLabels = ['A' => 'choice_a', 'B' => 'choice_b', 'C' => 'choice_c', 'D' => 'choice_d'];
?>

<main class="min-h-screen bg-slate-50 lg:ml-72">
    <div class="px-4 pt-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl overflow-hidden rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-6 text-white shadow-xl sm:p-8">
            <a href="student-quiz.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-200 hover:text-amber-300 transition">
                <i class="bi bi-arrow-left"></i> Back to Quizzes
            </a>
            <div class="mt-3 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <span class="rounded-md bg-white/10 px-2 py-1 text-[11px] font-bold text-blue-100">
                        <?= htmlspecialchars($attempt['subject_name']) ?>
                    </span>
                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">
                        <?= htmlspecialchars($attempt['title']) ?>
                    </h1>
                    <p class="mt-2 text-sm text-blue-100">
                        <?= htmlspecialchars($attempt['strand_name'] . ' - ' . $attempt['section_name']) ?>
                        &middot; <?= htmlspecialchars($professorName) ?>
                    </p>
                </div>
                <div class="min-w-[150px] rounded-2xl border border-white/15 bg-white/10 p-5 text-center backdrop-blur-sm">
                    <div class="text-3xl font-extrabold text-amber-300">
                        <?= htmlspecialchars($attempt['score']) ?>%
                    </div>
                    <div class="mt-1 text-xs font-medium text-blue-100">
                        <?= (int) $attempt['correct_count'] ?> / <?= (int) $attempt['total_items'] ?> correct
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl space-y-6">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Score</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#0A1931]"><?= htmlspecialchars($attempt['score']) ?>%</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Correct</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#0A1931]"><?= (int) $attempt['correct_count'] ?> / <?= (int) $attempt['total_items'] ?></p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Time Taken</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#0A1931]">
                        <?= $durationMinutes !== null ? $durationMinutes . ' min' : '—' ?>
                    </p>
                </div>
            </div>

            <section class="space-y-4">
                <?php foreach ($rows as $i => $row): ?>
                    <?php
                    $isCorrect = (bool) $row['is_correct'];
                    $selected  = $row['selected_answer'];
                    ?>
                    <div class="rounded-2xl border <?= $isCorrect ? 'border-emerald-200' : 'border-red-200' ?> bg-white p-5 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full <?= $isCorrect ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' ?> text-sm font-bold">
                                <?= $isCorrect ? '<i class="bi bi-check-lg"></i>' : '<i class="bi bi-x-lg"></i>' ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Question <?= $i + 1 ?>
                                </p>
                                <h3 class="mt-1 text-base font-bold text-[#0A1931]">
                                    <?= htmlspecialchars($row['question_text']) ?>
                                </h3>

                                <div class="mt-3 space-y-2">
                                    <?php foreach ($choiceLabels as $letter => $col): ?>
                                        <?php
                                        $isThisCorrect  = $letter === $row['correct_answer'];
                                        $isThisSelected = $letter === $selected;
                                        $rowClasses = 'border-slate-200 bg-white';
                                        if ($isThisCorrect) {
                                            $rowClasses = 'border-emerald-300 bg-emerald-50';
                                        } elseif ($isThisSelected && !$isThisCorrect) {
                                            $rowClasses = 'border-red-300 bg-red-50';
                                        }
                                        ?>
                                        <div class="flex items-center gap-3 rounded-xl border <?= $rowClasses ?> px-4 py-2.5 text-sm">
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-slate-100 text-xs font-bold text-slate-600"><?= $letter ?></span>
                                            <span class="text-slate-700"><?= htmlspecialchars($row[$col]) ?></span>
                                            <?php if ($isThisCorrect): ?>
                                                <span class="ml-auto text-xs font-bold text-emerald-700">Correct answer</span>
                                            <?php elseif ($isThisSelected): ?>
                                                <span class="ml-auto text-xs font-bold text-red-700">Your answer</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if ($selected === null): ?>
                                        <p class="text-xs font-semibold text-amber-700">
                                            <i class="bi bi-exclamation-circle mr-1"></i> You didn't answer this one.
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <div class="text-center">
                <a href="student-quiz.php" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0A1931] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#1E4DB7]">
                    <i class="bi bi-arrow-left"></i> Back to Quizzes
                </a>
            </div>
        </div>
    </div>
</main>