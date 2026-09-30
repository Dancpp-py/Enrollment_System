<?php

require_once "../../config/db.php";
require_once '../includes/auth.php';
requireStudentLogin();
include '../includes/header.php';
include '../includes/student-navbar.php';
include '../includes/student-sidebar.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$studentId = (int) $_SESSION['student_id'];

// ---------------------------------------------------------------
// Active school year
// ---------------------------------------------------------------
$syStmt = $conn->prepare("SELECT school_year_id FROM school_years WHERE is_active = 1 LIMIT 1");
$syStmt->execute();
$schoolYearId = $syStmt->get_result()->fetch_assoc()['school_year_id'] ?? 0;
$syStmt->close();

// ---------------------------------------------------------------
// Student's current confirmed enrollment (defines which classes/quizzes
// belong to them this year)
// ---------------------------------------------------------------
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

$quizzes = [];

if ($enrollment) {
    $stmt = $conn->prepare("
        SELECT
            q.quiz_id, q.title, q.time_limit, q.opens_at, q.closes_at,
            sub.subject_name, s.section_name, st.strand_name,
            p.first_name AS prof_first, p.last_name AS prof_last,
            (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id = q.quiz_id) AS item_count,
            qa.attempt_id, qa.status AS attempt_status, qa.score,
            qa.correct_count, qa.total_items, qa.started_at, qa.submitted_at
        FROM quizzes q
        INNER JOIN class_schedules cs ON cs.schedule_id = q.schedule_id
        INNER JOIN curriculum c ON c.curriculum_id = cs.curriculum_id
        INNER JOIN subjects sub ON sub.subject_id = c.subject_id
        INNER JOIN sections s ON s.section_id = cs.section_id
        INNER JOIN strands st ON st.strand_id = s.strand_id
        INNER JOIN professors p ON p.professor_id = q.professor_id
        LEFT JOIN quiz_attempts qa ON qa.quiz_id = q.quiz_id AND qa.student_id = ?
        WHERE q.status = 'Published'
          AND cs.section_id = ?
          AND c.grade_level = ?
          AND c.semester = ?
          AND c.strand_id = ?
        ORDER BY q.created_at DESC
    ");
    $stmt->bind_param(
        "iisss",
        $studentId,
        $enrollment['section_id'],
        $enrollment['grade_level'],
        $enrollment['semester'],
        $enrollment['strand_id']
    );
    $stmt->execute();
    $quizzes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// ---------------------------------------------------------------
// Derive status + stats
// ---------------------------------------------------------------
$upcomingCount = 0;
$completedCount = 0;
$scoreSum = 0.0;
$scoreCount = 0;

foreach ($quizzes as &$quiz) {
    $isSubmitted = ($quiz['attempt_status'] === 'Submitted');
    $quiz['display_status'] = $isSubmitted ? 'completed' : 'upcoming';

    if ($isSubmitted) {
        $completedCount++;
        if ($quiz['score'] !== null) {
            $scoreSum += (float) $quiz['score'];
            $scoreCount++;
        }
    } else {
        $upcomingCount++;
    }
}
unset($quiz);

$averageScore = $scoreCount > 0 ? round($scoreSum / $scoreCount, 1) : null;

function formatDuration($startedAt, $submittedAt) {
    if (!$startedAt || !$submittedAt) {
        return null;
    }
    $seconds = strtotime($submittedAt) - strtotime($startedAt);
    $minutes = max(0, round($seconds / 60));
    return $minutes . ' Minute' . ($minutes === 1 ? '' : 's');
}
?>

<main class="min-h-screen pt-20 bg-slate-50 lg:ml-72">
    <div class="px-4 pt-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl overflow-hidden rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-6 text-white shadow-xl sm:p-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-amber-300">
                        <i class="bi bi-card-checklist"></i>
                        Student Portal
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight">
                        Quizzes & Assessments
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-blue-100">
                        View your available quizzes, check assessment schedules,
                        and review your completed assessments.
                    </p>
                </div>

                <div class="min-w-[150px] rounded-2xl border border-white/15 bg-white/10 p-5 text-center backdrop-blur-sm">
                    <div class="text-3xl font-extrabold text-amber-300">
                        <?= $upcomingCount ?>
                    </div>
                    <div class="mt-1 text-xs font-medium text-blue-100">
                        Upcoming Quizzes
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <i class="bi bi-calendar-event-fill text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Scheduled Soon
                        </p>
                        <p class="mt-1 text-2xl font-extrabold text-[#0A1931]">
                            <?= $upcomingCount ?>
                        </p>
                        <p class="text-xs text-slate-400">
                            Upcoming quizzes
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="bi bi-check-circle-fill text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Completed
                        </p>
                        <p class="mt-1 text-2xl font-extrabold text-[#0A1931]">
                            <?= $completedCount ?>
                        </p>
                        <p class="text-xs text-slate-400">
                            Assessments
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#1E4DB7]">
                        <i class="bi bi-bar-chart-fill text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Assessment Average
                        </p>
                        <p class="mt-1 text-2xl font-extrabold text-[#0A1931]">
                            <?= $averageScore !== null ? htmlspecialchars($averageScore) . '%' : '—' ?>
                        </p>
                        <p class="text-xs text-slate-400">
                            Overall average
                        </p>
                    </div>
                </div>
            </div>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-lg font-extrabold text-[#0A1931]">
                                My Assessments
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                View your scheduled and completed quizzes.
                            </p>
                        </div>

                        <div class="relative w-full lg:max-w-sm">
                            <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input
                                type="text"
                                id="quizSearch"
                                placeholder="Search quizzes or subjects..."
                                autocomplete="off"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-[#1E4DB7] focus:bg-white focus:ring-2 focus:ring-blue-100"
                            >
                        </div>
                    </div>
                </div>

                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="quiz-filter-btn cursor-pointer rounded-xl bg-[#0A1931] px-4 py-2 text-xs font-bold text-white shadow-sm" data-filter="all">
                            All Quizzes
                        </button>
                        <button type="button" class="quiz-filter-btn cursor-pointer rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50" data-filter="upcoming">
                            Upcoming
                        </button>
                        <button type="button" class="quiz-filter-btn cursor-pointer rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50" data-filter="completed">
                            Completed
                        </button>
                    </div>
                </div>

                <div id="quizList" class="divide-y divide-slate-100">
                    <?php if (empty($quizzes)): ?>
                        <div class="px-6 py-14 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                <i class="bi bi-inbox text-2xl"></i>
                            </div>
                            <h3 class="mt-4 text-base font-bold text-[#0A1931]">
                                No quizzes yet
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Your teachers haven't published any quizzes for your classes yet.
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($quizzes as $quiz): ?>
                        <?php
                        $professorName = 'Mr./Ms. ' . $quiz['prof_last'];
                        $sectionLabel  = htmlspecialchars($quiz['strand_name'] . ' - ' . $quiz['section_name']);
                        $searchText    = strtolower($quiz['subject_name'] . ' ' . $quiz['title']);
                        $isCompleted   = $quiz['display_status'] === 'completed';
                        $isInProgress  = $quiz['attempt_status'] === 'In Progress';
                        $durationLabel = $quiz['time_limit'] ? $quiz['time_limit'] . ' Minutes' : 'Untimed';
                        ?>
                        <article class="quiz-item p-5 transition hover:bg-slate-50/70 sm:p-6"
                                 data-status="<?= $quiz['display_status'] ?>"
                                 data-search="<?= htmlspecialchars($searchText) ?>">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                                <div class="flex min-w-0 items-start gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl <?= $isCompleted ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-[#1E4DB7]' ?>">
                                        <i class="bi <?= $isCompleted ? 'bi-check2-circle' : 'bi-card-checklist' ?> text-xl"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-md bg-blue-50 px-2 py-1 text-[11px] font-bold text-[#1E4DB7]">
                                                <?= htmlspecialchars($quiz['subject_name']) ?>
                                            </span>
                                            <?php if ($isCompleted): ?>
                                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                                    <i class="bi bi-check-circle mr-1"></i> Completed
                                                </span>
                                            <?php elseif ($isInProgress): ?>
                                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-700">
                                                    <i class="bi bi-hourglass-split mr-1"></i> In Progress
                                                </span>
                                            <?php else: ?>
                                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700">
                                                    <i class="bi bi-calendar-event mr-1"></i> Upcoming
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <h3 class="mt-2 text-lg font-extrabold leading-snug text-[#0A1931]">
                                            <?= htmlspecialchars($quiz['title']) ?>
                                        </h3>

                                        <p class="mt-1 text-xs font-medium text-slate-500">
                                            <i class="bi bi-people mr-1"></i>
                                            <?= $sectionLabel ?>
                                        </p>

                                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-500">
                                            <span><i class="bi bi-question-circle mr-1"></i> <?= (int) $quiz['item_count'] ?> Items</span>
                                            <span>
                                                <i class="bi bi-stopwatch mr-1"></i>
                                                <?= $isCompleted
                                                    ? 'Completed in ' . (formatDuration($quiz['started_at'], $quiz['submitted_at']) ?? $durationLabel)
                                                    : $durationLabel ?>
                                            </span>
                                            <span><i class="bi bi-person mr-1"></i> <?= htmlspecialchars($professorName) ?></span>
                                        </div>

                                        <?php if ($isCompleted): ?>
                                            <div class="mt-3 inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                                <i class="bi bi-trophy"></i>
                                                Score: <?= (int) $quiz['correct_count'] ?> / <?= (int) $quiz['total_items'] ?> — <?= htmlspecialchars($quiz['score']) ?>%
                                            </div>
                                        <?php elseif ($quiz['closes_at']): ?>
                                            <div class="mt-3 inline-flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-800">
                                                <i class="bi bi-calendar3"></i>
                                                Due: <?= date('F j, Y \a\t g:i A', strtotime($quiz['closes_at'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="flex shrink-0 border-t border-slate-100 pt-4 lg:border-t-0 lg:pt-0">
                                    <?php if ($isCompleted): ?>
                                        <a href="student-quiz-result.php?attempt_id=<?= (int) $quiz['attempt_id'] ?>"
                                           class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50 sm:w-auto">
                                            <i class="bi bi-bar-chart"></i>
                                            View Result
                                        </a>
                                    <?php else: ?>
                                        <button type="button"
                                                class="open-guidelines-btn cursor-pointer inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#0A1931] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#1E4DB7] sm:w-auto"
                                                data-quiz-id="<?= (int) $quiz['quiz_id'] ?>"
                                                data-title="<?= htmlspecialchars($quiz['title']) ?>"
                                                data-subject="<?= htmlspecialchars($quiz['subject_name']) ?>"
                                                data-items="<?= (int) $quiz['item_count'] ?> Items"
                                                data-duration="<?= htmlspecialchars($durationLabel) ?>"
                                                data-schedule="<?= $quiz['closes_at'] ? 'Due ' . date('F j, Y \a\t g:i A', strtotime($quiz['closes_at'])) : 'No deadline' ?>"
                                                data-topics="Answer all items to the best of your ability. Once you begin, the time limit (if any) applies immediately.">
                                            <i class="bi bi-info-circle"></i>
                                            <?= $isInProgress ? 'Continue Quiz' : 'Exam Guidelines' ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div id="noQuizResults" class="hidden px-6 py-14 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                        <i class="bi bi-search text-2xl"></i>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-[#0A1931]">
                        No quizzes found
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Try another search term or change the selected filter.
                    </p>
                </div>
            </section>
        </div>
    </div>
</main>

<div id="quizModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm">
    <div id="quizModalContent" class="w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl">
        <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] px-6 py-5 text-white">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-300">
                        Exam Guidelines
                    </span>
                    <h2 id="modalQuizTitle" class="mt-1 text-xl font-extrabold">Quiz</h2>
                    <p id="modalQuizSubject" class="mt-1 text-xs text-blue-100">Subject</p>
                </div>
                <button type="button" id="closeQuizModal" class="flex cursor-pointer h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white transition hover:bg-white/20" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        <div class="space-y-5 p-6">
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Questions</p>
                    <p id="modalQuizItems" class="mt-1 text-sm font-extrabold text-[#0A1931]">—</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Time Limit</p>
                    <p id="modalQuizDuration" class="mt-1 text-sm font-extrabold text-[#0A1931]">—</p>
                </div>
                <div class="col-span-2 rounded-xl bg-slate-50 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Schedule</p>
                    <p id="modalQuizSchedule" class="mt-1 text-sm font-extrabold text-[#0A1931]">—</p>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-extrabold text-[#0A1931]">Topics Covered</h3>
                <p id="modalQuizTopics" class="mt-2 text-sm leading-relaxed text-slate-600">—</p>
            </div>

            <div class="flex gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                <i class="bi bi-exclamation-circle-fill mt-0.5 text-amber-600"></i>
                <div>
                    <p class="text-xs font-bold text-amber-800">Important Reminder</p>
                    <p class="mt-1 text-xs leading-relaxed text-amber-700">
                        Make sure you are ready before starting the assessment.
                        Once the quiz begins, the time limit will apply.
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="button" id="cancelQuizModal" class="rounded-xl border cursor-pointer border-slate-200 px-5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                    Close
                </button>
                <a id="modalStartQuizLink" href="student-quiz-v2.php" class="inline-flex items-center justify-center rounded-xl bg-[#1E4DB7] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#0A1931]">
                    <i class="bi bi-play-fill mr-1"></i>
                    Start Quiz
                </a>
            </div>
        </div>
    </div>
</div>

<script src="/enrollment_system/assets/js/student-quiz.js"></script>