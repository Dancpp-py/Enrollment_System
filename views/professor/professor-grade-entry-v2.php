<?php

require_once "../../config/db.php";
require_once "../includes/auth.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$professor_id = currentProfessorId();

if (isset($_POST['schedule_id'])) {

    $posted_schedule_id = (int) $_POST['schedule_id'];

    if ($posted_schedule_id <= 0) {
        header("Location: professor-grade-entry");
        exit();
    }

    // Verify that this schedule belongs to the logged-in professor
    // before storing it in the session.
    $stmt = $conn->prepare("
        SELECT cs.schedule_id
        FROM class_schedules cs
        INNER JOIN sections sec
            ON sec.section_id = cs.section_id
        INNER JOIN school_years sy
            ON sy.school_year_id = sec.school_year_id
        WHERE cs.schedule_id = ?
          AND cs.professor_id = ?
          AND sy.is_active = 1
        LIMIT 1
    ");

    $stmt->bind_param(
        "ii",
        $posted_schedule_id,
        $professor_id
    );

    $stmt->execute();

    $validSchedule = $stmt->get_result()->num_rows > 0;

    $stmt->close();

    if (!$validSchedule) {
        header("Location: professor-grade-entry");
        exit();
    }

    $_SESSION['professor_grade_schedule_id'] = $posted_schedule_id;

    header("Location: professor-grade-entry-v2");
    exit();
}

$schedule_id = (int) (
    $_SESSION['professor_grade_schedule_id'] ?? 0
);

if ($schedule_id <= 0) {
    header("Location: professor-grade-entry");
    exit();
}

// ---------------------------------------------------------------
// Confirm this schedule belongs to the logged-in teacher.
// ---------------------------------------------------------------

$stmt = $conn->prepare("
    SELECT
        cs.schedule_id,
        cs.section_id,
        sub.subject_name,
        sub.subject_code,
        sec.section_name,
        sec.grade_level,
        str.strand_name,
        c.semester
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
    WHERE cs.schedule_id = ?
      AND cs.professor_id = ?
      AND sy.is_active = 1
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $schedule_id,
    $professor_id
);

$stmt->execute();

$class = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$class) {
    unset($_SESSION['professor_grade_schedule_id']);

    header("Location: professor-grade-entry");
    exit();
}

$subjectName = $class['subject_name'];

$sectionName =
    $class['strand_name']
    . ' '
    . str_replace('Grade ', '', $class['grade_level'])
    . '-'
    . $class['section_name'];

// ---------------------------------------------------------------
// Students enrolled in this section, with grades for this schedule.
// ---------------------------------------------------------------

$stmt = $conn->prepare("
    SELECT
        e.enrollment_id,
        s.student_number,
        s.first_name,
        s.last_name,
        g.first_quarter,
        g.second_quarter,
        g.final_grade,
        g.remarks
    FROM enrollments e
    INNER JOIN students s
        ON s.student_id = e.student_id
    LEFT JOIN grades g
        ON g.enrollment_id = e.enrollment_id
       AND g.schedule_id = ?
    WHERE e.section_id = ?
      AND e.status = 'Confirmed'
      AND e.stage = 'Enrolled'
    ORDER BY
        s.last_name,
        s.first_name
");

$stmt->bind_param(
    "ii",
    $schedule_id,
    $class['section_id']
);

$stmt->execute();

$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();

function initials($first, $last)
{
    return strtoupper(
        substr($first, 0, 1) .
        substr($last, 0, 1)
    );
}

function gradeOrDash($value)
{
    return $value !== null
        ? number_format((float) $value, 2)
        : '—';
}

include '../includes/header.php';
?>

<main class="p-6">
    <section class="mx-auto space-y-8">

        <header class="rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-6 text-white shadow-xl">

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <section>

                    <span class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-amber-300">
                        <i class="bi bi-pencil-square"></i>
                        Grade Encoding
                    </span>

                    <h1 class="text-3xl font-extrabold tracking-tight text-white">
                        <?= htmlspecialchars($subjectName) ?>
                    </h1>

                    <span class="mt-1 flex items-center gap-2 text-sm text-blue-100">
                        <i class="bi bi-people-fill"></i>

                        <?= htmlspecialchars($sectionName) ?>
                        •
                        <?= htmlspecialchars($class['semester']) ?>
                    </span>

                </section>

                <a
                    href="professor-grade-entry"
                    class="inline-flex w-fit items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-white/20"
                >
                    <i class="bi bi-arrow-left"></i>
                    Back to Sections
                </a>

            </div>

        </header>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <header class="flex flex-col gap-4 border-b border-white/10 bg-[#0A1931] px-6 py-5 md:flex-row md:items-center md:justify-between">

                <section>

                    <h2 class="text-2xl font-bold text-white">
                        Students
                    </h2>

                    <span class="mt-1 block text-sm text-blue-100">
                        View and encode grades for students in this section.
                    </span>

                </section>

                <section class="relative w-full md:w-72">

                    <label
                        for="student-search"
                        class="sr-only"
                    >
                        Search Student
                    </label>

                    <input
                        id="student-search"
                        type="text"
                        placeholder="Search Student..."
                        class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    >

                    <i class="bi bi-search absolute left-3 top-3.5 text-slate-400"></i>

                </section>

            </header>

            <section class="overflow-x-auto">

                <table class="w-full min-w-[900px]">

                    <thead class="bg-slate-100">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">
                                Student No.
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">
                                Student Name
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-600">
                                First Quarter
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-600">
                                Second Quarter
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-600">
                                Average
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-600">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody
                        id="student-table-body"
                        class="divide-y divide-slate-200"
                    >

                        <?php if (empty($students)): ?>

                            <tr>

                                <td
                                    colspan="6"
                                    class="px-6 py-12 text-center text-slate-500"
                                >
                                    No students are enrolled in this section yet.
                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($students as $student): ?>

                                <?php
                                $fullName =
                                    $student['first_name']
                                    . ' '
                                    . $student['last_name'];
                                ?>

                                <tr class="student-row transition hover:bg-slate-50">

                                    <td class="whitespace-nowrap px-6 py-5">

                                        <span class="font-semibold text-slate-700">
                                            <?= htmlspecialchars($student['student_number'] ?? '—') ?>
                                        </span>

                                    </td>

                                    <td class="px-6 py-5">

                                        <section class="flex items-center gap-3">

                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                                                <?= htmlspecialchars(
                                                    initials(
                                                        $student['first_name'],
                                                        $student['last_name']
                                                    )
                                                ) ?>
                                            </div>

                                            <span class="font-semibold text-slate-800">
                                                <?= htmlspecialchars($fullName) ?>
                                            </span>

                                        </section>

                                    </td>

                                    <td class="px-6 py-5 text-center">

                                        <span class="font-semibold text-slate-700">
                                            <?= gradeOrDash($student['first_quarter']) ?>
                                        </span>

                                    </td>

                                    <td class="px-6 py-5 text-center">

                                        <span class="font-semibold text-slate-700">
                                            <?= gradeOrDash($student['second_quarter']) ?>
                                        </span>

                                    </td>

                                    <td class="px-6 py-5 text-center">

                                        <span class="font-bold text-[#1E4DB7]">
                                            <?= gradeOrDash($student['final_grade']) ?>
                                        </span>

                                    </td>

                                    <td class="px-6 py-5 text-center">

                                        <button
                                            type="button"
                                            class="open-grade-modal inline-flex items-center gap-2 rounded-xl bg-[#1E4DB7] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800"
                                            data-enrollment-id="<?= (int) $student['enrollment_id'] ?>"
                                            data-student-name="<?= htmlspecialchars($fullName, ENT_QUOTES) ?>"
                                            data-student-number="<?= htmlspecialchars($student['student_number'] ?? '', ENT_QUOTES) ?>"
                                            data-midterm="<?= $student['first_quarter'] !== null ? htmlspecialchars($student['first_quarter']) : '' ?>"
                                            data-finals="<?= $student['second_quarter'] !== null ? htmlspecialchars($student['second_quarter']) : '' ?>"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                            Edit Grades
                                        </button>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </section>

            <section
                id="no-student-results"
                class="hidden px-6 py-12 text-center"
            >
                <i class="bi bi-search mb-3 block text-3xl text-slate-300"></i>

                <h3 class="text-lg font-bold text-slate-700">
                    No students found
                </h3>

                <span class="mt-1 block text-sm text-slate-500">
                    Try searching for another student.
                </span>
            </section>

        </section>

    </section>
</main>

<section
    id="grade-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4"
>

    <form
        class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl"
        method="POST"
        action="../../controllers/process_grade_entry.php"
    >

        <input
            type="hidden"
            name="enrollment_id"
            id="modal-enrollment-id"
        >

        <header class="bg-[#0A1931] px-6 py-5">

            <div class="flex items-start justify-between gap-4">

                <section>

                    <span class="text-xs font-bold uppercase tracking-wider text-amber-300">
                        Grade Details
                    </span>

                    <h2
                        class="mt-1 text-2xl font-bold text-white"
                        id="modal-student-name"
                    ></h2>

                    <span class="mt-1 block text-sm text-blue-100">
                        <?= htmlspecialchars($subjectName) ?>
                        •
                        <?= htmlspecialchars($sectionName) ?>
                    </span>

                </section>

                <button
                    type="button"
                    id="close-grade-modal"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-blue-100 transition hover:bg-white/10 hover:text-white"
                >
                    <i class="bi bi-x-lg"></i>
                </button>

            </div>

        </header>

        <section class="space-y-5 p-6">

            <section class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                <div class="grid grid-cols-2 gap-4">

                    <section>

                        <span class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Student No.
                        </span>

                        <span
                            class="mt-1 block font-bold text-slate-800"
                            id="modal-student-number"
                        ></span>

                    </section>

                    <section>

                        <span class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Section
                        </span>

                        <span class="mt-1 block font-bold text-slate-800">
                            <?= htmlspecialchars($sectionName) ?>
                        </span>

                    </section>

                </div>

            </section>

            <section>

                <label
                    for="midterm-grade"
                    class="mb-2 block text-sm font-bold text-slate-700"
                >
                    First Quarter Grade
                </label>

                <input
                    type="number"
                    id="midterm-grade"
                    name="midterm_grade"
                    min="0"
                    max="100"
                    step="0.01"
                    disabled
                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed"
                >

            </section>

            <section>

                <label
                    for="finals-grade"
                    class="mb-2 block text-sm font-bold text-slate-700"
                >
                    Second Quarter Grade
                </label>

                <input
                    type="number"
                    id="finals-grade"
                    name="finals_grade"
                    min="0"
                    max="100"
                    step="0.01"
                    disabled
                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed"
                >

            </section>

            <section class="rounded-xl border border-blue-100 bg-blue-50 p-4">

                <div class="flex items-center justify-between">

                    <span class="font-bold text-slate-700">
                        Average
                    </span>

                    <span
                        id="average-grade"
                        class="text-2xl font-extrabold text-[#1E4DB7]"
                    >
                        —
                    </span>

                </div>

                <span class="mt-1 block text-xs text-slate-500">
                    Automatically calculated from First Quarter and Second Quarter grades.
                </span>

            </section>

        </section>

        <footer class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">

            <button
                type="button"
                id="edit-grade-button"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-100"
            >
                <i class="bi bi-pencil-square"></i>
                Edit Grades
            </button>

            <button
                type="submit"
                id="submit-grade-button"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#1E4DB7] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800"
            >
                <i class="bi bi-check-lg"></i>
                Submit
            </button>

        </footer>

    </form>

</section>

<script src="/enrollment_system/assets/js/professor-grade-entry-v2.js"></script>