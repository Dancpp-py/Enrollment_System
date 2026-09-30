<?php
require_once '../../config/db.php';
require_once '../includes/auth.php';



requireRole('Registrar', 'Super Admin');

include '../includes/header.php';
include '../includes/sidebar.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$schoolYears = $conn->query("
    SELECT school_year_id, school_year, is_active
    FROM school_years
    ORDER BY school_year DESC
")->fetch_all(MYSQLI_ASSOC);

if (isset($_POST['school_year_id'])) {
    $posted_school_year_id = (int) $_POST['school_year_id'];

    foreach ($schoolYears as $sy) {
        if ((int) $sy['school_year_id'] === $posted_school_year_id) {
            $_SESSION['grading_period_school_year_id'] = $posted_school_year_id;
            break;
        }
    }
}

$selected_school_year_id = (int) ($_SESSION['grading_period_school_year_id'] ?? 0);

if ($selected_school_year_id <= 0) {
    foreach ($schoolYears as $sy) {
        if ((int) $sy['is_active'] === 1) {
            $selected_school_year_id = (int) $sy['school_year_id'];
            $_SESSION['grading_period_school_year_id'] = $selected_school_year_id;
            break;
        }
    }
}
$selectedSchoolYear = null;
foreach ($schoolYears as $sy) {
    if ((int) $sy['school_year_id'] === $selected_school_year_id) {
        $selectedSchoolYear = $sy;
        break;
    }
}

$existingPeriods = [];

if ($selected_school_year_id > 0) {
    $stmt = $conn->prepare("
        SELECT grading_period_id, semester, quarter, opens_at, closes_at, is_open
        FROM grading_periods
        WHERE school_year_id = ?
    ");

    $stmt->bind_param("i", $selected_school_year_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($rows as $row) {
        $existingPeriods[$row['semester'] . '|' . $row['quarter']] = $row;
    }
}

$semesters = ['1st Semester', '2nd Semester'];
$quarters  = ['First Quarter', 'Second Quarter'];

$now = new DateTime();

function periodStatus($period, DateTime $now) {
    if (!$period) {
        return [
            'label' => 'Not configured',
            'class' => 'bg-slate-100 text-slate-600',
            'accepting' => false
        ];
    }

    $opens  = new DateTime($period['opens_at']);
    $closes = new DateTime($period['closes_at']);
    $flagOn = ((int) $period['is_open'] === 1);

    if (!$flagOn) {
        return [
            'label' => 'Manually closed',
            'class' => 'bg-red-100 text-red-700',
            'accepting' => false
        ];
    }

    if ($now < $opens) {
        return [
            'label' => 'Scheduled',
            'class' => 'bg-blue-100 text-blue-700',
            'accepting' => false
        ];
    }

    if ($now > $closes) {
        return [
            'label' => 'Window passed',
            'class' => 'bg-amber-100 text-amber-700',
            'accepting' => false
        ];
    }

    return [
        'label' => 'Accepting grades',
        'class' => 'bg-green-100 text-green-700',
        'accepting' => true
    ];
}

function toInputValue($datetime) {
    return $datetime ? date('Y-m-d\TH:i', strtotime($datetime)) : '';
}
?>

<main class="page-transition lg:ml-72 pt-20 min-h-screen p-6 bg-slate-50">
    <div class="max-w-7xl mx-auto space-y-8">
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-3">
                    <i class="bi bi-calendar2-week"></i>
                    Academic Configuration
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight">
                    Grading Periods
                </h1>
                <p class="text-blue-100 mt-1 text-sm max-w-2xl">
                    Control when teachers are allowed to encode grades for each quarter.
                </p>
            </div>
        </div>

        <div class="card-elevated overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center">
                        <i class="bi bi-calendar3 text-lg"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900">
                            School Year
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Select the school year you want to manage.
                        </p>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-white">
                <form method="GET" action="">
                    <label
                        for="school_year_id"
                        class="block text-xs font-bold text-slate-600 mb-2"
                    >
                        School Year
                    </label>
                    <select
                        id="school_year_id"
                        name="school_year_id"
                        onchange="this.form.submit()"
                        class="w-full md:w-80 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition"
                    >
                        <?php foreach ($schoolYears as $sy): ?>
                            <option
                                value="<?= (int) $sy['school_year_id'] ?>"
                                <?= $selected_school_year_id === (int) $sy['school_year_id'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($sy['school_year']) ?>
                                <?= (int) $sy['is_active'] === 1 ? ' (Active)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <?php if (!$selectedSchoolYear): ?>
            <div class="card-elevated bg-white p-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                    <i class="bi bi-calendar-x text-2xl"></i>
                </div>
                <p class="mt-4 font-bold text-slate-700">
                    No School Year Selected
                </p>
                <p class="text-sm text-slate-400 mt-1">
                    Please select a school year to continue.
                </p>
            </div>
        <?php else: ?>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        Current Configuration
                    </p>
                    <h2 class="text-xl font-extrabold text-[#0A1931] mt-1">
                        <?= htmlspecialchars($selectedSchoolYear['school_year']) ?>
                    </h2>
                </div>
                <?php if ((int) $selectedSchoolYear['is_active'] === 1): ?>
                    <span class="badge-pill badge-complete">
                        <i class="bi bi-check-circle-fill"></i>
                        Active
                    </span>
                <?php endif; ?>
            </div>

            <?php foreach ($semesters as $semester): ?>
                <div class="card-elevated overflow-hidden bg-white">
                    <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center">
                                <i class="bi bi-journal-bookmark-fill text-lg text-amber-300"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-extrabold">
                                    <?= htmlspecialchars($semester) ?>
                                </h2>
                                <p class="text-blue-100 text-xs mt-1">
                                    Encoding windows for each quarter of this semester.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-6">
                        <?php foreach ($quarters as $quarter): ?>
                            <?php
                            $key    = $semester . '|' . $quarter;
                            $period = $existingPeriods[$key] ?? null;
                            $status = periodStatus($period, $now);
                            ?>

                            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center">
                                                <i class="bi bi-clock-history"></i>
                                            </div>
                                            <h3 class="font-bold text-slate-800">
                                                <?= htmlspecialchars($quarter) ?>
                                            </h3>
                                        </div>
                                        <span class="<?= $status['class'] ?> px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                                            <?= htmlspecialchars($status['label']) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <form
                                        method="POST"
                                        action="../../controllers/process_grading_period.php"
                                        class="space-y-4"
                                    >
                                        <input
                                            type="hidden"
                                            name="action"
                                            value="save_period"
                                        >
                                        <input
                                            type="hidden"
                                            name="school_year_id"
                                            value="<?= (int) $selected_school_year_id ?>"
                                        >
                                        <input
                                            type="hidden"
                                            name="semester"
                                            value="<?= htmlspecialchars($semester) ?>"
                                        >
                                        <input
                                            type="hidden"
                                            name="quarter"
                                            value="<?= htmlspecialchars($quarter) ?>"
                                        >

                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 mb-1.5">
                                                Opens At
                                            </label>
                                            <div class="relative">
                                                <i class="bi bi-calendar-event absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                <input
                                                    type="datetime-local"
                                                    name="opens_at"
                                                    required
                                                    value="<?= toInputValue($period['opens_at'] ?? null) ?>"
                                                    class="w-full rounded-xl border border-slate-300 pl-10 pr-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition"
                                                >
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 mb-1.5">
                                                Closes At
                                            </label>
                                            <div class="relative">
                                                <i class="bi bi-calendar-check absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                <input
                                                    type="datetime-local"
                                                    name="closes_at"
                                                    required
                                                    value="<?= toInputValue($period['closes_at'] ?? null) ?>"
                                                    class="w-full rounded-xl border border-slate-300 pl-10 pr-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition"
                                                >
                                            </div>
                                        </div>

                                        <label class="flex items-center gap-3 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 cursor-pointer hover:bg-slate-100 transition">
                                            <input
                                                type="checkbox"
                                                name="is_open"
                                                value="1"
                                                <?= ($period === null || (int) $period['is_open'] === 1) ? 'checked' : '' ?>
                                                class="w-4 h-4 rounded border-slate-300"
                                            >
                                            <span class="text-sm font-semibold text-slate-700">
                                                Allow encoding during this window
                                            </span>
                                        </label>

                                        <div class="pt-1">
                                            <button
                                                type="submit"
                                                class="w-full bg-[#0A1931] hover:bg-[#1E4DB7] text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-sm inline-flex items-center justify-center gap-2"
                                            >
                                                <i class="bi bi-save2"></i>
                                                <?= $period ? 'Update' : 'Set Window' ?>
                                            </button>
                                        </div>
                                    </form>

                                    <?php if ($period): ?>
                                        <form
                                            method="POST"
                                            action="../../controllers/process_grading_period.php"
                                            class="mt-3"
                                        >
                                            <input
                                                type="hidden"
                                                name="action"
                                                value="toggle_period"
                                            >
                                            <input
                                                type="hidden"
                                                name="grading_period_id"
                                                value="<?= (int) $period['grading_period_id'] ?>"
                                            >
                                            <input
                                                type="hidden"
                                                name="school_year_id"
                                                value="<?= (int) $selected_school_year_id ?>"
                                            >
                                            <button
                                                type="submit"
                                                class="w-full border border-slate-300 hover:border-[#1E4DB7] hover:bg-blue-50 text-slate-700 hover:text-[#1E4DB7] text-sm font-bold px-4 py-2.5 rounded-xl transition inline-flex items-center justify-center gap-2"
                                            >
                                                <i class="bi <?= (int) $period['is_open'] === 1 ? 'bi-lock-fill' : 'bi-unlock-fill' ?>"></i>
                                                <?= (int) $period['is_open'] === 1 ? 'Close Encoding Now' : 'Open Encoding Now' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 shrink-0 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>
                    <div>
                        <strong class="block text-sm font-bold text-[#0A1931]">
                            How this works
                        </strong>
                        <p class="text-sm text-slate-600 mt-1 leading-6">
                            Teachers can only encode grades when a quarter is both marked as allowed
                            <em>and</em> the current date falls inside its window.
                        </p>
                        <p class="text-sm text-slate-600 mt-1 leading-6">
                            A quarter with no window configured will reject all grade submissions.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<script src="/enrollment_system/assets/js/swal.js"></script>