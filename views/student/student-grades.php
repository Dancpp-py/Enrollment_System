<?php
require_once '../../config/db.php';
require_once '../includes/auth.php';
requireStudentLogin();

$student_id = (int) $_SESSION['student_id'];

// ---------------------------------------------------------------
// Every graded subject across all of this student's enrollments.
// LEFT JOIN on grades so enrolled-but-not-yet-encoded subjects
// still appear (with blank marks) instead of silently vanishing.
// ---------------------------------------------------------------
$stmt = $conn->prepare("
    SELECT
        e.enrollment_id,
        e.grade_level,
        e.semester,
        sy.school_year,
        sub.subject_code,
        sub.subject_name,
        sub.units,
        p.last_name  AS prof_last_name,
        p.first_name AS prof_first_name,
        g.first_quarter,
        g.second_quarter,
        g.final_grade,
        g.remarks
    FROM enrollments e
    INNER JOIN school_years sy   ON sy.school_year_id = e.school_year_id
    INNER JOIN class_schedules cs ON cs.section_id = e.section_id
    INNER JOIN curriculum c       ON c.curriculum_id = cs.curriculum_id
                                 AND c.strand_id     = e.strand_id
                                 AND c.grade_level   = e.grade_level
                                 AND c.semester      = e.semester
    INNER JOIN subjects sub       ON sub.subject_id = c.subject_id
    LEFT  JOIN professors p       ON p.professor_id = cs.professor_id
    LEFT  JOIN grades g           ON g.schedule_id  = cs.schedule_id
                                 AND g.enrollment_id = e.enrollment_id
    WHERE e.student_id = ?
      AND e.status = 'Confirmed'
      AND e.stage  = 'Enrolled'
    ORDER BY e.grade_level, e.semester, sub.subject_code
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------------------------------------------------------------
// Group by period, and precompute the summary cards per period so
// the client-side filter can just swap in the right numbers.
// ---------------------------------------------------------------
function periodKey($gradeLevel, $semester) {
    $g = preg_replace('/[^0-9]/', '', $gradeLevel);      // "Grade 11" -> "11"
    $s = ($semester === '1st Semester') ? '1' : '2';
    return $g . '|' . $s;
}

function gradeDescriptor($grade) {
    if ($grade === null)  return ['—', 'slate'];
    if ($grade >= 90)     return ['Outstanding', 'emerald'];
    if ($grade >= 85)     return ['Very Satisfactory', 'emerald'];
    if ($grade >= 80)     return ['Satisfactory', 'blue'];
    if ($grade >= 75)     return ['Fairly Satisfactory', 'amber'];
    return ['Did Not Meet Expectations', 'red'];
}

// remarks is authoritative when the teacher has set it; otherwise
// fall back to deriving from final_grade; otherwise still pending.
function resolveRemarks($row) {
    if (!empty($row['remarks']))            return $row['remarks'];
    if ($row['final_grade'] === null)       return 'Pending';
    return ($row['final_grade'] >= 75) ? 'Passed' : 'Failed';
}

$periods = [];   // key => ['grade_level' =>, 'semester' =>, 'school_year' =>]
$stats   = [];   // key => ['gwa' =>, 'units' =>, 'passed' =>, 'total' =>]

foreach ($rows as $row) {
    $key = periodKey($row['grade_level'], $row['semester']);

    if (!isset($periods[$key])) {
        $periods[$key] = [
            'grade_level' => $row['grade_level'],
            'semester'    => $row['semester'],
            'school_year' => $row['school_year'],
        ];
        $stats[$key] = ['weighted' => 0, 'gradedUnits' => 0, 'units' => 0, 'passed' => 0, 'total' => 0];
    }

    $stats[$key]['units'] += (float) $row['units'];
    $stats[$key]['total']++;

    if ($row['final_grade'] !== null) {
        $stats[$key]['weighted']    += (float) $row['final_grade'] * (float) $row['units'];
        $stats[$key]['gradedUnits'] += (float) $row['units'];
    }

    if (resolveRemarks($row) === 'Passed') {
        $stats[$key]['passed']++;
    }
}

// Collapse into the final display shape
foreach ($stats as $key => $s) {
    $gwa = $s['gradedUnits'] > 0 ? round($s['weighted'] / $s['gradedUnits'], 2) : null;
    [$descriptor, $tone] = gradeDescriptor($gwa);

    $stats[$key] = [
        'gwa'        => $gwa !== null ? number_format($gwa, 2) : '—',
        'descriptor' => $descriptor,
        'tone'       => $tone,
        'units'      => number_format($s['units'], 1),
        'passed'     => $s['passed'],
        'total'      => $s['total'],
        'percent'    => $s['total'] > 0 ? round(($s['passed'] / $s['total']) * 100) : 0,
    ];
}

// Default to the most recent period the student has records for
$defaultKey = !empty($periods) ? array_key_last($periods) : null;

include '../includes/header.php';
include '../includes/student-navbar.php';
include '../includes/student-sidebar.php';
?>
<main class="page-transition min-h-screen p-3 sm:p-6 pt-16 sm:pt-20 lg:ml-72 bg-slate-50/50">
    <section class="mx-auto max-w-7xl space-y-4 sm:space-y-6">
        
        <!-- Header Banner -->
        <header class="rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-4 sm:p-6 text-white shadow-xl">
            <section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <section>
                    <span class="mb-1.5 sm:mb-2 inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-2.5 sm:px-3 py-0.5 sm:py-1 text-[10px] sm:text-xs font-bold text-amber-300 backdrop-blur-sm">
                        <i class="bi bi-mortarboard-fill text-xs"></i>
                        Student Portal
                    </span>
                    <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Academic Grades
                    </h1>
                    <span class="mt-0.5 sm:mt-1 block max-w-2xl text-xs sm:text-sm text-blue-100">
                        View your official enrolled subjects, quarterly grades, and evaluation status.
                    </span>
                </section>

                <section class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3">
                    <!-- Period Filter Dropdown -->
                    <div class="relative w-full sm:w-auto min-w-[200px]">
                        <select id="period-select" onchange="filterAcademicPeriod()" class="w-full appearance-none rounded-xl border border-white/20 bg-white/10 py-2 sm:py-2.5 pl-3 pr-8 text-xs font-bold text-white backdrop-blur-md outline-none transition focus:border-amber-400 focus:bg-white focus:text-[#0A1931]">
                            <?php if (empty($periods)): ?>
                                <option value="" class="text-slate-800">No records</option>
                            <?php else: ?>
                                <?php foreach ($periods as $key => $period): ?>
                                    <option value="<?= htmlspecialchars($key) ?>" class="text-slate-800" <?= $key === $defaultKey ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($period['grade_level']) ?> — <?= htmlspecialchars($period['semester']) ?> (<?= htmlspecialchars($period['school_year']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <i class="bi bi-chevron-down pointer-events-none absolute right-3 top-2.5 sm:top-3 text-xs text-blue-200"></i>
                    </div>

                    <!-- Term View Tabs -->
                    <div class="grid grid-cols-3 sm:flex items-center gap-1 rounded-xl bg-white/10 p-1 backdrop-blur-md border border-white/10">
                        <button type="button" onclick="switchTerm('q1')" id="btn-q1" class="term-btn rounded-lg px-2 sm:px-3.5 py-1.5 text-center text-[11px] sm:text-xs font-bold transition-all duration-200 text-white hover:bg-white/10 whitespace-nowrap">
                            1st Qtr
                        </button>
                        <button type="button" onclick="switchTerm('q2')" id="btn-q2" class="term-btn rounded-lg px-2 sm:px-3.5 py-1.5 text-center text-[11px] sm:text-xs font-bold transition-all duration-200 text-white hover:bg-white/10 whitespace-nowrap">
                            2nd Qtr
                        </button>
                        <button type="button" onclick="switchTerm('all')" id="btn-all" class="term-btn rounded-lg px-2 sm:px-3.5 py-1.5 text-center text-[11px] sm:text-xs font-bold transition-all duration-200 bg-white text-[#0A1931] shadow-md whitespace-nowrap">
                            Final
                        </button>
                    </div>
                </section>
            </section>
        </header>

        <!-- KPI Metrics Summary Cards -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-5">
            <!-- General Average Card -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-sm transition-all duration-200 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">General Average</span>
                    <span class="rounded-xl bg-blue-50 p-2 sm:p-3 text-[#1E4DB7]"><i class="bi bi-award-fill text-lg sm:text-xl"></i></span>
                </div>
                <div class="mt-2 sm:mt-4 flex flex-wrap items-baseline gap-2">
                    <span class="text-2xl sm:text-4xl font-black text-slate-800" id="gwa-display">—</span>
                    <span id="gwa-descriptor" class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-2 sm:px-2.5 py-0.5 text-[10px] sm:text-xs font-bold text-slate-600 border border-slate-200/60">
                        —
                    </span>
                </div>
                <p class="mt-1 sm:mt-2 text-[11px] sm:text-xs text-slate-400">Weighted by units, across graded subjects</p>
            </div>

            <!-- Enrolled Units Card -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-sm transition-all duration-200 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Enrolled Units</span>
                    <span class="rounded-xl bg-indigo-50 p-2 sm:p-3 text-indigo-600"><i class="bi bi-book-half text-lg sm:text-xl"></i></span>
                </div>
                <div class="mt-2 sm:mt-4 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-4xl font-black text-slate-800" id="units-display">—</span>
                    <span class="text-[11px] sm:text-xs font-semibold text-slate-500">Academic Units</span>
                </div>
                <p class="mt-1 sm:mt-2 text-[11px] sm:text-xs text-slate-400">Total units loaded for this term</p>
            </div>

            <!-- Passed Subjects Card -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-sm sm:col-span-2 lg:col-span-1 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Passed Subjects</span>
                    <span class="rounded-xl bg-emerald-50 p-2 sm:p-3 text-emerald-600"><i class="bi bi-check-circle-fill text-lg sm:text-xl"></i></span>
                </div>
                <div class="mt-2 sm:mt-4 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-4xl font-black text-slate-800" id="passed-display">—</span>
                    <span class="text-xs font-semibold text-emerald-600" id="passed-percent"></span>
                </div>
                <p class="mt-1 sm:mt-2 text-[11px] sm:text-xs text-slate-400">Evaluated course progress</p>
            </div>
        </section>

        <!-- Main Grade Datatable Container -->
        <section class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <header class="flex flex-col gap-3 border-b border-white/10 bg-[#0A1931] p-4 sm:px-6 sm:py-5 sm:flex-row sm:items-center sm:justify-between">
                <section>
                    <h2 class="text-base sm:text-xl font-bold text-white flex items-center gap-2" id="table-title">
                        <i class="bi bi-mortarboard text-amber-400"></i> Final Grades
                    </h2>
                    <span class="mt-0.5 block text-[11px] sm:text-xs text-blue-200">
                        Official academic evaluation breakdown for your enrolled subjects.
                    </span>
                </section>

                <section class="relative w-full sm:w-64 md:w-72">
                    <label for="grade-search" class="sr-only">Search Subject</label>
                    <input id="grade-search" type="text" placeholder="Search code or title..." class="w-full rounded-xl border border-white/15 bg-white/10 py-2 pl-9 pr-3 text-xs text-white placeholder-blue-200 outline-none backdrop-blur-sm transition focus:border-amber-400 focus:bg-white focus:text-slate-800 focus:placeholder-slate-400 focus:ring-2 focus:ring-amber-400/30">
                    <i class="bi bi-search absolute left-3 top-2.5 text-xs text-blue-200"></i>
                </section>
            </header>

            <!-- Mobile Table Hint (Visible on screens < 640px) -->
            <div class="block sm:hidden bg-slate-50 border-b border-slate-100 px-4 py-1.5 text-[10px] text-slate-400 text-center font-medium">
                <i class="bi bi-arrow-left-right mr-1"></i> Swipe horizontally to view full details
            </div>

            <!-- Responsive Table Wrapper -->
            <section class="overflow-x-auto min-w-full scrollbar-thin scrollbar-thumb-slate-200">
                <table class="w-full text-left border-collapse min-w-[640px]">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/75 text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-3 sm:px-6 py-3">Subject Code</th>
                            <th class="px-3 sm:px-6 py-3">Course Details</th>
                            <th class="px-3 sm:px-6 py-3 text-center">Units</th>
                            <th class="px-3 sm:px-6 py-3 text-center term-col-q1 hidden">1st Quarter</th>
                            <th class="px-3 sm:px-6 py-3 text-center term-col-q2 hidden">2nd Quarter</th>
                            <th class="px-3 sm:px-6 py-3 text-center term-col-final bg-blue-50/50">Final Grade</th>
                            <th class="px-3 sm:px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="grade-table-body" class="divide-y divide-slate-100 text-xs sm:text-sm">
                        <?php foreach ($rows as $row): ?>
                            <?php
                                $key     = periodKey($row['grade_level'], $row['semester']);
                                $remarks = resolveRemarks($row);
                                $toneMap = [
                                    'Passed'     => ['emerald', 'bg-emerald-500'],
                                    'Failed'     => ['red',     'bg-red-500'],
                                    'Incomplete' => ['amber',   'bg-amber-500'],
                                    'Dropped'    => ['slate',   'bg-slate-400'],
                                    'Pending'    => ['slate',   'bg-slate-300'],
                                ];
                                [$tone, $dot] = $toneMap[$remarks] ?? ['slate', 'bg-slate-300'];

                                $professor = $row['prof_last_name']
                                    ? $row['prof_last_name'] . ', ' . $row['prof_first_name']
                                    : 'Not yet assigned';
                            ?>
                            <tr class="grade-row transition-all duration-150 hover:bg-slate-50/80" data-period="<?= htmlspecialchars($key) ?>">
                                <td class="whitespace-nowrap px-3 sm:px-6 py-3 sm:py-4">
                                    <span class="inline-block rounded-lg border border-blue-100/60 bg-blue-50 px-2 sm:px-2.5 py-1 font-bold tracking-wide text-[#1E4DB7] text-[11px] sm:text-xs">
                                        <?= htmlspecialchars($row['subject_code']) ?>
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 min-w-[160px] sm:min-w-[200px]">
                                    <div class="font-bold text-slate-800 text-xs sm:text-sm line-clamp-2"><?= htmlspecialchars($row['subject_name']) ?></div>
                                    <div class="mt-0.5 flex items-center gap-1.5 text-[10px] sm:text-xs text-slate-500">
                                        <span><i class="bi bi-person text-slate-400"></i> <?= htmlspecialchars($professor) ?></span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-center font-semibold text-slate-600 whitespace-nowrap text-xs sm:text-sm">
                                    <?= number_format((float) $row['units'], 1) ?>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-center term-col-q1 hidden whitespace-nowrap">
                                    <span class="inline-block rounded-lg bg-slate-100 px-2.5 py-0.5 sm:px-3 sm:py-1 text-xs sm:text-base font-extrabold text-slate-800">
                                        <?= $row['first_quarter'] !== null ? number_format($row['first_quarter'], 2) : '—' ?>
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-center term-col-q2 hidden whitespace-nowrap">
                                    <span class="inline-block rounded-lg bg-slate-100 px-2.5 py-0.5 sm:px-3 sm:py-1 text-xs sm:text-base font-extrabold text-slate-800">
                                        <?= $row['second_quarter'] !== null ? number_format($row['second_quarter'], 2) : '—' ?>
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-center term-col-final bg-blue-50/30 whitespace-nowrap">
                                    <span class="inline-block rounded-lg bg-[#1E4DB7] px-2.5 py-0.5 sm:px-3 sm:py-1 text-xs sm:text-base font-extrabold text-white shadow-sm">
                                        <?= $row['final_grade'] !== null ? number_format($row['final_grade'], 2) : '—' ?>
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 sm:gap-1.5 rounded-full border border-<?= $tone ?>-200/60 bg-<?= $tone ?>-50 px-2 sm:px-2.5 py-0.5 text-[10px] sm:text-xs font-bold text-<?= $tone ?>-700">
                                        <span class="h-1.5 w-1.5 rounded-full <?= $dot ?>"></span> <?= htmlspecialchars($remarks) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Search No Results Placeholder -->
            <section id="no-results" class="hidden px-4 sm:px-6 py-8 sm:py-12 text-center">
                <i class="bi bi-search mb-2 block text-2xl sm:text-3xl text-slate-300"></i>
                <h3 class="text-xs sm:text-base font-bold text-slate-700">No matching subjects found</h3>
                <span class="mt-0.5 block text-[11px] sm:text-xs text-slate-400">
                    Try selecting another term or searching for a different subject code.
                </span>
            </section>
        </section>
    </section>
</main>

<script>
    const PERIOD_STATS = <?= json_encode($stats, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    const searchInput  = document.getElementById('grade-search');
    const periodSelect = document.getElementById('period-select');
    const rows         = document.querySelectorAll('.grade-row');
    const noResults    = document.getElementById('no-results');

    const TONE_CLASSES = {
        emerald: 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
        blue:    'bg-blue-50 text-blue-700 border-blue-200/60',
        amber:   'bg-amber-50 text-amber-700 border-amber-200/60',
        red:     'bg-red-50 text-red-700 border-red-200/60',
        slate:   'bg-slate-50 text-slate-600 border-slate-200/60',
    };

    function updateSummary(periodKey) {
        const s = PERIOD_STATS[periodKey];

        const gwaEl     = document.getElementById('gwa-display');
        const descEl    = document.getElementById('gwa-descriptor');
        const unitsEl   = document.getElementById('units-display');
        const passedEl  = document.getElementById('passed-display');
        const percentEl = document.getElementById('passed-percent');

        if (!s) {
            gwaEl.textContent = '—';
            descEl.textContent = '—';
            descEl.className = 'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold border ' + TONE_CLASSES.slate;
            unitsEl.textContent = '—';
            passedEl.textContent = '—';
            percentEl.textContent = '';
            return;
        }

        gwaEl.textContent = s.gwa;
        descEl.textContent = s.descriptor;
        descEl.className = 'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold border ' +
            (TONE_CLASSES[s.tone] || TONE_CLASSES.slate);
        unitsEl.textContent = s.units;
        passedEl.textContent = s.passed + ' / ' + s.total;
        percentEl.textContent = s.percent + '% Passed';
    }

    function filterAcademicPeriod() {
        const selectedPeriod = periodSelect ? periodSelect.value : '';
        const searchValue = searchInput ? searchInput.value.toLowerCase().trim() : '';
        let visibleRows = 0;

        rows.forEach(row => {
            const matchesPeriod = row.getAttribute('data-period') === selectedPeriod;
            const matchesSearch = row.textContent.toLowerCase().includes(searchValue);

            if (matchesPeriod && matchesSearch) {
                row.classList.remove('hidden');
                visibleRows++;
            } else {
                row.classList.add('hidden');
            }
        });

        noResults.classList.toggle('hidden', visibleRows !== 0);
        updateSummary(selectedPeriod);
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterAcademicPeriod);
    }

    function switchTerm(term) {
        const buttons = document.querySelectorAll('.term-btn');
        const q1Cols    = document.querySelectorAll('.term-col-q1');
        const q2Cols    = document.querySelectorAll('.term-col-q2');
        const finalCols = document.querySelectorAll('.term-col-final');
        const tableTitle = document.getElementById('table-title');

        buttons.forEach(btn => {
            btn.className = 'term-btn rounded-xl px-4 py-2 text-xs font-bold transition-all duration-200 text-white hover:bg-white/10';
        });

        const activeBtn = document.getElementById('btn-' + term);
        if (activeBtn) {
            activeBtn.className = 'term-btn rounded-xl px-4 py-2 text-xs font-bold transition-all duration-200 bg-white text-[#0A1931] shadow-md';
        }

        if (term === 'q1') {
            tableTitle.innerHTML = '<i class="bi bi-journal-bookmark text-amber-400"></i> 1st Quarter Grades';
            q1Cols.forEach(c => c.classList.remove('hidden'));
            q2Cols.forEach(c => c.classList.add('hidden'));
            finalCols.forEach(c => c.classList.add('hidden'));
        } else if (term === 'q2') {
            tableTitle.innerHTML = '<i class="bi bi-journal-check text-amber-400"></i> 2nd Quarter Grades';
            q1Cols.forEach(c => c.classList.add('hidden'));
            q2Cols.forEach(c => c.classList.remove('hidden'));
            finalCols.forEach(c => c.classList.add('hidden'));
        } else {
            tableTitle.innerHTML = '<i class="bi bi-mortarboard text-amber-400"></i> Final Grades';
            q1Cols.forEach(c => c.classList.remove('hidden'));
            q2Cols.forEach(c => c.classList.remove('hidden'));
            finalCols.forEach(c => c.classList.remove('hidden'));
        }
    }

    document.addEventListener('DOMContentLoaded', filterAcademicPeriod);
</script>