```php
<?php
session_start();

$page_title = "Academic Management - Masinag SHS";

require_once "../../config/db.php";
require_once '../includes/auth.php';

requireRole('Scheduler', 'Super Admin');

require_once "../../controllers/academic_management_data.php";

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="page-transition lg:ml-72 pt-20 min-h-screen p-6 bg-slate-50">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-diagram-2-fill"></i> Class Schedule & Sections
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight">
                    Academic Section Management
                </h1>

                <p class="text-blue-100 mt-1 text-sm max-w-2xl">
                    Configure class sections, auto-load curriculum subjects, and assign teacher schedules.
                </p>
            </div>
            
            <div class="relative z-10">
                <button
                    onclick="openModal('sectionModal')"
                    class="btn-accent px-5 py-3 rounded-xl font-bold text-xs text-slate-900 cursor-pointer shadow-lg transition duration-200 flex items-center gap-2">

                    <i class="bi bi-plus-circle-fill"></i>
                    <span>New Class Section</span>

                </button>
            </div>
        </div>


        <!-- CONTENT MASTER-DETAIL GRID -->
        <div class="grid grid-cols-12 gap-8">

            <!-- SECTIONS SIDEBAR -->
            <div class="col-span-12 lg:col-span-4 space-y-6">

                <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">

                    <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center justify-between">

                        <h2 class="font-bold text-sm tracking-tight flex items-center gap-2">
                            <i class="bi bi-collection-fill text-amber-300"></i>
                            Active Sections
                        </h2>

                        <span class="text-xs bg-white/10 px-2.5 py-0.5 rounded-full text-blue-100 font-medium">
                            <?= count($sections) ?>
                        </span>

                    </div>


                    <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">

                        <?php if (empty($sections)): ?>

                            <div class="p-8 text-center text-xs text-slate-400">

                                <i class="bi bi-inbox text-3xl block mb-2 text-slate-300"></i>

                                No active sections created yet for this school year.

                            </div>

                        <?php endif; ?>


                        <?php foreach ($sections as $s): ?>

                            <?php
                            $isSelected = $s['section_id'] == $selected_section_id;
                            ?>

                            <!-- SECTION SELECTION -->
                            <form
                                method="POST"
                                action="../../controllers/academic_management_select.php">

                                <input
                                    type="hidden"
                                    name="section_id"
                                    value="<?= (int)$s['section_id'] ?>">

                                <input
                                    type="hidden"
                                    name="semester"
                                    value="<?= htmlspecialchars($semester) ?>">


                                <button
                                    type="submit"
                                    class="w-full text-left block p-4 transition duration-200
                                    <?= $isSelected
                                        ? 'bg-blue-50/90 border-l-4 border-blue-700'
                                        : 'hover:bg-slate-50' ?>">

                                    <div class="font-bold text-slate-900 text-sm flex items-center justify-between">

                                        <span>
                                            <?= htmlspecialchars(
                                                $s['strand_name'] . ' ' .
                                                str_replace('Grade ', '', $s['grade_level']) . '-' .
                                                $s['section_name']
                                            ) ?>
                                        </span>

                                        <?php if ($isSelected): ?>

                                            <i class="bi bi-chevron-right text-blue-700 text-xs"></i>

                                        <?php endif; ?>

                                    </div>


                                    <div class="text-xs text-slate-500 mt-1 flex items-center gap-3">

                                        <span>
                                            <i class="bi bi-mortarboard text-slate-400"></i>
                                            <?= htmlspecialchars($s['grade_level']) ?>
                                        </span>

                                        <span>
                                            <i class="bi bi-people text-slate-400"></i>
                                            Max <?= (int)$s['capacity'] ?> Students
                                        </span>

                                    </div>

                                </button>

                            </form>

                        <?php endforeach; ?>

                    </div>


                    <?php if ($section): ?>

                        <div class="p-4 bg-slate-50 border-t border-slate-200">

                            <form
                                id="deleteForm"
                                method="POST"
                                action="../../controllers/academic_management_actions.php">

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete_section">

                                <input
                                    type="hidden"
                                    name="section_id"
                                    value="<?= (int)$section['section_id'] ?>">

                                <button
                                    id="Delete"
                                    type="submit"
                                    class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-4 py-2.5 rounded-xl font-bold text-xs cursor-pointer transition flex items-center justify-center gap-1.5">

                                    <i class="bi bi-trash"></i>
                                    Delete Current Section

                                </button>

                            </form>

                        </div>

                    <?php endif; ?>

                </div>

            </div>


            <!-- DETAIL SCHEDULE PANE -->
            <div class="col-span-12 lg:col-span-8 space-y-6">

                <?php if (!$section): ?>

                    <div class="card-elevated p-12 text-center text-slate-400 bg-white border border-slate-200 space-y-3">

                        <i class="bi bi-arrow-left-circle text-4xl text-blue-400 block"></i>

                        <h3 class="text-base font-bold text-slate-700">
                            Select a Section
                        </h3>

                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Click a section on the left or create a new section to manage curriculum schedules.
                        </p>

                    </div>

                <?php else: ?>


                    <!-- SEMESTER TOGGLE PILLS -->
                    <div class="flex items-center gap-3 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm w-fit">

                        <!-- 1ST SEMESTER -->
                        <form
                            method="POST"
                            action="../../controllers/academic_management_select.php">

                            <input
                                type="hidden"
                                name="section_id"
                                value="<?= (int)$selected_section_id ?>">

                            <input
                                type="hidden"
                                name="semester"
                                value="1st Semester">

                            <button
                                type="submit"
                                class="px-5 py-2 rounded-xl text-xs font-bold transition duration-200
                                <?= $semester === '1st Semester'
                                    ? 'bg-[#0A1931] text-white shadow-sm'
                                    : 'text-slate-600 hover:bg-slate-100' ?>">

                                <i class="bi bi-1-circle me-1"></i>
                                1st Semester

                            </button>

                        </form>


                        <!-- 2ND SEMESTER -->
                        <form
                            method="POST"
                            action="../../controllers/academic_management_select.php">

                            <input
                                type="hidden"
                                name="section_id"
                                value="<?= (int)$selected_section_id ?>">

                            <input
                                type="hidden"
                                name="semester"
                                value="2nd Semester">

                            <button
                                type="submit"
                                class="px-5 py-2 rounded-xl text-xs font-bold transition duration-200
                                <?= $semester === '2nd Semester'
                                    ? 'bg-[#0A1931] text-white shadow-sm'
                                    : 'text-slate-600 hover:bg-slate-100' ?>">

                                <i class="bi bi-2-circle me-1"></i>
                                2nd Semester

                            </button>

                        </form>

                    </div>


                    <!-- ASSIGNED SUBJECTS (READ-ONLY) -->
                    <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">

                        <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">

                            <h3 class="font-bold text-sm tracking-tight flex items-center gap-2">

                                <i class="bi bi-book-half text-amber-300"></i>
                                Curriculum Subjects

                            </h3>

                            <span class="text-xs text-blue-100">

                                <?= htmlspecialchars(
                                    $section['strand_name'] .
                                    ' • ' .
                                    $section['grade_level'] .
                                    ' • ' .
                                    $semester
                                ) ?>

                            </span>

                        </div>


                        <table class="w-full text-left text-sm">

                            <thead class="bg-slate-100 border-b border-slate-200 text-slate-700 font-semibold text-xs">

                                <tr>

                                    <th class="px-6 py-3">
                                        Subject Code
                                    </th>

                                    <th class="px-6 py-3">
                                        Subject Description
                                    </th>

                                    <th class="px-6 py-3 text-center">
                                        Credit Units
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-slate-100">

                                <?php if (empty($scheduleRows)): ?>

                                    <tr>

                                        <td
                                            colspan="3"
                                            class="px-6 py-8 text-center text-xs text-slate-400">

                                            No curriculum subjects found for this configuration.

                                        </td>

                                    </tr>

                                <?php endif; ?>


                                <?php foreach ($scheduleRows as $row): ?>

                                    <tr class="hover:bg-slate-50/80 transition text-xs">

                                        <td class="px-6 py-3.5 font-mono font-bold text-blue-800">

                                            <?= htmlspecialchars($row['subject_code']) ?>

                                        </td>

                                        <td class="px-6 py-3.5 font-medium text-slate-800">

                                            <?= htmlspecialchars($row['subject_name']) ?>

                                        </td>

                                        <td class="px-6 py-3.5 text-center font-bold text-slate-700">

                                            <?= (int)$row['units'] ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>


                    <!-- CLASS SCHEDULE ASSIGNMENT -->
                    <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">

                        <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center justify-between">

                            <h3 class="font-bold text-sm tracking-tight flex items-center gap-2">

                                <i class="bi bi-calendar-week text-amber-300"></i>
                                Assigned Schedule Slots

                            </h3>

                        </div>


                        <div class="overflow-x-auto">

                            <table class="w-full text-left text-sm">

                                <thead class="bg-slate-100 border-b border-slate-200 text-slate-700 font-semibold text-xs">

                                    <tr>

                                        <th class="px-6 py-3">
                                            Subject
                                        </th>

                                        <th class="px-6 py-3">
                                            Day(s)
                                        </th>

                                        <th class="px-6 py-3">
                                            Time
                                        </th>

                                        <th class="px-6 py-3">
                                            Teacher
                                        </th>

                                        <th class="px-6 py-3 text-center">
                                            Status
                                        </th>

                                        <th class="px-6 py-3 text-center">
                                            Action
                                        </th>

                                    </tr>

                                </thead>


                                <tbody class="divide-y divide-slate-100 text-xs">

                                    <?php foreach ($scheduleRows as $row): ?>

                                        <?php
                                        $isComplete =
                                            $row['professor_id'] &&
                                            $row['day_of_week'] &&
                                            $row['start_time'] &&
                                            $row['end_time'];
                                        ?>

                                        <tr class="hover:bg-slate-50/80 transition">

                                            <td class="px-6 py-3.5 font-semibold text-slate-800">

                                                <?= htmlspecialchars($row['subject_name']) ?>

                                            </td>


                                            <td class="px-6 py-3.5 text-slate-600 font-medium">

                                                <?= htmlspecialchars(
                                                    $row['day_of_week'] ?? '—'
                                                ) ?>

                                            </td>


                                            <td class="px-6 py-3.5 font-mono text-slate-600">

                                                <?=
                                                $row['start_time']
                                                    ? htmlspecialchars(
                                                        substr($row['start_time'], 0, 5) .
                                                        ' - ' .
                                                        substr($row['end_time'], 0, 5)
                                                    )
                                                    : '—'
                                                ?>

                                            </td>


                                            <td class="px-6 py-3.5 font-medium text-slate-800">

                                                <?php if ($row['prof_last']): ?>

                                                    <?= htmlspecialchars(
                                                        $row['prof_first'] .
                                                        ' ' .
                                                        $row['prof_last']
                                                    ) ?>

                                                <?php else: ?>

                                                    <span class="text-slate-400">
                                                        Unassigned
                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <td class="px-6 py-3.5 text-center">

                                                <?php if ($isComplete): ?>

                                                    <span class="badge-pill badge-complete">
                                                        Complete
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge-pill badge-pending">
                                                        Unscheduled
                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <td class="px-6 py-3.5 text-center">

                                                <button
                                                    onclick='openScheduleModal(<?= json_encode($row) ?>)'
                                                    class="btn-primary px-3.5 py-1.5 rounded-xl text-xs font-bold cursor-pointer shadow-sm">

                                                    <?= $isComplete
                                                        ? '<i class="bi bi-pencil-square mr-1"></i> Edit'
                                                        : '<i class="bi bi-plus-lg mr-1"></i> Set'
                                                    ?>

                                                </button>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>
</main>


<!-- ADD SECTION MODAL -->
<div
    id="sectionModal"
    class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">

    <div class="bg-white rounded-3xl w-full max-w-md p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200">

        <div class="flex justify-between items-center border-b border-slate-200 pb-4">

            <h3 class="font-extrabold text-xl text-slate-900">
                Create Class Section
            </h3>

            <button
                type="button"
                onclick="closeModal('sectionModal')"
                class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-lg font-bold">

                &times;

            </button>

        </div>


        <form
            method="POST"
            action="../../controllers/academic_management_actions.php"
            class="space-y-4 text-xs">

            <input
                type="hidden"
                name="action"
                value="create_section">


            <div>

                <label class="block font-semibold text-slate-600 mb-1">
                    Section Name
                </label>

                <input
                    name="section_name"
                    required
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm"
                    placeholder="e.g. St. Thomas or Section A">

            </div>


            <div>

                <label class="block font-semibold text-slate-600 mb-1">
                    Grade Level
                </label>

                <select
                    name="grade_level"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white cursor-pointer">

                    <option value="Grade 11">
                        Grade 11
                    </option>

                    <option value="Grade 12">
                        Grade 12
                    </option>

                </select>

            </div>


            <div>

                <label class="block font-semibold text-slate-600 mb-1">
                    Assigned Strand
                </label>

                <select
                    name="strand_id"
                    required
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white cursor-pointer">

                    <option value="" disabled selected>
                        Select Strand
                    </option>

                    <?php foreach ($strands as $st): ?>

                        <option value="<?= (int)$st['strand_id'] ?>">

                            <?= htmlspecialchars($st['strand_name']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div>

                <label class="block font-semibold text-slate-600 mb-1">
                    Student Capacity
                </label>

                <input
                    name="capacity"
                    type="number"
                    value="40"
                    min="1"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm"
                    placeholder="Capacity">

            </div>


            <p class="text-[11px] text-slate-400">

                Curriculum subjects will be auto-populated from the master curriculum for this strand.

            </p>


            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">

                <button
                    type="button"
                    onclick="closeModal('sectionModal')"
                    class="px-5 py-2.5 border border-slate-300 rounded-xl font-semibold text-slate-700 hover:bg-slate-100 cursor-pointer">

                    Cancel

                </button>

                <button
                    type="submit"
                    class="btn-primary px-6 py-2.5 rounded-xl font-bold cursor-pointer shadow-md">

                    Create Section

                </button>

            </div>

        </form>

    </div>

</div>


<!-- SET SCHEDULE MODAL -->
<div
    id="scheduleModal"
    class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">

    <div class="bg-white rounded-3xl w-full max-w-lg p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200">

        <div class="flex justify-between items-center border-b border-slate-200 pb-4">

            <div>

                <h3 class="font-extrabold text-xl text-slate-900">
                    Set Subject Schedule
                </h3>

                <p
                    id="scheduleModalSubject"
                    class="text-xs text-blue-700 font-semibold mt-0.5">
                </p>

            </div>


            <button
                type="button"
                onclick="closeModal('scheduleModal')"
                class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-lg font-bold">

                &times;

            </button>

        </div>


        <form
            method="POST"
            action="../../controllers/academic_management_actions.php"
            class="space-y-4 text-xs">

            <input
                type="hidden"
                name="action"
                value="set_schedule">

            <input
                type="hidden"
                name="schedule_id"
                id="sched_schedule_id">

            <input
                type="hidden"
                name="section_id"
                value="<?= (int)$selected_section_id ?>">

            <input
                type="hidden"
                name="semester"
                value="<?= htmlspecialchars($semester) ?>">


            <div>

                <label class="block font-semibold text-slate-600 mb-1">
                    Faculty / Teacher
                </label>

                <select
                    name="professor_id"
                    id="sched_professor_id"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white cursor-pointer">

                    <option value="" disabled selected>
                        Select Teacher
                    </option>

                    <?php foreach ($professors as $p): ?>

                        <option value="<?= (int)$p['professor_id'] ?>">

                            <?= htmlspecialchars(
                                $p['first_name'] . ' ' . $p['last_name']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div>

                <label class="block font-semibold text-slate-600 mb-1.5">
                    Days of Week
                </label>

                <div class="grid grid-cols-3 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200">

                    <?php foreach (
                        ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']
                        as $d
                    ): ?>

                        <label class="cursor-pointer flex items-center gap-1.5 text-xs text-slate-700 font-medium">

                            <input
                                type="checkbox"
                                name="day_of_week[]"
                                value="<?= $d ?>"
                                class="sched-day-checkbox rounded text-blue-600">

                            <?= substr($d, 0, 3) ?>

                        </label>

                    <?php endforeach; ?>

                </div>

            </div>


            <div class="grid grid-cols-2 gap-3">

                <div>

                    <label class="block font-semibold text-slate-600 mb-1">
                        Start Time
                    </label>

                    <input
                        type="time"
                        name="start_time"
                        id="sched_start"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">

                </div>


                <div>

                    <label class="block font-semibold text-slate-600 mb-1">
                        End Time
                    </label>

                    <input
                        type="time"
                        name="end_time"
                        id="sched_end"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">

                </div>

            </div>


            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">

                <button
                    type="button"
                    onclick="closeModal('scheduleModal')"
                    class="px-5 py-2.5 border border-slate-300 rounded-xl font-semibold text-slate-700 hover:bg-slate-100 cursor-pointer">

                    Cancel

                </button>

                <button
                    type="submit"
                    class="btn-primary px-6 py-2.5 rounded-xl font-bold cursor-pointer shadow-md">

                    Save Schedule

                </button>

            </div>

        </form>

    </div>

</div>


<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/cs-validation/academic-management-validation.js"></script>


<script>

function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}


function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}


function openScheduleModal(row) {

    document.getElementById('scheduleModalSubject').innerText =
        row.subject_code + ' — ' + row.subject_name;


    document.getElementById('sched_schedule_id').value =
        row.schedule_id;


    document.getElementById('sched_professor_id').value =
        row.professor_id || '';


    document.querySelectorAll(".sched-day-checkbox").forEach(cb => {
        cb.checked = false;
    });


    if (row.day_of_week) {

        row.day_of_week.split(",").forEach(day => {

            const checkbox = document.querySelector(
                '.sched-day-checkbox[value="' + day.trim() + '"]'
            );

            if (checkbox) {
                checkbox.checked = true;
            }

        });

    }


    document.getElementById('sched_start').value =
        row.start_time
            ? row.start_time.substring(0, 5)
            : '';


    document.getElementById('sched_end').value =
        row.end_time
            ? row.end_time.substring(0, 5)
            : '';


    openModal('scheduleModal');

}

</script>


<?php if (isset($_GET['success'])): ?>

<script>

    Swal.fire({
        title: 'Action Completed!',
        text: 'Class section changes saved successfully.',
        icon: 'success',
        confirmButtonColor: '#0A1931',
        confirmButtonText: 'Done'
    }).then(() => {

        window.history.replaceState(
            {},
            document.title,
            window.location.pathname
        );

    });

</script>

<?php endif; ?>
```
