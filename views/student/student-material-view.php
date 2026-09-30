<?php

require_once '../../config/db.php';
require_once '../includes/auth.php';

requireStudentLogin();

$student_id = (int) $_SESSION['student_id'];
$lms_class_id = (int) ($_GET['lms_class_id'] ?? 0);

if ($lms_class_id <= 0) {
    http_response_code(400);
    die('Invalid subject.');
}

$stmt = $conn->prepare("
    SELECT
        lc.lms_class_id,
        lc.schedule_id,
        lc.status AS lms_class_status,
        cs.section_id,
        cs.day_of_week,
        cs.start_time,
        cs.end_time,
        sub.subject_id,
        sub.subject_code,
        sub.subject_name,
        sub.units,
        p.first_name AS prof_first,
        p.last_name AS prof_last,
        r.room_name
    FROM lms_classes lc
    INNER JOIN class_schedules cs
        ON cs.schedule_id = lc.schedule_id
    INNER JOIN curriculum c
        ON c.curriculum_id = cs.curriculum_id
    INNER JOIN subjects sub
        ON sub.subject_id = c.subject_id
    INNER JOIN enrollments e
        ON e.section_id = cs.section_id
    LEFT JOIN professors p
        ON p.professor_id = cs.professor_id
    LEFT JOIN rooms r
        ON r.room_id = cs.room_id
    WHERE lc.lms_class_id = ?
      AND e.student_id = ?
      AND e.status = 'Confirmed'
      AND e.stage = 'Enrolled'
    ORDER BY e.enrollment_id DESC
    LIMIT 1
");

$stmt->bind_param("ii", $lms_class_id, $student_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$class) {
    http_response_code(404);
    die('Subject not found or you are not authorized to access this subject.');
}

$materials = [];

$stmt = $conn->prepare("
    SELECT
        material_id,
        title,
        description,
        original_file_name,
        file_type,
        file_size,
        created_at
    FROM lms_materials
    WHERE lms_class_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $lms_class_id);
$stmt->execute();
$materials = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function formatFileSize($bytes)
{
    $bytes = (int) $bytes;

    if ($bytes <= 0) {
        return 'Unknown size';
    }

    if ($bytes < 1024) {
        return $bytes . ' B';
    }

    if ($bytes < 1024 * 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    }

    if ($bytes < 1024 * 1024 * 1024) {
        return number_format($bytes / (1024 * 1024), 1) . ' MB';
    }

    return number_format($bytes / (1024 * 1024 * 1024), 1) . ' GB';
}

function getFileIcon($fileName, $fileType = '')
{
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    switch ($extension) {
        case 'pdf':
            return 'bi-file-earmark-pdf-fill';
        case 'doc':
        case 'docx':
            return 'bi-file-earmark-word-fill';
        case 'xls':
        case 'xlsx':
            return 'bi-file-earmark-excel-fill';
        case 'ppt':
        case 'pptx':
            return 'bi-file-earmark-ppt-fill';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'webp':
            return 'bi-file-earmark-image-fill';
        case 'zip':
        case 'rar':
            return 'bi-file-earmark-zip-fill';
        case 'txt':
            return 'bi-file-earmark-text-fill';
        default:
            return 'bi-file-earmark-fill';
    }
}

function getFileTypeLabel($fileName)
{
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($extension === '') {
        return 'FILE';
    }

    return strtoupper($extension);
}

function formatMaterialDate($date)
{
    if (empty($date)) {
        return '';
    }

    return date('M d, Y • g:i A', strtotime($date));
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
    $result = [];

    foreach ($days as $day) {
        $result[] = $map[$day] ?? $day;
    }

    return implode('', $result);
}

function formatSchedule($class)
{
    if (
        empty($class['day_of_week']) ||
        empty($class['start_time']) ||
        empty($class['end_time'])
    ) {
        return 'Schedule TBA';
    }

    return abbreviateDays($class['day_of_week']) . ' ' .
        date('g:i A', strtotime($class['start_time'])) .
        ' - ' .
        date('g:i A', strtotime($class['end_time']));
}

function professorName($class)
{
    if (empty($class['prof_first'])) {
        return 'Teacher TBA';
    }

    return trim($class['prof_first'] . ' ' . $class['prof_last']);
}

include '../includes/header.php';
include '../includes/student-navbar.php';
include '../includes/student-sidebar.php';

?>

<main class="page-transition min-h-screen px-4 py-6 sm:p-6 pt-16 sm:pt-20 lg:ml-72">
    <section class="mx-auto max-w-7xl space-y-6 sm:space-y-8">
        <header class="overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] text-white shadow-xl">
            <div class="p-4 sm:p-8">
                <div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="space-y-1">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-2.5 py-0.5 sm:px-3 sm:py-1 text-xs font-bold text-amber-300">
                            <i class="bi bi-book-half"></i>
                            Subject Materials
                        </span>

                        <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight break-words">
                            <?= htmlspecialchars($class['subject_name']) ?>
                        </h1>

                        <?php if (!empty($class['subject_code'])): ?>
                            <span class="block text-xs sm:text-sm font-semibold text-blue-100">
                                <?= htmlspecialchars($class['subject_code']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="flex h-12 w-12 sm:h-16 sm:w-16 shrink-0 items-center justify-center rounded-xl sm:rounded-2xl border border-white/10 bg-white/10 text-amber-300">
                        <i class="bi bi-journal-bookmark-fill text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 bg-black/10 px-4 py-3 sm:px-8 sm:py-4">
                <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs sm:text-sm text-blue-100">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-person-fill text-amber-300"></i>
                        <?= htmlspecialchars(professorName($class)) ?>
                    </span>

                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-clock-fill text-amber-300"></i>
                        <?= htmlspecialchars(formatSchedule($class)) ?>
                    </span>

                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-door-open-fill text-amber-300"></i>
                        <?= htmlspecialchars($class['room_name'] ?? 'TBA') ?>
                    </span>

                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-file-earmark-fill text-amber-300"></i>
                        <?= count($materials) ?> material<?= count($materials) !== 1 ? 's' : '' ?>
                    </span>
                </div>
            </div>
        </header>

        <?php if ($class['lms_class_status'] !== 'Active'): ?>
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5">
                <div class="flex items-start gap-3 sm:gap-4">
                    <div class="flex h-9 w-9 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600 text-lg sm:text-xl">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>

                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-amber-800">
                            Subject currently unavailable
                        </h2>

                        <span class="mt-1 block text-xs sm:text-sm text-amber-700 leading-relaxed">
                            Your professor's LMS class is currently not active.
                            The materials cannot be accessed at this time.
                        </span>
                    </div>
                </div>
            </section>
        <?php elseif (empty($materials)): ?>
            <section class="rounded-2xl border border-slate-200 bg-white px-4 py-8 sm:p-12 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 sm:h-16 sm:w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <i class="bi bi-folder2-open text-2xl sm:text-3xl"></i>
                </div>

                <h2 class="mt-4 sm:mt-5 text-lg sm:text-xl font-bold text-slate-700">
                    No learning materials yet
                </h2>

                <span class="mx-auto mt-1 sm:mt-2 block max-w-md text-xs sm:text-sm text-slate-500">
                    Your professor has not uploaded any learning materials
                    for this subject yet.
                </span>
            </section>
        <?php else: ?>
            <section class="space-y-4 sm:space-y-5">
                <div class="flex flex-row items-center justify-between gap-2">
                    <div>
                        <h2 class="text-lg sm:text-2xl font-bold text-slate-800">
                            Learning Materials
                        </h2>

                        <span class="hidden sm:block mt-1 text-sm text-slate-500">
                            Materials uploaded by your professor for this subject.
                        </span>
                    </div>

                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-blue-50 px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-bold text-[#1E4DB7]">
                        <i class="bi bi-files"></i>
                        <?= count($materials) ?> file<?= count($materials) !== 1 ? 's' : '' ?>
                    </span>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($materials as $material): ?>
                            <?php
                                $fileName = $material['original_file_name'] ?: $material['title'];
                                $fileType = getFileTypeLabel($fileName);
                                $fileIcon = getFileIcon(
                                    $fileName,
                                    $material['file_type']
                                );
                            ?>

                            <article class="group p-4 sm:p-5 transition hover:bg-slate-50">
                                <div class="flex flex-col gap-4 sm:gap-5 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="flex min-w-0 items-start gap-3 sm:gap-4">
                                        <div class="flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#1E4DB7]">
                                            <i class="bi <?= htmlspecialchars($fileIcon) ?> text-xl sm:text-2xl"></i>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <h3 class="break-words text-sm sm:text-base font-bold text-slate-800">
                                                <?= htmlspecialchars($material['title']) ?>
                                            </h3>

                                            <div class="mt-1 flex flex-wrap items-center gap-x-2 sm:gap-x-3 gap-y-1 text-xs text-slate-500">
                                                <span class="inline-flex items-center gap-1 font-bold text-[#1E4DB7]">
                                                    <i class="bi bi-file-earmark"></i>
                                                    <?= htmlspecialchars($fileType) ?>
                                                </span>

                                                <span>
                                                    <?= htmlspecialchars(formatFileSize($material['file_size'])) ?>
                                                </span>

                                                <span class="hidden xs:inline">•</span>

                                                <span>
                                                    <?= htmlspecialchars(formatMaterialDate($material['created_at'])) ?>
                                                </span>
                                            </div>

                                            <?php if (!empty($material['description'])): ?>
                                                <p class="mt-2 max-w-3xl text-xs sm:text-sm leading-relaxed text-slate-500 break-words">
                                                    <?= nl2br(htmlspecialchars($material['description'])) ?>
                                                </p>
                                            <?php endif; ?>

                                            <span class="mt-2 block truncate text-xs text-slate-400">
                                                <?= htmlspecialchars($fileName) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 pt-2 sm:pt-0 border-t sm:border-0 border-slate-100">
                                        <a
                                            href="student-file-view.php?id=<?= (int) $material['material_id'] ?>"
                                            target="_blank"
                                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 rounded-xl bg-[#1E4DB7] px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-bold text-white transition hover:bg-[#132A52]"
                                        >
                                            <i class="bi bi-eye-fill"></i>
                                            View
                                        </a>

                                        <a
                                            href="student-file-view.php?id=<?= (int) $material['material_id'] ?>&download=1"
                                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm font-bold text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-[#1E4DB7]"
                                        >
                                            <i class="bi bi-download"></i>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </section>
</main>