<?php
require_once '../../config/db.php';
require_once '../includes/auth.php';
requireStudentLogin();

$student_id = (int) $_SESSION['student_id'];

$stmt = $conn->prepare("
    SELECT e.strand_id, e.grade_level, e.semester, st.strand_name
    FROM enrollments e
    INNER JOIN strands st ON st.strand_id = e.strand_id
    WHERE e.student_id = ?
      AND e.status = 'Confirmed'
      AND e.stage = 'Enrolled'
    ORDER BY e.enrollment_id DESC
    LIMIT 1
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Chronological rank of each (grade_level, semester) combo in Senior High.
$semesterRank = [
    'Grade 11|1st Semester' => 1,
    'Grade 11|2nd Semester' => 2,
    'Grade 12|1st Semester' => 3,
    'Grade 12|2nd Semester' => 4,
];

$currentRank = null;
if ($current) {
    $currentKey  = $current['grade_level'] . '|' . $current['semester'];
    $currentRank = $semesterRank[$currentKey] ?? null;
}

$subjectsByGrade = ['Grade 11' => [], 'Grade 12' => []];

if ($current) {
    $stmt = $conn->prepare("
        SELECT sub.subject_code, sub.subject_name, sub.units, c.grade_level, c.semester
        FROM curriculum c
        INNER JOIN subjects sub ON sub.subject_id = c.subject_id
        WHERE c.strand_id = ?
        ORDER BY c.grade_level, c.semester, sub.subject_name
    ");
    $stmt->bind_param("i", $current['strand_id']);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($rows as $row) {
        $rank = $semesterRank[$row['grade_level'] . '|' . $row['semester']] ?? null;

        if ($rank === null || $currentRank === null) {
            $status = 'Upcoming';
        } elseif ($rank < $currentRank) {
            $status = 'Completed';
        } elseif ($rank === $currentRank) {
            $status = 'Current';
        } else {
            $status = 'Upcoming';
        }

        $row['status'] = $status;
        $subjectsByGrade[$row['grade_level']][] = $row;
    }
}

function renderStatusBadge($status) {
    $styles = [
        'Completed' => 'badge-complete',
        'Current'   => 'badge-new',
        'Upcoming'  => 'badge-existing',
    ];
    $class = $styles[$status] ?? 'badge-existing';
    return '<span class="badge-pill ' . $class . '">' . htmlspecialchars($status) . '</span>';
}

function semesterLabel($semester) {
    return $semester === '1st Semester' ? '1st Sem' : '2nd Sem';
}

$page_title = "My Academic Curriculum - Masinag SHS";

include '../includes/header.php';
include '../includes/student-navbar.php';
include '../includes/student-sidebar.php';

?>

<main class="page-transition min-h-screen p-3 sm:p-6 pt-16 sm:pt-20 lg:ml-72 bg-slate-50/50">
    <section class="mx-auto max-w-7xl space-y-4 sm:space-y-6">

        <!-- Hero Header -->
        <header class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-5 sm:p-8 text-white shadow-xl">
            <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <section class="space-y-1.5 sm:space-y-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-2.5 sm:px-3 py-0.5 sm:py-1 text-[10px] sm:text-xs font-bold text-amber-300 backdrop-blur-sm">
                        <i class="bi bi-journal-text"></i> Official Curriculum Plan
                    </span>
                    <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight text-white">
                        Senior High School Curriculum
                    </h1>
                    <p class="max-w-2xl text-xs sm:text-sm text-blue-100">
                        Track your semester-by-semester subjects, credit units, and academic completion milestones.
                    </p>
                </section>

                <?php if ($current): ?>
                    <section class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
                        <div class="rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-center backdrop-blur-sm min-w-[110px] sm:min-w-[120px] flex-1 sm:flex-none">
                            <span class="block text-[10px] uppercase tracking-wider text-blue-200 font-semibold">Strand</span>
                            <span class="font-extrabold text-white text-xs sm:text-sm"><?= htmlspecialchars($current['strand_name']) ?></span>
                        </div>
                        <div class="rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-center backdrop-blur-sm min-w-[110px] sm:min-w-[120px] flex-1 sm:flex-none">
                            <span class="block text-[10px] uppercase tracking-wider text-blue-200 font-semibold">Current Level</span>
                            <span class="font-extrabold text-white text-xs sm:text-sm"><?= htmlspecialchars($current['grade_level'] . ' - ' . $current['semester']) ?></span>
                        </div>
                    </section>
                <?php endif; ?>
            </div>

            <!-- Background Decorative Glow -->
            <div class="pointer-events-none absolute -right-10 -bottom-10 h-48 sm:h-64 w-48 sm:w-64 rounded-full bg-blue-400/10 blur-3xl"></div>
        </header>

        <?php if (!$current): ?>
            <!-- No Enrollment Found Card -->
            <section class="rounded-2xl border border-slate-200/80 bg-white p-8 sm:p-12 text-center shadow-sm space-y-3">
                <i class="bi bi-journal-x text-3xl sm:text-4xl text-slate-400 block"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-700">No Active Enrollment Record Found</h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                    Your curriculum track will automatically appear here once your enrollment is confirmed by the Registrar.
                </p>
            </section>
        <?php else: ?>

            <?php foreach (['Grade 11', 'Grade 12'] as $gradeLevel): ?>
                <!-- Grade Matrix Container -->
                <section class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
                    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] p-4 sm:px-6 sm:py-4 gap-2 text-white">
                        <div class="flex items-center gap-2.5">
                            <i class="bi bi-mortarboard-fill text-amber-400 text-lg sm:text-xl"></i>
                            <h2 class="text-base sm:text-lg font-bold tracking-tight text-white">
                                <?= $gradeLevel ?> Subject Matrix
                            </h2>
                        </div>
                        <span class="self-start sm:self-auto text-[11px] sm:text-xs bg-white/10 border border-white/10 px-3 py-1 rounded-full text-blue-100 font-semibold backdrop-blur-sm">
                            <?= count($subjectsByGrade[$gradeLevel]) ?> Subjects
                        </span>
                    </header>

                    <!-- Mobile Swipe Notice -->
                    <div class="block sm:hidden bg-slate-50 border-b border-slate-100 px-4 py-1.5 text-[10px] text-slate-400 text-center font-medium">
                        <i class="bi bi-arrow-left-right mr-1"></i> Swipe horizontally to view full subject details
                    </div>

                    <!-- Responsive Table Wrapper -->
                    <div class="overflow-x-auto min-w-full">
                        <table class="w-full text-left border-collapse min-w-[550px]">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/75 text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500">
                                    <th class="px-3 sm:px-6 py-3">Subject Code</th>
                                    <th class="px-3 sm:px-6 py-3">Subject Description</th>
                                    <th class="px-3 sm:px-6 py-3 text-center">Semester</th>
                                    <th class="px-3 sm:px-6 py-3 text-center">Units</th>
                                    <th class="px-3 sm:px-6 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                                <?php if (empty($subjectsByGrade[$gradeLevel])): ?>
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-400 text-xs sm:text-sm">
                                            No curriculum subjects configured for this grade level.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($subjectsByGrade[$gradeLevel] as $subj): ?>
                                        <tr class="transition-all duration-150 hover:bg-slate-50/80">
                                            <td class="whitespace-nowrap px-3 sm:px-6 py-3 sm:py-4">
                                                <span class="inline-block rounded-lg border border-blue-100/60 bg-blue-50 px-2 sm:px-2.5 py-0.5 sm:py-1 font-mono font-bold text-[#1E4DB7] text-[11px] sm:text-xs">
                                                    <?= htmlspecialchars($subj['subject_code']) ?>
                                                </span>
                                            </td>
                                            <td class="px-3 sm:px-6 py-3 sm:py-4 min-w-[180px]">
                                                <div class="font-bold text-slate-800 text-xs sm:text-sm line-clamp-2">
                                                    <?= htmlspecialchars($subj['subject_name']) ?>
                                                </div>
                                            </td>
                                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-center font-semibold text-slate-600 whitespace-nowrap text-xs sm:text-sm">
                                                <?= semesterLabel($subj['semester']) ?>
                                            </td>
                                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-center font-bold text-[#1E4DB7] whitespace-nowrap text-xs sm:text-sm">
                                                <?= htmlspecialchars($subj['units']) ?>
                                            </td>
                                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-center whitespace-nowrap">
                                                <?= renderStatusBadge($subj['status']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endforeach; ?>

        <?php endif; ?>

    </section>
</main>