<?php
session_start();

$page_title = "Curriculum Version Builder - Masinag SHS";

require_once "../../config/db.php";
require_once "../includes/auth.php";
requireRole('Scheduler', 'Super Admin');

$VALID_GRADES = ['Grade 11', 'Grade 12'];
$VALID_SEMESTERS = ['1st Semester', '2nd Semester'];

if (isset($_POST['version_id'])) {
    $version_id = (int) $_POST['version_id'];

    if ($version_id > 0) {
        $_SESSION['curriculum_version_id'] = $version_id;
    }
} else {
    $version_id = (int) ($_SESSION['curriculum_version_id'] ?? 0);
}

if (isset($_POST['grade']) && in_array($_POST['grade'], $VALID_GRADES, true)) {
    $_SESSION['curriculum_builder_grade'] = $_POST['grade'];
}

if (isset($_POST['semester']) && in_array($_POST['semester'], $VALID_SEMESTERS, true)) {
    $_SESSION['curriculum_builder_semester'] = $_POST['semester'];
}

$current_grade = $_SESSION['curriculum_builder_grade'] ?? 'Grade 11';
$current_semester = $_SESSION['curriculum_builder_semester'] ?? '1st Semester';

// Get version details
if ($version_id > 0) {
    $stmt = $conn->prepare("SELECT version_name FROM curriculum_versions WHERE curriculum_version_id = ?");
    $stmt->bind_param("i", $version_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $version_name = $row['version_name'];
    }
    $stmt->close();
}

// Fetch all subjects
$subjects = [];
$subject_result = $conn->query("SELECT subject_id, subject_code, subject_name, units FROM subjects WHERE is_active = 1 ORDER BY subject_name");
if ($subject_result) {
    while ($row = $subject_result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

// Fetch all strands
$strands = [];
$strand_result = $conn->query("SELECT strand_id, strand_name FROM strands ORDER BY strand_name");
if ($strand_result) {
    while ($row = $strand_result->fetch_assoc()) {
        $strands[] = $row;
    }
}

// Get current filter values
if (isset($_POST['grade']) && in_array($_POST['grade'], $VALID_GRADES, true)) {
    $_SESSION['curriculum_builder_grade'] = $_POST['grade'];
}

if (isset($_POST['semester']) && in_array($_POST['semester'], $VALID_SEMESTERS, true)) {
    $_SESSION['curriculum_builder_semester'] = $_POST['semester'];
}

// $current_grade = $_SESSION['curriculum_builder_grade'] ?? 'Grade 11';
// $current_semester = $_SESSION['curriculum_builder_semester'] ?? '1st Semester';

// Fetch existing curriculum assignments for this version
$existing_assignments = [];
if ($version_id > 0) {
    $stmt = $conn->prepare("
        SELECT subject_id, strand_id
        FROM curriculum
        WHERE curriculum_version_id = ? AND grade_level = ? AND semester = ?
    ");
    $stmt->bind_param("iss", $version_id, $current_grade, $current_semester);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $key = $row['subject_id'] . '|' . $row['strand_id'];
        $existing_assignments[$key] = true;
    }
    $stmt->close();
}

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<main id="mainContent" class="page-transition lg:ml-72 pt-20 min-h-screen p-6 bg-slate-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- PAGE HEADER -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Subject Matrix Editor
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight">
                    <?= $version_id > 0 ? htmlspecialchars($version_name) : 'Curriculum Matrix Builder' ?>
                </h1>
                <p class="text-blue-100 mt-1 text-sm max-w-2xl">
                    Configure subject offerings per academic track, grade level, and semester.
                </p>
            </div>
            
            <div class="relative z-10">
                <button onclick="window.location.href = '../scheduler/curriculum-builder';" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-bold px-5 py-3 rounded-xl transition cursor-pointer flex items-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    <span>All Versions</span>
                </button>
            </div>
        </div>
        
        <!-- Display messages from back-end -->
        <?php if (isset($_GET['error']) && isset($_GET['message'])): ?>
            <div class="bg-red-50 border border-red-200 p-4 rounded-2xl text-red-700 text-xs flex items-center gap-2">
                <i class="bi bi-exclamation-circle-fill text-red-500 text-base"></i>
                <span><?= htmlspecialchars($_GET['message']) ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && isset($_GET['message'])): ?>
            <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl text-emerald-700 text-xs flex items-center gap-2">
                <i class="bi bi-check-circle-fill text-emerald-500 text-base"></i>
                <span><?= htmlspecialchars($_GET['message']) ?></span>
            </div>
        <?php endif; ?>

        <!-- CURRICULUM VERSION SETTINGS -->
        <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white p-6 sm:p-8">
            <?php if ($version_id > 0): ?>
            <form method="POST" action="../../controllers/curriculum_controller.php">
                <input type="hidden" name="action" value="rename_version">
                <input type="hidden" name="version_id" value="<?= $version_id ?>">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-end text-xs">
                    <div class="lg:col-span-2">
                        <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Curriculum Version Name
                        </label>
                        <input
                            type="text"
                            name="version_name"
                            required
                            value="<?= htmlspecialchars($version_name) ?>"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Version Identifier
                        </label>
                        <div class="flex gap-3">
                            <input
                                type="text"
                                value="#<?= $version_id ?>"
                                readonly
                                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm font-mono text-slate-600"
                            >
                            <button type="submit" class="shrink-0 btn-primary px-5 py-2.5 rounded-xl text-xs font-bold cursor-pointer shadow-sm">
                                Save Name
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <?php endif; ?>
        </div>

        <!-- FILTERS -->
        <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex flex-wrap items-center gap-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Grade Level</p>
                    <div class="flex gap-2">
                        <form method="POST" class="inline">
                            <input type="hidden" name="grade" value="Grade 11">
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer <?= $current_grade === 'Grade 11' ? 'bg-[#0A1931] text-white shadow-sm' : 'border border-slate-300 bg-slate-50 text-slate-700 hover:bg-slate-100' ?>">
                                Grade 11
                            </button>
                        </form>
                        <form method="POST" class="inline">
                            <input type="hidden" name="grade" value="Grade 12">
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer <?= $current_grade === 'Grade 12' ? 'bg-[#0A1931] text-white shadow-sm' : 'border border-slate-300 bg-slate-50 text-slate-700 hover:bg-slate-100' ?>">
                                Grade 12
                            </button>
                        </form>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Semester</p>
                    <div class="flex gap-2">
                        <form method="POST" class="inline">
                            <input type="hidden" name="semester" value="1st Semester">
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer <?= $current_semester === '1st Semester' ? 'bg-[#0A1931] text-white shadow-sm' : 'border border-slate-300 bg-slate-50 text-slate-700 hover:bg-slate-100' ?>">
                                1st Semester
                            </button>
                        </form>
                        <form method="POST" class="inline">
                            <input type="hidden" name="semester" value="2nd Semester">
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer <?= $current_semester === '2nd Semester' ? 'bg-[#0A1931] text-white shadow-sm' : 'border border-slate-300 bg-slate-50 text-slate-700 hover:bg-slate-100' ?>">
                                2nd Semester
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="text-xs text-slate-500 font-semibold">
                <span id="entryCount" class="font-mono font-bold text-blue-700 text-sm"><?= count($subjects) ?></span> catalog subjects
            </div>
        </div>

        <!-- CURRICULUM MATRIX -->
        <form method="POST" action="../../controllers/curriculum_controller.php" id="curriculumForm">
            <input type="hidden" name="action" value="save_curriculum">
            <input type="hidden" name="version_id" value="<?= $version_id ?>">
            <input type="hidden" name="grade_level" value="<?= htmlspecialchars($current_grade) ?>">
            <input type="hidden" name="semester" value="<?= htmlspecialchars($current_semester) ?>">

            <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] border-collapse text-sm">
                        <thead class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white">
                            <tr>
                                <th class="w-[36%] px-6 py-4 text-left font-semibold">
                                    Subject Description
                                </th>
                                <th class="w-[10%] px-4 py-4 text-center font-semibold">
                                    Core (All)
                                </th>
                                <?php foreach ($strands as $strand): ?>
                                <th class="px-4 py-4 text-center font-semibold">
                                    <?= htmlspecialchars($strand['strand_name']) ?>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody id="curriculumTableBody" class="divide-y divide-slate-100 text-xs">
                            <?php if (empty($subjects)): ?>
                            <tr>
                                <td colspan="<?= 2 + count($strands) ?>" class="text-center py-12 text-slate-400">
                                    No subjects available in catalog. Please register subjects first.
                                    <br>
                                    <a href="subject-bank" class="text-blue-600 font-bold hover:underline mt-2 inline-block">Open Subject Bank</a>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($subjects as $subject): ?>
                                <tr class="curriculum-row hover:bg-slate-50/80 transition"
                                    data-subject-id="<?= $subject['subject_id'] ?>">
                                    <td class="px-6 py-3.5">
                                        <span class="font-bold text-slate-900 text-sm block">
                                            <?= htmlspecialchars($subject['subject_name']) ?>
                                        </span>
                                        <span class="text-xs text-slate-500 font-mono">
                                            <?= htmlspecialchars($subject['subject_code']) ?> · <?= $subject['units'] ?> unit(s)
                                        </span>
                                    </td>
                                    <td class="text-center px-4 py-3.5 bg-blue-50/30">
                                        <input type="checkbox" class="core-check w-4 h-4 text-blue-600 rounded cursor-pointer">
                                    </td>
                                    <?php foreach ($strands as $strand): ?>
                                    <td class="text-center px-4 py-3.5">
                                        <?php
                                            $key = $subject['subject_id'] . '|' . $strand['strand_id'];
                                            $checked = isset($existing_assignments[$key]) ? 'checked' : '';
                                        ?>
                                        <input
                                            type="checkbox"
                                            name="curriculum[<?= $subject['subject_id'] ?>][<?= $strand['strand_id'] ?>][<?= htmlspecialchars($current_grade) ?>][<?= htmlspecialchars($current_semester) ?>]"
                                            value="1"
                                            class="strand-check w-4 h-4 text-blue-600 rounded cursor-pointer"
                                            data-subject-id="<?= $subject['subject_id'] ?>"
                                            data-strand-id="<?= $strand['strand_id'] ?>"
                                            <?= $checked ?>
                                        >
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- SAVE BUTTON -->
                <div class="flex justify-between items-center px-6 py-4 border-t border-slate-200 bg-slate-50">
                    <p class="text-xs text-slate-500">
                        Saving will update the subject matrix for <strong class="text-slate-700"><?= htmlspecialchars($current_grade) ?> &bull; <?= htmlspecialchars($current_semester) ?></strong>.
                    </p>

                    <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl font-bold text-xs cursor-pointer shadow-md flex items-center gap-2">
                        <i class="bi bi-save-fill"></i>
                        <span>Save Curriculum Matrix</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.core-check').forEach(function(coreCheck) {
        coreCheck.addEventListener('change', function() {
            const row = this.closest('tr');
            const strandChecks = row.querySelectorAll('.strand-check');
            strandChecks.forEach(function(check) {
                check.checked = coreCheck.checked;
            });
        });
    });
});
</script>