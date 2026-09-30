<?php
require_once "../../config/db.php";
require_once "../includes/auth.php";

$professor_id = currentProfessorId();

// ---------------------------------------------------------------
// Active school year
// ---------------------------------------------------------------
$activeYear = null;
$result = $conn->query("
    SELECT school_year_id, school_year
    FROM school_years
    WHERE is_active = 1
    LIMIT 1
");
if ($result) {
    $activeYear = $result->fetch_assoc();
}

$school_year_id = (int) ($activeYear['school_year_id'] ?? 0);

// ---------------------------------------------------------------
// Every schedule row assigned to this professor for the active
// school year, with the subject, section, strand, room, timeslot,
// and a live count of enrolled students in that section/semester.
// ---------------------------------------------------------------
$assignments = [];

if ($school_year_id > 0) {
    $stmt = $conn->prepare("
        SELECT
            cs.schedule_id,
            cs.day_of_week,
            cs.start_time,
            cs.end_time,
            s.subject_code,
            s.subject_name,
            s.units,
            c.semester,
            c.grade_level,
            sec.section_id,
            sec.section_name,
            st.strand_name,
            r.room_name,
            (
                SELECT COUNT(*)
                FROM enrollments e
                WHERE e.section_id       = sec.section_id
                  AND e.semester         = c.semester
                  AND e.school_year_id   = sec.school_year_id
                  AND e.status           = 'Confirmed'
                  AND e.stage            = 'Enrolled'
            ) AS student_count
        FROM class_schedules cs
        JOIN curriculum c  ON c.curriculum_id = cs.curriculum_id
        JOIN subjects   s  ON s.subject_id    = c.subject_id
        JOIN sections   sec ON sec.section_id  = cs.section_id
        JOIN strands    st ON st.strand_id    = sec.strand_id
        LEFT JOIN rooms r  ON r.room_id       = cs.room_id
        WHERE cs.professor_id = ?
          AND sec.school_year_id = ?
        ORDER BY c.semester, s.subject_code, sec.section_name
    ");
    $stmt->bind_param("ii", $professor_id, $school_year_id);
    $stmt->execute();
    $assignments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

/**
 * Turn a pair of TIME columns into "7:30 AM - 9:00 AM".
 */
function formatTimeRange(?string $start, ?string $end): string
{
    if (empty($start) || empty($end)) {
        return 'Not set';
    }

    return date('g:i A', strtotime($start)) . ' - ' . date('g:i A', strtotime($end));
}

include '../includes/header.php';
include '../includes/professor-sidebar.php';
?>

<main class="min-h-screen lg:ml-72 p-6 pt-20">
    <section class="mx-auto max-w-7xl space-y-8">

        <!-- HEADER -->
        <header class="rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-6 text-white shadow-xl">
            <section>
                <span class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-amber-300">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    Teacher Portal
                </span>

                <h1 class="text-3xl font-extrabold tracking-tight text-white">
                    My Subjects & Sections
                </h1>

                <span class="mt-1 block max-w-2xl text-sm text-blue-100">
                    View your assigned subjects and the students enrolled in each class.
                    <?php if ($activeYear): ?>
                        Showing School Year <?= htmlspecialchars($activeYear['school_year']) ?>.
                    <?php endif; ?>
                </span>
            </section>
        </header>

        <!-- SUBJECT TABLE -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <!-- TABLE HEADER -->
            <header class="flex flex-col gap-4 border-b border-white/10 bg-[#0A1931] px-6 py-5 md:flex-row md:items-center md:justify-between">

                <section>
                    <h2 class="text-2xl font-bold text-white">
                        My Subjects & Sections
                    </h2>

                    <span class="mt-1 block text-sm text-blue-100">
                        Select a subject to view the students in that class.
                    </span>
                </section>

                <section class="relative w-full md:w-72">
                    <label for="subject-search" class="sr-only">
                        Search Subject
                    </label>

                    <input
                        id="subject-search"
                        type="text"
                        placeholder="Search Subject..."
                        autocomplete="off"
                        class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-slate-700 outline-none focus:ring-2 focus:ring-blue-500"
                    >

                    <i class="bi bi-search absolute left-3 top-4 text-slate-400"></i>
                </section>

            </header>

            <!-- TABLE -->
            <section class="overflow-x-auto">
                <table class="w-full min-w-[900px]">

                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Subject Code
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Subject
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Section
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Semester
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Students
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Schedule
                            </th>

                            <th class="px-6 py-4 text-center text-sm font-semibold text-slate-600">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody id="subjectTableBody">
                        <?php if (empty($assignments)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    <i class="bi bi-journal-x mb-3 block text-4xl text-slate-300"></i>
                                    <?php if (!$activeYear): ?>
                                        No active school year has been set yet.
                                    <?php else: ?>
                                        You have no subjects assigned for this school year.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($assignments as $row): ?>
                                <?php
                                    $sectionLabel = $row['strand_name'] . ' '
                                        . str_replace('Grade ', '', $row['grade_level']) . '-'
                                        . $row['section_name'];
                                ?>
                                <tr class="subject-row border-t border-slate-200 transition hover:bg-slate-50"
                                    data-search="<?= htmlspecialchars(strtolower($row['subject_code'] . ' ' . $row['subject_name'] . ' ' . $sectionLabel)) ?>">

                                    <td class="px-6 py-4 text-sm font-semibold text-slate-800">
                                        <?= htmlspecialchars($row['subject_code']) ?>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        <?= htmlspecialchars($row['subject_name']) ?>
                                        <span class="mt-0.5 block text-xs text-slate-400">
                                            <?= (int) $row['units'] ?> units
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        <?= htmlspecialchars($sectionLabel) ?>
                                        <?php if (!empty($row['room_name'])): ?>
                                            <span class="mt-0.5 block text-xs text-slate-400">
                                                <?= htmlspecialchars($row['room_name']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        <?= htmlspecialchars($row['semester']) ?>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-700">
                                            <?= (int) $row['student_count'] ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        <?= htmlspecialchars($row['day_of_week'] ?: 'Not set') ?>
                                        <span class="mt-0.5 block text-xs text-slate-400">
                                            <?= htmlspecialchars(formatTimeRange($row['start_time'], $row['end_time'])) ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <button
                                            type="button"
                                            class="view-students-btn inline-flex items-center justify-center rounded-xl bg-[#1E4DB7] px-5 py-2.5 font-semibold text-white shadow-sm transition hover:bg-[#132A52]"
                                            data-schedule-id="<?= (int) $row['schedule_id'] ?>"
                                            data-subject="<?= htmlspecialchars($row['subject_code'] . ' — ' . $row['subject_name']) ?>"
                                            data-section="<?= htmlspecialchars($sectionLabel . ' • ' . $row['semester']) ?>"
                                        >
                                            <i class="bi bi-eye-fill mr-2"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <tr id="noSearchResults" class="hidden">
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                No subjects match your search.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </section>
    </section>
</main>

<!-- STUDENT LIST MODAL -->
<div id="studentsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4 backdrop-blur-sm transition-all">
    <div class="flex max-h-[90vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <!-- MODAL HEADER -->
        <header class="flex items-center justify-between gap-4 bg-[#0A1931] px-8 py-6 text-white shadow-md">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 text-xl text-blue-200">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h2 id="modalSubjectTitle" class="text-xl font-bold tracking-wide text-white">
                        Students List
                    </h2>
                    <span id="modalSectionSubtitle" class="mt-0.5 block text-sm font-medium text-blue-200"></span>
                </div>
            </div>

            <button type="button" id="closeStudentsModal" class="flex h-9 w-9 items-center justify-center rounded-lg text-xl text-white/80 transition hover:bg-white/10 hover:text-white">
                &times;
            </button>
        </header>

        <!-- MODAL BODY -->
        <section class="flex-1 overflow-y-auto bg-slate-50/50 p-6">

            <!-- LOADING -->
            <div id="studentsLoading" class="my-12 rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-16 text-center text-slate-500 shadow-sm">
                <i class="bi bi-arrow-repeat mb-3 inline-block animate-spin text-4xl text-blue-600"></i>
                <p class="font-medium text-slate-700">Loading student records...</p>
            </div>

            <!-- ERROR -->
            <div id="studentsError" class="hidden my-12 rounded-2xl border border-red-100 bg-red-50/50 px-6 py-16 text-center text-red-600 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill mb-3 inline-block text-4xl text-red-500"></i>
                <p id="studentsErrorText" class="font-medium">Something went wrong while fetching data.</p>
            </div>

            <!-- EMPTY -->
            <div id="studentsEmpty" class="hidden my-12 rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-16 text-center text-slate-500 shadow-sm">
                <i class="bi bi-people mb-3 inline-block text-5xl text-slate-300"></i>
                <p class="font-medium text-slate-700">No students are enrolled in this class yet.</p>
            </div>

            <!-- TABLE -->
            <div id="studentsTableWrapper" class="hidden overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full min-w-[750px] border-collapse text-left">
                    <thead class="sticky top-0 bg-slate-100/80 backdrop-blur-md">
                        <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-600">
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Student No.</th>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Gender</th>
                            <th class="px-6 py-4">Contact</th>
                            <th class="px-6 py-4">Email</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody" class="divide-y divide-slate-100 text-sm text-slate-700"></tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/professor_subject.js"></script>