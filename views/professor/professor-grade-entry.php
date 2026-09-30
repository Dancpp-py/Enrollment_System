<?php

require_once "../../config/db.php";
require_once "../includes/auth.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$professor_id = currentProfessorId();

$stmt = $conn->prepare("
    SELECT
        cs.schedule_id,
        cs.day_of_week,
        cs.start_time,
        cs.end_time,
        sub.subject_code,
        sub.subject_name,
        sub.units,
        sec.section_id,
        sec.section_name,
        sec.grade_level,
        str.strand_name,
        c.semester,
        r.room_name,
        (
            SELECT COUNT(*)
            FROM enrollments e
            WHERE e.section_id = cs.section_id
              AND e.status = 'Confirmed'
              AND e.stage = 'Enrolled'
        ) AS student_count,
        (
            SELECT COUNT(*)
            FROM enrollments e
            INNER JOIN grades g
                ON g.enrollment_id = e.enrollment_id
               AND g.schedule_id = cs.schedule_id
            WHERE e.section_id = cs.section_id
              AND e.status = 'Confirmed'
              AND e.stage = 'Enrolled'
              AND g.final_grade IS NOT NULL
        ) AS graded_count
    FROM class_schedules cs
    INNER JOIN curriculum c
        ON c.curriculum_id = cs.curriculum_id
    INNER JOIN subjects sub
        ON sub.subject_id = c.subject_id
    INNER JOIN sections sec
        ON sec.section_id = cs.section_id
    INNER JOIN strands str
        ON str.strand_id = sec.strand_id
    INNER JOIN school_years sy
        ON sy.school_year_id = sec.school_year_id
    LEFT JOIN rooms r
        ON r.room_id = cs.room_id
    WHERE cs.professor_id = ?
      AND sy.is_active = 1
    ORDER BY
        c.semester,
        str.strand_name,
        sec.grade_level,
        sec.section_name,
        sub.subject_name
");

$stmt->bind_param("i", $professor_id);
$stmt->execute();

$classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();

function sectionLabel($row)
{
    return $row['strand_name']
        . ' '
        . str_replace('Grade ', '', $row['grade_level'])
        . '-'
        . $row['section_name'];
}

function formatTimeRange($start, $end)
{
    if (!$start || !$end) {
        return 'No schedule set';
    }

    return date('g:i A', strtotime($start))
        . ' - '
        . date('g:i A', strtotime($end));
}

include '../includes/header.php';
include '../includes/professor-sidebar.php';
?>

<main class="lg:ml-72 pt-20 min-h-screen p-6">
    <section class="mx-auto max-w-7xl space-y-8">

        <header class="rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-6 text-white shadow-xl">
            <span class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-amber-300">
                <i class="bi bi-journal-bookmark-fill"></i>
                My Classes
            </span>

            <h1 class="text-3xl font-extrabold tracking-tight text-white">
                Grade Encoding
            </h1>

            <span class="mt-1 block text-sm text-blue-100">
                Select a class to view its students and encode their grades.
            </span>
        </header>

        <?php if (empty($classes)): ?>

            <section class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                <i class="bi bi-calendar-x mb-3 block text-4xl text-slate-300"></i>

                <h3 class="text-lg font-bold text-slate-700">
                    No classes assigned
                </h3>

                <span class="mt-1 block text-sm text-slate-500">
                    You don't have any classes assigned for the active school year yet.
                </span>
            </section>

        <?php else: ?>

            <section class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">

                <?php foreach ($classes as $class): ?>

                    <?php
                    $total = (int) $class['student_count'];
                    $graded = (int) $class['graded_count'];
                    $complete = $total > 0 && $graded === $total;
                    ?>

                    <article class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">

                        <header class="bg-[#0A1931] px-5 py-4">

                            <span class="text-xs font-bold uppercase tracking-wider text-amber-300">
                                <?= htmlspecialchars($class['subject_code']) ?>
                            </span>

                            <h2 class="mt-1 text-lg font-bold text-white">
                                <?= htmlspecialchars($class['subject_name']) ?>
                            </h2>

                            <span class="mt-1 block text-sm text-blue-100">
                                <?= htmlspecialchars(sectionLabel($class)) ?>
                                •
                                <?= htmlspecialchars($class['semester']) ?>
                            </span>

                        </header>

                        <section class="flex-1 space-y-3 p-5 text-sm text-slate-600">

                            <div class="flex items-center gap-2">
                                <i class="bi bi-clock text-slate-400"></i>

                                <span>
                                    <?= htmlspecialchars($class['day_of_week'] ?: 'No day set') ?>
                                    •
                                    <?= htmlspecialchars(formatTimeRange($class['start_time'], $class['end_time'])) ?>
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <i class="bi bi-door-closed text-slate-400"></i>

                                <span>
                                    <?= htmlspecialchars($class['room_name'] ?: 'No room set') ?>
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <i class="bi bi-people text-slate-400"></i>

                                <span>
                                    <?= $total ?>
                                    student<?= $total === 1 ? '' : 's' ?>
                                </span>
                            </div>

                            <div class="pt-1">

                                <?php if ($total === 0): ?>

                                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        No students yet
                                    </span>

                                <?php elseif ($complete): ?>

                                    <span class="inline-flex items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                                        <i class="bi bi-check-circle-fill"></i>
                                        All grades encoded
                                    </span>

                                <?php else: ?>

                                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                        <?= $graded ?> of <?= $total ?> encoded
                                    </span>

                                <?php endif; ?>

                            </div>

                        </section>

                        <footer class="border-t border-slate-200 bg-slate-50 px-5 py-4">

                            <form
                                method="POST"
                                action="professor-grade-entry-v2"
                            >
                                <input
                                    type="hidden"
                                    name="schedule_id"
                                    value="<?= (int) $class['schedule_id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="inline-flex cursor-pointer w-full items-center justify-center gap-2 rounded-xl bg-[#1E4DB7] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                    Encode Grades
                                </button>
                            </form>

                        </footer>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php endif; ?>

    </section>
</main>

<script src="/enrollment_system/assets/js/swal.js"></script>