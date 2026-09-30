<?php
session_start();

$page_title = "Curriculum Versions - Masinag SHS";

require_once "../../config/db.php";
require_once '../includes/auth.php';
requireRole('Scheduler', 'Super Admin');

$versions_result = $conn->query("
    SELECT
        cv.curriculum_version_id,
        cv.version_name,
        cv.created_at,
        COUNT(c.curriculum_id) AS subject_count,
        GROUP_CONCAT(DISTINCT sy.school_year) AS used_by
    FROM curriculum_versions cv
    LEFT JOIN curriculum c ON cv.curriculum_version_id = c.curriculum_version_id
    LEFT JOIN school_years sy ON cv.curriculum_version_id = sy.curriculum_version_id
    GROUP BY cv.curriculum_version_id
    ORDER BY cv.created_at DESC
");

$all_versions = [];
$all_versions_result = $conn->query("
    SELECT curriculum_version_id, version_name
    FROM curriculum_versions
    ORDER BY version_name DESC
");

if ($all_versions_result) {
    while ($row = $all_versions_result->fetch_assoc()) {
        $all_versions[] = $row;
    }
}

$sy_result = $conn->query("
    SELECT
        sy.school_year_id,
        sy.school_year,
        sy.is_active,
        sy.curriculum_version_id
    FROM school_years sy
    ORDER BY sy.school_year DESC
");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="page-transition lg:ml-72 pt-20 p-6 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto space-y-8">

        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-layers-fill"></i>
                    Version Controlled Curricula
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight">
                    Curriculum Versions
                </h1>

                <p class="text-blue-100 mt-1 text-sm max-w-2xl">
                    Create, snapshot, and deploy strand curriculum versions across academic school years.
                </p>
            </div>

            <div class="relative z-10">
                <button
                    type="button"
                    id="openCreateModal"
                    class="btn-accent px-5 py-3 rounded-xl font-bold text-xs text-slate-900 cursor-pointer shadow-lg transition duration-200 flex items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>New Curriculum Version</span>
                </button>
            </div>
        </div>

        <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-journal-text text-amber-300"></i>
                    <h2 class="font-bold text-base tracking-tight">
                        Version Snapshots
                    </h2>
                </div>

                <span class="text-xs bg-white/10 px-3 py-1 rounded-full text-blue-100 font-medium">
                    <?= $versions_result ? $versions_result->num_rows : 0 ?> Versions
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 border-b border-slate-200 text-slate-700 font-semibold text-xs">
                        <tr>
                            <th class="px-6 py-3.5">Version Name</th>
                            <th class="px-6 py-3.5">Date Created</th>
                            <th class="px-6 py-3.5">Assigned Subjects</th>
                            <th class="px-6 py-3.5">Active Deployment</th>
                            <th class="px-6 py-3.5 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php if ($versions_result && $versions_result->num_rows > 0): ?>
                            <?php while ($row = $versions_result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-6 py-4 font-bold text-slate-900 text-sm">
                                        <?= htmlspecialchars($row['version_name']) ?>
                                    </td>

                                    <td class="px-6 py-4 text-slate-500 font-mono">
                                        <?= htmlspecialchars(date('M j, Y', strtotime($row['created_at']))) ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="badge-pill bg-blue-50 text-blue-700 border border-blue-200 text-[11px]">
                                            <?= (int)$row['subject_count'] ?> subject entries
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-slate-600 font-medium">
                                        <?=
                                            $row['used_by']
                                                ? '<span class="text-emerald-700 font-bold font-mono">SY ' . htmlspecialchars($row['used_by']) . '</span>'
                                                : '<span class="text-slate-400 italic">Not deployed</span>'
                                        ?>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <form
                                            method="POST"
                                            action="curriculum-version-builder"
                                            class="inline">

                                            <input
                                                type="hidden"
                                                name="version_id"
                                                value="<?= (int)$row['curriculum_version_id'] ?>">

                                            <button
                                                type="submit"
                                                class="btn-primary px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer">
                                                <i class="bi bi-pencil-square"></i>
                                                <span>Open Matrix Builder</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400 text-xs">
                                    <i class="bi bi-layers text-3xl block mb-2 text-slate-300"></i>
                                    No curriculum versions created yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-calendar-check text-amber-300"></i>
                    <h2 class="font-bold text-base tracking-tight">
                        School Year Curriculum Deployment
                    </h2>
                </div>

                <button
                    type="button"
                    id="openCreateSchoolYearModal"
                    class="bg-white/10 hover:bg-white/20 border border-white/15 text-white px-4 py-2 rounded-xl text-xs font-bold cursor-pointer transition flex items-center gap-1.5">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>New School Year</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 border-b border-slate-200 text-slate-700 font-semibold text-xs">
                        <tr>
                            <th class="px-6 py-3.5">School Year</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Deployed Curriculum Version</th>
                            <th class="px-6 py-3.5 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php if ($sy_result && $sy_result->num_rows > 0): ?>
                            <?php while ($sy = $sy_result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-slate-900 text-sm">
                                        <?= htmlspecialchars($sy['school_year']) ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <?php if ($sy['is_active']): ?>
                                            <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm font-semibold text-emerald-700">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Active
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-2 rounded-xl bg-slate-100 border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-500">
                                                <i class="bi bi-circle"></i>
                                                Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <form
                                            method="POST"
                                            action="../../controllers/curriculum_controller.php">

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="assign_school_year">

                                            <input
                                                type="hidden"
                                                name="school_year_id"
                                                value="<?= (int)$sy['school_year_id'] ?>">

                                            <select
                                                name="curriculum_version_id"
                                                onchange="this.form.submit()"
                                                class="rounded-xl border border-slate-300 px-4 py-2 w-full max-w-xs text-xs font-semibold text-slate-800 bg-white cursor-pointer focus:ring-2 focus:ring-blue-600 focus:outline-none">

                                                <option value="" disabled>
                                                    &mdash; None assigned &mdash;
                                                </option>

                                                <?php foreach ($all_versions as $v): ?>
                                                    <option
                                                        value="<?= (int)$v['curriculum_version_id'] ?>"
                                                        <?= ((int)$sy['curriculum_version_id'] === (int)$v['curriculum_version_id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($v['version_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>

                                            </select>
                                        </form>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <?php if ($sy['is_active']): ?>
                                            <span class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm font-semibold text-emerald-700">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Current School Year
                                            </span>
                                        <?php else: ?>
                                            <form
                                                method="POST"
                                                action="../../controllers/curriculum_controller.php"
                                                class="school-year-status-form inline">

                                                <input
                                                    type="hidden"
                                                    name="action"
                                                    value="activate_school_year">

                                                <input
                                                    type="hidden"
                                                    name="school_year_id"
                                                    value="<?= (int)$sy['school_year_id'] ?>">

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-2 rounded-xl bg-slate-100 border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-500 hover:bg-slate-200">
                                                     <i class="bi bi-circle"></i>
                                                    Activate
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-8 text-slate-400 text-xs">
                                    No school years registered in the system.
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
    id="createModal"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">

    <div class="bg-white rounded-3xl w-full max-w-lg p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200">
        <div class="flex justify-between items-center border-b border-slate-200 pb-4">
            <h3 class="text-xl font-extrabold text-slate-900">
                New Curriculum Version
            </h3>

            <button
                type="button"
                id="closeCreateModal"
                class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-lg font-bold">
                &times;
            </button>
        </div>

        <form
            method="POST"
            action="../../controllers/curriculum_controller.php"
            class="space-y-4 text-xs">

            <input
                type="hidden"
                name="action"
                value="create_version">

            <div>
                <label class="block font-semibold text-slate-600 mb-1">
                    Curriculum Version Name
                </label>

                <input
                    type="text"
                    name="version_name"
                    required
                    placeholder="e.g. SY 2026-2027 Curriculum"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">
                    Duplicate Template From (Optional)
                </label>

                <select
                    name="source_version_id"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white cursor-pointer focus:ring-2 focus:ring-blue-600 focus:outline-none">

                    <option value="">
                        Start with blank matrix
                    </option>

                    <?php foreach ($all_versions as $v): ?>
                        <option value="<?= (int)$v['curriculum_version_id'] ?>">
                            <?= htmlspecialchars($v['version_name']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>

                <p class="text-[11px] text-slate-400 mt-1">
                    Copying will duplicate all strand-subject pairings into the new version.
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button
                    type="button"
                    id="cancelCreateModal"
                    class="px-5 py-2.5 border border-slate-300 rounded-xl font-semibold text-slate-700 hover:bg-slate-100 cursor-pointer">
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn-primary px-6 py-2.5 rounded-xl font-bold cursor-pointer shadow-md">
                    Create & Open Matrix
                </button>
            </div>
        </form>
    </div>
</div>

<div
    id="createSchoolYearModal"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">

    <div class="bg-white rounded-3xl w-full max-w-lg p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200">
        <div class="flex justify-between items-center border-b border-slate-200 pb-4">
            <h3 class="text-xl font-extrabold text-slate-900">
                New School Year
            </h3>

            <button
                type="button"
                id="closeCreateSchoolYearModal"
                class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-lg font-bold">
                &times;
            </button>
        </div>

        <form
            method="POST"
            action="../../controllers/curriculum_controller.php"
            class="space-y-4 text-xs">

            <input
                type="hidden"
                name="action"
                value="create_school_year">

            <div>
                <label class="block font-semibold text-slate-600 mb-1">
                    School Year
                </label>

                <input
                    type="text"
                    name="school_year"
                    required
                    pattern="\d{4}-\d{4}"
                    placeholder="e.g. 2026-2027"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-600 focus:outline-none">

                <p class="text-[11px] text-slate-400 mt-1">
                    Format: YYYY-YYYY, with the second year exactly one year after the first.
                </p>
            </div>

            <p class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                New school years are added as inactive. Activate the school year when you are ready to switch over.
            </p>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button
                    type="button"
                    id="cancelCreateSchoolYearModal"
                    class="px-5 py-2.5 border border-slate-300 rounded-xl font-semibold text-slate-700 hover:bg-slate-100 cursor-pointer">
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn-primary px-6 py-2.5 rounded-xl font-bold cursor-pointer shadow-md">
                    Add School Year
                </button>
            </div>
        </form>
    </div>
</div>

<script src="/enrollment_system/assets/js/swal.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const createModal = document.getElementById('createModal');
        const openCreateModal = document.getElementById('openCreateModal');
        const closeCreateModal = document.getElementById('closeCreateModal');
        const cancelCreateModal = document.getElementById('cancelCreateModal');

        if (createModal && openCreateModal) {
            openCreateModal.addEventListener('click', function () {
                createModal.classList.remove('hidden');
                createModal.classList.add('flex');
            });
        }

        function closeCreate() {
            if (!createModal) {
                return;
            }

            createModal.classList.add('hidden');
            createModal.classList.remove('flex');
        }

        if (closeCreateModal) {
            closeCreateModal.addEventListener('click', closeCreate);
        }

        if (cancelCreateModal) {
            cancelCreateModal.addEventListener('click', closeCreate);
        }

        const createSchoolYearModal = document.getElementById('createSchoolYearModal');
        const openCreateSchoolYearModal = document.getElementById('openCreateSchoolYearModal');
        const closeCreateSchoolYearModal = document.getElementById('closeCreateSchoolYearModal');
        const cancelCreateSchoolYearModal = document.getElementById('cancelCreateSchoolYearModal');

        if (createSchoolYearModal && openCreateSchoolYearModal) {
            openCreateSchoolYearModal.addEventListener('click', function () {
                createSchoolYearModal.classList.remove('hidden');
                createSchoolYearModal.classList.add('flex');
            });
        }

        function closeCreateSchoolYear() {
            if (!createSchoolYearModal) {
                return;
            }

            createSchoolYearModal.classList.add('hidden');
            createSchoolYearModal.classList.remove('flex');
        }

        if (closeCreateSchoolYearModal) {
            closeCreateSchoolYearModal.addEventListener('click', closeCreateSchoolYear);
        }

        if (cancelCreateSchoolYearModal) {
            cancelCreateSchoolYearModal.addEventListener('click', closeCreateSchoolYear);
        }

        document.querySelectorAll('.school-year-status-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'Activate School Year?',
                    text: 'This will make this the active school year and automatically deactivate the current school year.',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Activate',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#059669'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

<?php if (isset($_GET['assigned'])): ?>
    <script>
        Swal.fire({
            title: 'Saved!',
            text: 'The school year curriculum assignment has been successfully deployed.',
            icon: 'success',
            confirmButtonColor: '#0A1931',
            confirmButtonText: 'Done'
        }).then(function () {
            window.history.replaceState(
                {},
                document.title,
                window.location.pathname
            );
        });
    </script>
<?php endif; ?>