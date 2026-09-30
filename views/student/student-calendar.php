<?php
require_once '../../config/db.php';
require_once '../includes/auth.php';

requireStudentLogin();

$year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');
$month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('n');

if ($month < 1 || $month > 12) {
    $month = (int) date('n');
}

if ($year < 2000 || $year > 2100) {
    $year = (int) date('Y');
}

$currentMonth = sprintf('%04d-%02d', $year, $month);

$previousMonth = strtotime('-1 month', strtotime($currentMonth . '-01'));
$nextMonth = strtotime('+1 month', strtotime($currentMonth . '-01'));

$previousYear = (int) date('Y', $previousMonth);
$previousMonthNumber = (int) date('n', $previousMonth);

$nextYear = (int) date('Y', $nextMonth);
$nextMonthNumber = (int) date('n', $nextMonth);

$firstDayTimestamp = strtotime($currentMonth . '-01');
$daysInMonth = (int) date('t', $firstDayTimestamp);
$firstDayOfWeek = (int) date('w', $firstDayTimestamp);

$monthStart = date('Y-m-01', $firstDayTimestamp);
$monthEnd = date('Y-m-t', $firstDayTimestamp);

$stmt = $conn->prepare("
    SELECT
        calendar_id,
        title,
        event_type,
        event_date,
        start_time,
        end_time,
        description
    FROM school_calendar
    WHERE event_date BETWEEN ? AND ?
    ORDER BY event_date ASC, start_time ASC, title ASC
");

$stmt->bind_param(
    "ss",
    $monthStart,
    $monthEnd
);

$stmt->execute();

$result = $stmt->get_result();

$eventsByDate = [];

while ($event = $result->fetch_assoc()) {
    $eventsByDate[$event['event_date']][] = $event;
}

$stmt->close();

$today = date('Y-m-d');

function eventTypeClass($type)
{
    return match ($type) {
        'Exam' => 'bg-rose-100 text-rose-700 border-rose-200',
        'School Event' => 'bg-blue-100 text-blue-700 border-blue-200',
        'Deadline' => 'bg-amber-100 text-amber-700 border-amber-200',
        'Holiday' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200'
    };
}

function eventTypeDot($type)
{
    return match ($type) {
        'Exam' => 'bg-rose-500',
        'School Event' => 'bg-blue-500',
        'Deadline' => 'bg-amber-500',
        'Holiday' => 'bg-emerald-500',
        default => 'bg-slate-500'
    };
}

function eventTypeIcon($type)
{
    return match ($type) {
        'Exam' => 'bi-journal-check',
        'School Event' => 'bi-calendar-event',
        'Deadline' => 'bi-clock-history',
        'Holiday' => 'bi-stars',
        default => 'bi-calendar2-event'
    };
}

function formatEventTime($startTime, $endTime)
{
    if (!$startTime) {
        return 'All day';
    }

    $start = date('g:i A', strtotime($startTime));

    if (!$endTime) {
        return $start;
    }

    return $start . ' - ' . date('g:i A', strtotime($endTime));
}

function eventDateLabel($date)
{
    return date('F j, Y', strtotime($date));
}

$page_title = "School Calendar - Masinag SHS";

include '../includes/header.php';
include '../includes/student-navbar.php';
include '../includes/student-sidebar.php';
?>

<main class="page-transition min-h-screen bg-slate-50 p-3 pt-16 sm:p-6 sm:pt-20 lg:ml-72">
    <section class="mx-auto max-w-7xl space-y-6 sm:space-y-8">

        <header class="overflow-hidden rounded-2xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-5 text-white shadow-xl sm:rounded-3xl sm:p-8">
            <section class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                <section>
                    <span class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-amber-300">
                        <i class="bi bi-calendar3"></i>
                        Student Portal
                    </span>

                    <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                        School Calendar
                    </h1>

                    <p class="mt-1 max-w-2xl text-xs text-blue-100 sm:text-sm">
                        View examinations, school events, deadlines, holidays, and other important school dates.
                    </p>
                </section>

                <section class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur-md">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-amber-300">
                        <i class="bi bi-calendar-month text-2xl"></i>
                    </div>

                    <div>
                        <span class="block text-xs font-medium text-blue-100">
                            Academic Calendar
                        </span>

                        <span class="block text-sm font-extrabold text-white">
                            <?= date('Y', $firstDayTimestamp) ?>
                        </span>
                    </div>
                </section>
            </section>
        </header>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <a
                    href="?year=<?= $previousYear ?>&month=<?= $previousMonthNumber ?>"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:border-[#1E4DB7] hover:bg-blue-50 hover:text-[#1E4DB7]"
                >
                    <i class="bi bi-chevron-left"></i>
                    Previous
                </a>

                <div class="text-center">
                    <h2 class="text-2xl font-extrabold tracking-tight text-[#0A1931]">
                        <?= date('F', $firstDayTimestamp) ?>
                    </h2>

                    <p class="mt-0.5 text-sm font-semibold text-slate-500">
                        <?= date('Y', $firstDayTimestamp) ?>
                    </p>
                </div>

                <a
                    href="?year=<?= $nextYear ?>&month=<?= $nextMonthNumber ?>"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:border-[#1E4DB7] hover:bg-blue-50 hover:text-[#1E4DB7]"
                >
                    Next
                    <i class="bi bi-chevron-right"></i>
                </a>

            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="grid grid-cols-7 border-b border-slate-200 bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white">

                <?php
                $daysOfWeek = [
                    'Sunday',
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday'
                ];
                ?>

                <?php foreach ($daysOfWeek as $day): ?>
                    <div class="border-r border-white/10 px-1 py-3 text-center last:border-r-0 sm:px-2">
                        <span class="hidden text-xs font-bold sm:inline">
                            <?= $day ?>
                        </span>

                        <span class="text-[10px] font-bold sm:hidden">
                            <?= substr($day, 0, 3) ?>
                        </span>
                    </div>
                <?php endforeach; ?>

            </div>

            <div class="grid grid-cols-7">

                <?php for ($blank = 0; $blank < $firstDayOfWeek; $blank++): ?>

                    <div class="min-h-[120px] border-b border-r border-slate-100 bg-slate-50/60 p-2 sm:min-h-[155px] sm:p-3"></div>

                <?php endfor; ?>

                <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>

                    <?php
                    $date = sprintf(
                        '%04d-%02d-%02d',
                        $year,
                        $month,
                        $day
                    );

                    $timestamp = strtotime($date);

                    $dayOfWeek = (int) date('w', $timestamp);

                    $isToday = $date === $today;

                    $dayEvents = $eventsByDate[$date] ?? [];

                    $isWeekend = $dayOfWeek === 0 || $dayOfWeek === 6;
                    ?>

                    <div
                        class="group relative min-h-[120px] border-b border-r border-slate-100 p-2 transition hover:bg-blue-50/40 sm:min-h-[155px] sm:p-3 <?= $isWeekend ? 'bg-slate-50/40' : 'bg-white' ?>"
                    >

                        <div class="mb-2 flex items-center justify-between">

                            <?php if ($isToday): ?>

                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#1E4DB7] text-sm font-extrabold text-white shadow-sm">
                                    <?= $day ?>
                                </span>

                            <?php else: ?>

                                <span class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold <?= $isWeekend ? 'text-slate-400' : 'text-slate-700' ?>">
                                    <?= $day ?>
                                </span>

                            <?php endif; ?>

                            <?php if ($isToday): ?>

                                <span class="hidden rounded-full bg-emerald-100 px-2 py-1 text-[9px] font-extrabold text-emerald-700 sm:inline-flex">
                                    TODAY
                                </span>

                            <?php endif; ?>

                        </div>

                        <?php if (!empty($dayEvents)): ?>

                            <div class="space-y-1.5">

                                <?php foreach ($dayEvents as $event): ?>

                                    <div
                                        class="group/event cursor-default rounded-lg border p-1.5 <?= eventTypeClass($event['event_type']) ?>"
                                        title="<?= htmlspecialchars(
                                            $event['title'] . ' — ' .
                                            eventDateLabel($event['event_date']) . ' — ' .
                                            formatEventTime($event['start_time'], $event['end_time'])
                                        ) ?>"
                                    >

                                        <div class="flex items-start gap-1.5">

                                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full <?= eventTypeDot($event['event_type']) ?>"></span>

                                            <div class="min-w-0 flex-1">

                                                <p class="truncate text-[10px] font-extrabold sm:text-[11px]">
                                                    <?= htmlspecialchars($event['title']) ?>
                                                </p>

                                                <p class="mt-0.5 truncate text-[9px] font-semibold opacity-80 sm:text-[10px]">
                                                    <?= htmlspecialchars(formatEventTime($event['start_time'], $event['end_time'])) ?>
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; ?>

                    </div>

                <?php endfor; ?>

                <?php
                $totalCells = $firstDayOfWeek + $daysInMonth;
                $remainingCells = (7 - ($totalCells % 7)) % 7;
                ?>

                <?php for ($blank = 0; $blank < $remainingCells; $blank++): ?>

                    <div class="min-h-[120px] border-b border-r border-slate-100 bg-slate-50/60 p-2 sm:min-h-[155px] sm:p-3"></div>

                <?php endfor; ?>

            </div>

        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">

            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-[#0A1931]">
                        Calendar Legend
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Event colors used on the calendar.
                    </p>
                </div>

                <i class="bi bi-info-circle text-slate-400"></i>
            </div>

            <div class="flex flex-wrap gap-3">

                <span class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                    Exam
                </span>

                <span class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                    School Event
                </span>

                <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                    Deadline
                </span>

                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    Holiday
                </span>

                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-slate-500"></span>
                    Other
                </span>

            </div>

        </section>

    </section>
</main>