<?php
require_once "../../config/db.php";
require_once "../includes/auth.php";

$professor_id = currentProfessorId();

include '../includes/header.php';
include '../includes/professor-sidebar.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$professorId   = $_SESSION['professor_id'];
$professorName = $_SESSION['professor_name'] ?? 'Teacher';

$syStmt = $conn->prepare("
    SELECT school_year_id, school_year, curriculum_version_id
    FROM school_years
    WHERE is_active = 1
    LIMIT 1
");
$syStmt->execute();
$activeYear = $syStmt->get_result()->fetch_assoc();
$syStmt->close();

$schoolYearId  = $activeYear['school_year_id'] ?? 0;
$curriculumVer = $activeYear['curriculum_version_id'] ?? 0;

$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT cs.section_id) AS total
    FROM class_schedules cs
    INNER JOIN sections s ON s.section_id = cs.section_id
    WHERE cs.professor_id = ? AND s.school_year_id = ?
");
$stmt->bind_param("ii", $professorId, $schoolYearId);
$stmt->execute();
$sectionCount = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT c.subject_id) AS total
    FROM class_schedules cs
    INNER JOIN curriculum c ON c.curriculum_id = cs.curriculum_id
    WHERE cs.professor_id = ? AND c.curriculum_version_id = ?
");
$stmt->bind_param("ii", $professorId, $curriculumVer);
$stmt->execute();
$subjectCount = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT e.student_id) AS total
    FROM enrollments e
    WHERE e.status = 'Confirmed'
      AND e.section_id IN (
          SELECT DISTINCT cs.section_id
          FROM class_schedules cs
          INNER JOIN sections s ON s.section_id = cs.section_id
          WHERE cs.professor_id = ? AND s.school_year_id = ?
      )
");
$stmt->bind_param("ii", $professorId, $schoolYearId);
$stmt->execute();
$studentCount = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

$todayName = date('l');

$stmt = $conn->prepare("
    SELECT cs.start_time, cs.end_time, sub.subject_name, s.section_name, st.strand_name, r.room_name
    FROM class_schedules cs
    INNER JOIN curriculum c ON c.curriculum_id = cs.curriculum_id
    INNER JOIN subjects sub ON sub.subject_id = c.subject_id
    INNER JOIN sections s ON s.section_id = cs.section_id
    INNER JOIN strands st ON st.strand_id = s.strand_id
    LEFT JOIN rooms r ON r.room_id = cs.room_id
    WHERE cs.professor_id = ?
      AND s.school_year_id = ?
      AND cs.day_of_week LIKE CONCAT('%', ?, '%')
    ORDER BY cs.start_time ASC
");
$stmt->bind_param("iis", $professorId, $schoolYearId, $todayName);
$stmt->execute();
$todaysClasses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function formatTimeRange($start, $end) {
    return date('g:i A', strtotime($start)) . ' - ' . date('g:i A', strtotime($end));
}
?>

<main class="page-transition min-h-screen lg:ml-72 p-6 pt-20 bg-slate-50">
    <div class="mx-auto max-w-7xl space-y-8">
        <header class="rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-6 text-white shadow-xl">
            <span class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-amber-300">
                <i class="bi bi-person-badge-fill"></i>
                Professor Portal
            </span>

            <h1 class="text-3xl font-extrabold tracking-tight text-white">
                Welcome back, <?= htmlspecialchars($professorName) ?>!
            </h1>

            <span class="mt-2 block text-sm text-blue-100">
                Here's what's on today, <?= date('F j, Y') ?>.
            </span>
        </header>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-[#1E4DB7]">
                        <i class="bi bi-book-half text-xl"></i>
                    </div>

                    <span class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Academic
                    </span>
                </div>

                <span class="mt-4 block text-3xl font-extrabold text-slate-800">
                    <?= $subjectCount ?>
                </span>

                <span class="mt-1 block text-sm text-slate-500">
                    Assigned Subjects
                </span>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                        <i class="bi bi-mortarboard-fill text-xl"></i>
                    </div>

                    <span class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Students
                    </span>
                </div>

                <span class="mt-4 block text-3xl font-extrabold text-slate-800">
                    <?= $studentCount ?>
                </span>

                <span class="mt-1 block text-sm text-slate-500">
                    Confirmed Students
                </span>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-[#0A1931]">
                        <i class="bi bi-diagram-3-fill text-xl"></i>
                    </div>

                    <span class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Class
                    </span>
                </div>

                <span class="mt-4 block text-3xl font-extrabold text-slate-800">
                    <?= $sectionCount ?>
                </span>

                <span class="mt-1 block text-sm text-slate-500">
                    Assigned Sections
                </span>
            </section>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex flex-col gap-2 border-b border-white/10 bg-[#0A1931] px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <section>
                    <h2 class="text-2xl font-bold text-white">
                        Today's Schedule
                    </h2>

                    <span class="mt-1 block text-sm text-blue-100">
                        Your scheduled classes for today.
                    </span>
                </section>

                <span class="inline-flex w-fit items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-blue-100">
                    <i class="bi bi-calendar3"></i>
                    <?= htmlspecialchars($todayName) ?>
                </span>
            </header>

            <?php if (empty($todaysClasses)): ?>
                <div class="p-12 text-center">
                    <i class="bi bi-cup-hot mb-3 block text-4xl text-slate-300"></i>

                    <h3 class="text-lg font-bold text-slate-700">
                        No classes scheduled for today.
                    </h3>

                    <span class="mt-1 block text-sm text-slate-500">
                        Your schedule will appear here when you have classes assigned.
                    </span>
                </div>
            <?php else: ?>
                <section class="overflow-x-auto">
                    <table class="w-full min-w-[700px]">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">
                                    Time
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">
                                    Subject
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">
                                    Section
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">
                                    Room
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200">
                            <?php foreach ($todaysClasses as $class): ?>
                                <tr class="transition hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <span class="flex items-center gap-2 text-sm font-medium text-slate-700">
                                            <i class="bi bi-clock text-slate-400"></i>
                                            <?= htmlspecialchars(formatTimeRange($class['start_time'], $class['end_time'])) ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-5">
                                        <span class="block font-bold text-slate-800">
                                            <?= htmlspecialchars($class['subject_name']) ?>
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-5">
                                        <span class="flex items-center gap-2 text-sm font-medium text-slate-700">
                                            <i class="bi bi-people-fill text-slate-400"></i>
                                            <?= htmlspecialchars($class['strand_name'] . ' ' . $class['section_name']) ?>
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-5">
                                        <span class="flex items-center gap-2 text-sm text-slate-600">
                                            <i class="bi bi-door-open text-slate-400"></i>
                                            <?= htmlspecialchars($class['room_name'] ?? 'TBA') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>
        </section>
    </div>
</main>

<script src="/enrollment_system/assets/js/swal.js"></script>