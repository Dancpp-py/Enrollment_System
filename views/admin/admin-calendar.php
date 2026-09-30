<?php
session_start();

$page_title = "School Calendar - Masinag SHS";

require_once "../../config/db.php";
require_once '../includes/auth.php';
requireRole('Scheduler', 'Super Admin');

$calendar_result = $conn->query("
    SELECT
        calendar_id,
        title,
        event_type,
        event_date,
        start_time,
        end_time,
        description
    FROM school_calendar
    ORDER BY event_date ASC, start_time ASC, calendar_id ASC
");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="page-transition lg:ml-72 pt-20 p-6 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto space-y-8">

        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-calendar-event-fill"></i>
                    Academic Schedule
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight">
                    School Calendar
                </h1>

                <p class="text-blue-100 mt-1 text-sm max-w-2xl">
                    Manage important academic dates, examinations, school events, deadlines, and holidays.
                </p>
            </div>

            <div class="relative z-10">
                <button
                    type="button"
                    id="openCalendarModal"
                    class="btn-accent px-5 py-3 rounded-xl font-bold text-xs text-slate-900 cursor-pointer shadow-lg transition duration-200 flex items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Add Calendar Event</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            <div class="card-elevated bg-white border border-slate-200/80 rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            Total Events
                        </p>

                        <p class="text-2xl font-extrabold text-slate-900 mt-1">
                            <?= $calendar_result ? $calendar_result->num_rows : 0 ?>
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                        <i class="bi bi-calendar3 text-xl"></i>
                    </div>
                </div>
            </div>

            <?php
            $exam_count = 0;
            $school_event_count = 0;

            if ($calendar_result) {
                $calendar_result->data_seek(0);

                while ($calendar = $calendar_result->fetch_assoc()) {
                    if ($calendar['event_type'] === 'Exam') {
                        $exam_count++;
                    }

                    if ($calendar['event_type'] === 'School Event') {
                        $school_event_count++;
                    }
                }

                $calendar_result->data_seek(0);
            }
            ?>

            <div class="card-elevated bg-white border border-slate-200/80 rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            Scheduled Exams
                        </p>

                        <p class="text-2xl font-extrabold text-slate-900 mt-1">
                            <?= $exam_count ?>
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="bi bi-journal-check text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="card-elevated bg-white border border-slate-200/80 rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500">
                            School Events
                        </p>

                        <p class="text-2xl font-extrabold text-slate-900 mt-1">
                            <?= $school_event_count ?>
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="bi bi-calendar2-event text-xl"></i>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">

            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-calendar-check text-amber-300"></i>

                    <h2 class="font-bold text-base tracking-tight">
                        Calendar Events
                    </h2>
                </div>

                <span class="text-xs bg-white/10 px-3 py-1 rounded-full text-blue-100 font-medium">
                    <?= $calendar_result ? $calendar_result->num_rows : 0 ?> Events
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">

                    <thead class="bg-slate-100 border-b border-slate-200 text-slate-700 font-semibold text-xs">
                        <tr>
                            <th class="px-6 py-3.5">
                                Date
                            </th>

                            <th class="px-6 py-3.5">
                                Event
                            </th>

                            <th class="px-6 py-3.5">
                                Type
                            </th>

                            <th class="px-6 py-3.5">
                                Time
                            </th>

                            <th class="px-6 py-3.5 text-center">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-xs">

                        <?php if ($calendar_result && $calendar_result->num_rows > 0): ?>

                            <?php while ($event = $calendar_result->fetch_assoc()): ?>

                                <?php
                                $event_date = new DateTime($event['event_date']);
                                $formatted_date = $event_date->format('M j, Y');
                                $formatted_day = $event_date->format('l');

                                $type_classes = [
                                    'Exam' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                    'School Event' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                    'Deadline' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                    'Holiday' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    'Other' => 'bg-slate-100 text-slate-600 border border-slate-200'
                                ];

                                $type_icons = [
                                    'Exam' => 'bi-journal-check',
                                    'School Event' => 'bi-calendar-event',
                                    'Deadline' => 'bi-clock-history',
                                    'Holiday' => 'bi-stars',
                                    'Other' => 'bi-calendar3'
                                ];

                                $type_class = $type_classes[$event['event_type']] ?? $type_classes['Other'];
                                $type_icon = $type_icons[$event['event_type']] ?? $type_icons['Other'];

                                if (!empty($event['start_time'])) {
                                    $time_display = date('g:i A', strtotime($event['start_time']));

                                    if (!empty($event['end_time'])) {
                                        $time_display .= ' - ' . date('g:i A', strtotime($event['end_time']));
                                    }
                                } else {
                                    $time_display = 'Whole Day';
                                }
                                ?>

                                <tr class="hover:bg-slate-50/80 transition">

                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-bold text-slate-900 text-sm">
                                                <?= htmlspecialchars($formatted_date) ?>
                                            </p>

                                            <p class="text-[11px] text-slate-400 mt-0.5">
                                                <?= htmlspecialchars($formatted_day) ?>
                                            </p>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="max-w-md">
                                            <p class="font-bold text-slate-900 text-sm">
                                                <?= htmlspecialchars($event['title']) ?>
                                            </p>

                                            <?php if (!empty($event['description'])): ?>
                                                <p class="text-[11px] text-slate-500 mt-1">
                                                    <?= htmlspecialchars($event['description']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="badge-pill <?= $type_class ?> text-[11px] inline-flex items-center gap-1.5">
                                            <i class="bi <?= $type_icon ?>"></i>
                                            <?= htmlspecialchars($event['event_type']) ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="text-slate-600 font-medium">
                                            <?= htmlspecialchars($time_display) ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">

                                            <button
                                                type="button"
                                                onclick='editCalendarEvent(<?= json_encode($event, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="btn-primary px-3 py-2 rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer">
                                                <i class="bi bi-pencil-square"></i>
                                                Edit
                                            </button>

                                            <button
                                                type="button"
                                                onclick='deleteCalendarEvent(<?= (int)$event['calendar_id'] ?>, <?= json_encode($event['title']) ?>)'
                                                class="px-3 py-2 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition inline-flex items-center gap-1.5 cursor-pointer">
                                                <i class="bi bi-trash3"></i>
                                                Delete
                                            </button>

                                        </div>
                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400 text-xs">
                                    <i class="bi bi-calendar-x text-3xl block mb-2 text-slate-300"></i>
                                    No calendar events have been scheduled yet.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>
            </div>

        </div>

    </div>
</main>

<div
    id="calendarModal"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">

    <div class="bg-white rounded-3xl w-full max-w-lg p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200">

        <div class="flex justify-between items-center border-b border-slate-200 pb-4">

            <div>
                <h3
                    id="calendarModalTitle"
                    class="text-xl font-extrabold text-slate-900">
                    Add Calendar Event
                </h3>

                <p class="text-xs text-slate-400 mt-1">
                    Schedule an important academic date.
                </p>
            </div>

            <button
                type="button"
                id="closeCalendarModal"
                class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-lg font-bold cursor-pointer">
                &times;
            </button>

        </div>

        <form
            method="POST"
            action="../../controllers/process_admin_calendar.php"
            class="space-y-5 text-xs">

            <input
                type="hidden"
                name="action"
                id="calendarAction"
                value="create_event">

            <input
                type="hidden"
                name="calendar_id"
                id="calendarId"
                value="">

            <div>
                <label class="block font-semibold text-slate-600 mb-1">
                    Event Title
                </label>

                <input
                    type="text"
                    name="title"
                    id="calendarTitle"
                    required
                    maxlength="200"
                    placeholder="e.g. Midterm Examination"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">
                        Event Type
                    </label>

                    <select
                        name="event_type"
                        id="calendarEventType"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white cursor-pointer focus:ring-2 focus:ring-blue-600 focus:outline-none">

                        <option value="Exam">
                            Exam
                        </option>

                        <option value="School Event">
                            School Event
                        </option>

                        <option value="Deadline">
                            Deadline
                        </option>

                        <option value="Holiday">
                            Holiday
                        </option>

                        <option value="Other">
                            Other
                        </option>

                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">
                        Event Date
                    </label>

                    <input
                        type="date"
                        name="event_date"
                        id="calendarDate"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">
                        Start Time
                        <span class="font-normal text-slate-400">
                            (Optional)
                        </span>
                    </label>

                    <input
                        type="time"
                        name="start_time"
                        id="calendarStartTime"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">
                        End Time
                        <span class="font-normal text-slate-400">
                            (Optional)
                        </span>
                    </label>

                    <input
                        type="time"
                        name="end_time"
                        id="calendarEndTime"
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>

            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">
                    Description
                    <span class="font-normal text-slate-400">
                        (Optional)
                    </span>
                </label>

                <textarea
                    name="description"
                    id="calendarDescription"
                    rows="4"
                    placeholder="Additional information about this event..."
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm resize-none focus:ring-2 focus:ring-blue-600 focus:outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">

                <button
                    type="button"
                    id="cancelCalendarModal"
                    class="px-5 py-2.5 border border-slate-300 rounded-xl font-semibold text-slate-700 hover:bg-slate-100 cursor-pointer">
                    Cancel
                </button>

                <button
                    type="submit"
                    id="calendarSubmitButton"
                    class="btn-primary px-6 py-2.5 rounded-xl font-bold cursor-pointer shadow-md">
                    Save Event
                </button>

            </div>

        </form>

    </div>
</div>

<form
    method="POST"
    action="../../controllers/process_admin_calendar.php"
    id="deleteCalendarForm"
    class="hidden">

    <input
        type="hidden"
        name="action"
        value="delete_event">

    <input
        type="hidden"
        name="calendar_id"
        id="deleteCalendarId">

</form>
<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/admin-calendar.js"></script>

<?php if (isset($_GET['saved'])): ?>
<script>
    Swal.fire({
        title: 'Saved!',
        text: 'The calendar event has been successfully saved.',
        icon: 'success',
        confirmButtonColor: '#0A1931',
        confirmButtonText: 'Done'
    }).then(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
    });
</script>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
<script>
    Swal.fire({
        title: 'Deleted!',
        text: 'The calendar event has been removed.',
        icon: 'success',
        confirmButtonColor: '#0A1931',
        confirmButtonText: 'Done'
    }).then(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
    });
</script>
<?php endif; ?>