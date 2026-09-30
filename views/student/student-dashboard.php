<?php
require_once '../../config/db.php';
require_once '../includes/auth.php';
requireStudentLogin();

$student_id = (int) $_SESSION['student_id'];

// -----------------------------------------------------------------
// Most recent officially-Enrolled record — this is "current" for the
// dashboard (section, school year, currently loaded subjects).
// -----------------------------------------------------------------
$stmt = $conn->prepare("
    SELECT
        e.enrollment_id, e.section_id, e.strand_id, e.grade_level, e.semester,
        s.student_number, s.first_name, s.last_name,
        sec.section_name,
        str.strand_name,
        sy.school_year
    FROM enrollments e
    INNER JOIN students s ON s.student_id = e.student_id
    INNER JOIN sections sec ON sec.section_id = e.section_id
    INNER JOIN strands str ON str.strand_id = e.strand_id
    INNER JOIN school_years sy ON sy.school_year_id = e.school_year_id
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
    // Every subject for this strand/grade/semester, left-joined against
    // this specific section's class_schedules — so an unscheduled subject
    // still shows up, just marked TBA instead of disappearing.
    $stmt = $conn->prepare("
        SELECT
            sub.subject_code, sub.subject_name, sub.units,
            cs.day_of_week, cs.start_time, cs.end_time,
            r.room_name,
            p.first_name AS prof_first, p.last_name AS prof_last
        FROM curriculum c
        INNER JOIN subjects sub ON sub.subject_id = c.subject_id
        LEFT JOIN class_schedules cs ON cs.curriculum_id = c.curriculum_id AND cs.section_id = ?
        LEFT JOIN rooms r ON r.room_id = cs.room_id
        LEFT JOIN professors p ON p.professor_id = cs.professor_id
        WHERE c.strand_id = ? AND c.grade_level = ? AND c.semester = ?
        ORDER BY sub.subject_name
    ");
    $stmt->bind_param("iiss", $current['section_id'], $current['strand_id'], $current['grade_level'], $current['semester']);
    $stmt->execute();
    $subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

function abbreviateDays($csv) {
    if (!$csv) return 'TBA';
    $map = [
        'Monday' => 'M', 'Tuesday' => 'T', 'Wednesday' => 'W',
        'Thursday' => 'Th', 'Friday' => 'F', 'Saturday' => 'Sat',
    ];
    $days = array_map('trim', explode(',', $csv));
    $abbrev = array_map(fn($d) => $map[$d] ?? $d, $days);
    return implode('', $abbrev);
}

function formatSchedule($row) {
    if (!$row['day_of_week'] || !$row['start_time'] || !$row['end_time']) {
        return 'TBA';
    }
    return abbreviateDays($row['day_of_week']) . ' ' .
        date('g:i A', strtotime($row['start_time'])) . ' - ' . date('g:i A', strtotime($row['end_time']));
}

function teacherName($row) {
    if (!$row['prof_first']) return 'TBA';
    return htmlspecialchars($row['prof_first'] . ' ' . $row['prof_last']);
}

include '../includes/header.php';
include '../includes/student-navbar.php';
include '../includes/student-sidebar.php';
?>

<main class="page-transition min-h-screen lg:ml-72 p-3 sm:p-6 pt-16 sm:pt-20 bg-slate-50/50">
    <section class="mx-auto max-w-7xl space-y-4 sm:space-y-6">
        <!-- Hero Header -->
        <header class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-5 sm:p-8 text-white shadow-xl">
            <span class="mb-2 inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-2.5 sm:px-3 py-0.5 sm:py-1 text-[10px] sm:text-xs font-bold text-amber-300 backdrop-blur-sm">
                <i class="bi bi-mortarboard-fill"></i>
                Student Portal
            </span>
            <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight text-white leading-snug">
                Welcome back, <?= $current ? htmlspecialchars($current['first_name'] . ' ' . $current['last_name']) : htmlspecialchars($_SESSION['student_name'] ?? 'Student') ?>!
            </h1>
            <span class="mt-1 sm:mt-2 block text-xs sm:text-sm text-blue-100">
                View your enrolled subjects and class information.
            </span>
            <!-- Background Decorative Glow -->
            <div class="pointer-events-none absolute -right-10 -bottom-10 h-48 sm:h-64 w-48 sm:w-64 rounded-full bg-blue-400/10 blur-3xl"></div>
        </header>

        <?php if (!$current): ?>

            <section class="rounded-2xl border border-slate-200 bg-white p-8 sm:p-12 text-center shadow-sm">
                <i class="bi bi-info-circle mb-3 block text-3xl sm:text-4xl text-slate-300"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-700">No active enrollment found</h3>
                <span class="mt-1 block text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                    Your dashboard will show your subjects once you're officially enrolled.
                </span>
            </section>

        <?php else: ?>

            <!-- Metric Cards Section -->
            <section class="grid grid-cols-1 gap-3 sm:gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Card 1: Enrolled Subjects -->
                <section class="rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-5 shadow-sm transition-all duration-150 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-xl bg-blue-100/80 text-[#1E4DB7]">
                            <i class="bi bi-book-half text-lg sm:text-xl"></i>
                        </div>
                        <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Academic</span>
                    </div>
                    <span class="mt-3 sm:mt-4 block text-2xl sm:text-3xl font-extrabold text-slate-800"><?= count($subjects) ?></span>
                    <span class="mt-0.5 sm:mt-1 block text-xs sm:text-sm text-slate-500 font-medium">Enrolled Subjects</span>
                </section>

                <!-- Card 2: Current Section -->
                <section class="rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-5 shadow-sm transition-all duration-150 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-xl bg-amber-100/80 text-amber-600">
                            <i class="bi bi-people-fill text-lg sm:text-xl"></i>
                        </div>
                        <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Class</span>
                    </div>
                    <span class="mt-3 sm:mt-4 block text-xl sm:text-2xl font-extrabold text-slate-800 truncate">
                        <?= htmlspecialchars($current['strand_name'] . ' ' . str_replace('Grade ', '', $current['grade_level']) . '-' . $current['section_name']) ?>
                    </span>
                    <span class="mt-0.5 sm:mt-1 block text-xs sm:text-sm text-slate-500 font-medium">Current Section</span>
                </section>

                <!-- Card 3: School Year -->
                <section class="rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-5 shadow-sm transition-all duration-150 hover:shadow-md sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-xl bg-slate-100 text-[#0A1931]">
                            <i class="bi bi-calendar3 text-lg sm:text-xl"></i>
                        </div>
                        <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Academic</span>
                    </div>
                    <span class="mt-3 sm:mt-4 block text-xl sm:text-2xl font-extrabold text-slate-800"><?= htmlspecialchars($current['school_year']) ?></span>
                    <span class="mt-0.5 sm:mt-1 block text-xs sm:text-sm text-slate-500 font-medium">School Year</span>
                </section>
            </section>

            <!-- Subjects Table Container -->
            <section class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
                <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] p-4 sm:px-6 sm:py-5 gap-3 text-white">
                    <section>
                        <h2 class="text-lg sm:text-2xl font-bold text-white tracking-tight">Enrolled Subjects</h2>
                        <span class="mt-0.5 block text-xs sm:text-sm text-blue-100">
                            Your subjects, teachers, schedules, and assigned rooms.
                        </span>
                    </section>
                    <span class="inline-flex w-fit items-center gap-1.5 sm:gap-2 rounded-xl bg-white/10 border border-white/10 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-blue-100 backdrop-blur-sm">
                        <i class="bi bi-person-badge-fill text-amber-400"></i>
                        <?= htmlspecialchars($current['student_number'] ?? '—') ?>
                    </span>
                </header>

                <!-- Mobile Horizontal Scroll Tip -->
                <div class="block sm:hidden bg-slate-50 border-b border-slate-100 px-4 py-1.5 text-[10px] text-slate-400 text-center font-medium">
                    <i class="bi bi-arrow-left-right mr-1"></i> Scroll horizontally to view full schedule
                </div>

                <!-- Table Wrapper for Responsiveness -->
                <section class="overflow-x-auto min-w-full">
                    <table class="w-full text-left border-collapse min-w-[700px] sm:min-w-[850px]">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/75 text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500">
                                <th class="px-3 sm:px-6 py-3 sm:py-4">Subject Code</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4">Subject</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4">Teacher</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4">Schedule</th>
                                <th class="px-3 sm:px-6 py-3 sm:py-4">Room</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                            <?php if (empty($subjects)): ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400 text-xs sm:text-sm">
                                        No subjects found for this term yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($subjects as $subj): ?>
                                    <tr class="transition-all duration-150 hover:bg-slate-50/80">
                                        <td class="whitespace-nowrap px-3 sm:px-6 py-3.5 sm:py-5">
                                            <span class="inline-block rounded-lg border border-blue-100/60 bg-blue-50 px-2.5 py-1 text-[11px] sm:text-xs font-mono font-bold text-[#1E4DB7]">
                                                <?= htmlspecialchars($subj['subject_code']) ?>
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-3.5 sm:py-5 min-w-[160px]">
                                            <span class="block font-bold text-slate-800 text-xs sm:text-sm line-clamp-2">
                                                <?= htmlspecialchars($subj['subject_name']) ?>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 sm:px-6 py-3.5 sm:py-5 font-medium text-slate-700 text-xs sm:text-sm">
                                            <span class="flex items-center gap-1.5 sm:gap-2">
                                                <i class="bi bi-person-fill text-slate-400"></i>
                                                <?= teacherName($subj) ?>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 sm:px-6 py-3.5 sm:py-5">
                                            <span class="flex items-center gap-1.5 sm:gap-2 font-medium text-slate-700 text-xs sm:text-sm">
                                                <i class="bi bi-clock text-slate-400"></i>
                                                <?= htmlspecialchars(formatSchedule($subj)) ?>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 sm:px-6 py-3.5 sm:py-5 text-slate-600 text-xs sm:text-sm">
                                            <span class="flex items-center gap-1.5 sm:gap-2">
                                                <i class="bi bi-door-open text-slate-400"></i>
                                                <?= htmlspecialchars($subj['room_name'] ?? 'TBA') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            </section>
        <?php endif; ?>
    </section>
</main>