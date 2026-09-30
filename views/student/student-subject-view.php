<?php

require_once '../../config/db.php';
require_once '../includes/auth.php';

requireStudentLogin();

$student_id = (int) $_SESSION['student_id'];

// Fetch student's current active enrollment
$stmt = $conn->prepare("
    SELECT
        e.enrollment_id,
        e.section_id,
        e.school_year_id,
        e.strand_id,
        e.grade_level,
        e.semester,
        s.student_number,
        s.first_name,
        s.last_name,
        sec.section_name,
        str.strand_name,
        sy.school_year
    FROM enrollments e
    INNER JOIN students s
        ON s.student_id = e.student_id
    INNER JOIN sections sec
        ON sec.section_id = e.section_id
    INNER JOIN strands str
        ON str.strand_id = e.strand_id
    INNER JOIN school_years sy
        ON sy.school_year_id = e.school_year_id
    WHERE e.student_id = ?
      AND e.status = 'Confirmed'
      AND e.stage = 'Enrolled'
    ORDER BY e.enrollment_id DESC
    LIMIT 1
");

$stmt->bind_param("i", $student_id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();
$stmt->close();

$subjects = [];

if ($current) {
    $stmt = $conn->prepare("
        SELECT
            cs.schedule_id,
            lc.lms_class_id,
            lc.status AS lms_class_status,
            sub.subject_id,
            sub.subject_code,
            sub.subject_name,
            sub.units,
            cs.day_of_week,
            cs.start_time,
            cs.end_time,
            p.first_name AS prof_first,
            p.last_name AS prof_last,
            r.room_name
        FROM class_schedules cs
        INNER JOIN curriculum c
            ON c.curriculum_id = cs.curriculum_id
        INNER JOIN subjects sub
            ON sub.subject_id = c.subject_id
        LEFT JOIN lms_classes lc
            ON lc.schedule_id = cs.schedule_id
        LEFT JOIN professors p
            ON p.professor_id = cs.professor_id
        LEFT JOIN rooms r
            ON r.room_id = cs.room_id
        WHERE cs.section_id = ?
        ORDER BY sub.subject_name ASC
    ");

    $stmt->bind_param("i", $current['section_id']);
    $stmt->execute();
    $subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

function abbreviateDays($csv)
{
    if (!$csv) {
        return 'TBA';
    }

    $map = [
        'Monday'    => 'M',
        'Tuesday'   => 'T',
        'Wednesday' => 'W',
        'Thursday'  => 'Th',
        'Friday'    => 'F',
        'Saturday'  => 'Sat',
        'Sunday'    => 'Sun',
    ];

    $days = array_map('trim', explode(',', $csv));
    $abbreviated = [];

    foreach ($days as $day) {
        $abbreviated[] = $map[$day] ?? $day;
    }

    return implode('', $abbreviated);
}

function formatSchedule($subject)
{
    if (
        empty($subject['day_of_week']) ||
        empty($subject['start_time']) ||
        empty($subject['end_time'])
    ) {
        return 'Schedule TBA';
    }

    return abbreviateDays($subject['day_of_week']) . ' ' .
        date('g:i A', strtotime($subject['start_time'])) .
        ' - ' .
        date('g:i A', strtotime($subject['end_time']));
}

function professorName($subject)
{
    if (empty($subject['prof_first'])) {
        return 'Teacher TBA';
    }

    return trim($subject['prof_first'] . ' ' . $subject['prof_last']);
}

function subjectIcon($subjectName)
{
    $name = strtolower($subjectName);

    if (str_contains($name, 'math')) {
        return 'bi-calculator-fill';
    }

    if (
        str_contains($name, 'science') ||
        str_contains($name, 'earth') ||
        str_contains($name, 'biology') ||
        str_contains($name, 'physics') ||
        str_contains($name, 'chemistry')
    ) {
        return 'bi-flask-fill';
    }

    if (
        str_contains($name, 'communication') ||
        str_contains($name, 'english') ||
        str_contains($name, 'filipino')
    ) {
        return 'bi-chat-square-text-fill';
    }

    if (
        str_contains($name, 'computer') ||
        str_contains($name, 'information') ||
        str_contains($name, 'technology')
    ) {
        return 'bi-pc-display-horizontal';
    }

    if (
        str_contains($name, 'arts') ||
        str_contains($name, 'music')
    ) {
        return 'bi-palette-fill';
    }

    if (
        str_contains($name, 'physical') ||
        str_contains($name, 'pe')
    ) {
        return 'bi-person-walking';
    }

    return 'bi-book-half';
}

include '../includes/header.php';
include '../includes/student-navbar.php';
include '../includes/student-sidebar.php';

?>

<main class="page-transition min-h-screen p-6 pt-20 lg:ml-72">
    <section class="mx-auto max-w-7xl space-y-8">
        <header class="rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-6 text-white shadow-xl">
            <span class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-amber-300">
                <i class="bi bi-book-half"></i>
                My Subjects
            </span>

            <h1 class="text-3xl font-extrabold tracking-tight text-white">
                Your Subjects
            </h1>

            <span class="mt-2 block text-sm text-blue-100">
                Select a subject to view its learning materials.
            </span>
        </header>

        <?php if (!$current): ?>
            <section class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <i class="bi bi-info-circle-fill text-3xl"></i>
                </div>

                <h2 class="mt-5 text-xl font-bold text-slate-700">
                    No active enrollment found
                </h2>

                <span class="mt-2 block text-sm text-slate-500">
                    Your subjects will appear here once your enrollment is confirmed.
                </span>
            </section>
        <?php elseif (empty($subjects)): ?>
            <section class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <i class="bi bi-journal-x text-3xl"></i>
                </div>

                <h2 class="mt-5 text-xl font-bold text-slate-700">
                    No subjects found
                </h2>

                <span class="mt-2 block text-sm text-slate-500">
                    There are currently no subjects assigned to your section.
                </span>
            </section>
        <?php else: ?>
            <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">
                        Enrolled Subjects
                    </h2>

                    <span class="mt-1 block text-sm text-slate-500">
                        <?= count($subjects) ?> subject<?= count($subjects) !== 1 ? 's' : '' ?> available
                    </span>
                </div>

                <span class="inline-flex w-fit items-center gap-2 rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-[#1E4DB7]">
                    <i class="bi bi-person-badge-fill"></i>
                    <?= htmlspecialchars($current['student_number'] ?? '—') ?>
                </span>
            </section>

            <section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                <?php foreach ($subjects as $subject): ?>
                    <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                        <header class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/10 text-amber-300">
                                    <i class="bi <?= htmlspecialchars(subjectIcon($subject['subject_name'])) ?> text-xl"></i>
                                </div>

                                <?php if (!empty($subject['subject_code'])): ?>
                                    <span class="rounded-lg bg-white/10 px-3 py-1 text-xs font-bold text-blue-100">
                                        <?= htmlspecialchars($subject['subject_code']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h3 class="mt-5 text-xl font-bold text-white">
                                <?= htmlspecialchars($subject['subject_name']) ?>
                            </h3>

                            <span class="mt-1 block text-sm text-blue-100">
                                <?= htmlspecialchars(professorName($subject)) ?>
                            </span>
                        </header>

                        <section class="p-5">
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[#1E4DB7]">
                                        <i class="bi bi-person-fill"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <span class="block text-xs font-bold uppercase tracking-wide text-slate-400">
                                            Teacher
                                        </span>

                                        <span class="block truncate text-sm font-semibold text-slate-700">
                                            <?= htmlspecialchars(professorName($subject)) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                                        <i class="bi bi-clock-fill"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <span class="block text-xs font-bold uppercase tracking-wide text-slate-400">
                                            Schedule
                                        </span>

                                        <span class="block truncate text-sm font-semibold text-slate-700">
                                            <?= htmlspecialchars(formatSchedule($subject)) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                                        <i class="bi bi-door-open-fill"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <span class="block text-xs font-bold uppercase tracking-wide text-slate-400">
                                            Room
                                        </span>

                                        <span class="block truncate text-sm font-semibold text-slate-700">
                                            <?= htmlspecialchars($subject['room_name'] ?? 'TBA') ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[#1E4DB7]">
                                        <i class="bi bi-layers-fill"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <span class="block text-xs font-bold uppercase tracking-wide text-slate-400">
                                            Units
                                        </span>

                                        <span class="block text-sm font-semibold text-slate-700">
                                            <?= htmlspecialchars($subject['units'] ?? '—') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="my-5 border-t border-slate-200"></div>

                            <?php if (!empty($subject['lms_class_id']) && $subject['lms_class_status'] === 'Active'): ?>
                                <a
                                    href="student-material-view.php?lms_class_id=<?= (int) $subject['lms_class_id'] ?>"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#1E4DB7] px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#132A52]"
                                >
                                    <i class="bi bi-eye-fill"></i>
                                    View Subject
                                </a>
                            <?php elseif (!empty($subject['lms_class_id'])): ?>
                                <button
                                    type="button"
                                    disabled
                                    class="flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-400"
                                >
                                    <i class="bi bi-lock-fill"></i>
                                    Subject Unavailable
                                </button> 
                            <?php else: ?>
                                <button
                                    type="button"
                                    disabled
                                    class="flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-400"
                                >
                                    <i class="bi bi-hourglass-split"></i>
                                    Materials Not Available
                                </button>
                            <?php endif; ?>
                        </section>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </section>
</main>