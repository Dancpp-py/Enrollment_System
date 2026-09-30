<?php
$page_title = "Subject Bank - Masinag SHS";

require_once "../../config/db.php";
require_once "../includes/auth.php";
requireRole('Scheduler', 'Super Admin');

$subjects = [];
$result = $conn->query("
    SELECT
        s.subject_id,
        s.subject_code,
        s.subject_name,
        s.units,
        COUNT(c.curriculum_id) AS usage_count
    FROM subjects s
    LEFT JOIN curriculum c ON c.subject_id = s.subject_id
    GROUP BY s.subject_id
    ORDER BY s.subject_code
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<main class="page-transition lg:ml-72 pt-20 p-6 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-journal-bookmark-fill"></i> Subject Master Catalog
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight">Subject Bank</h1>
                <p class="text-blue-100 mt-1 text-sm max-w-2xl">Create and maintain the centralized curriculum subject library across all Senior High School tracks.</p>
            </div>

            <div class="relative z-10 bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 text-center min-w-[140px]">
                <span class="text-3xl font-extrabold text-amber-300"><?= count($subjects) ?></span>
                <p class="text-xs text-blue-100 font-medium mt-0.5">Total Subjects</p>
            </div>
        </div>

        <!-- CREATE SUBJECT CARD -->
        <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2.5">
                <i class="bi bi-plus-circle-fill text-amber-300"></i>
                <h2 class="font-bold text-base tracking-tight">Create Master Subject</h2>
            </div>

            <form id="subjectForm" method="POST" action="../../controllers/subject_bank.php" class="p-6 sm:p-8 space-y-5 text-xs">
                <input type="hidden" name="action" value="create_subject">

                <div class="grid md:grid-cols-3 gap-5">
                    <div>
                        <label for="subject_code" class="block font-semibold text-slate-700 mb-1.5">
                            Subject Code
                        </label>
                        <input type="text" id="subject_code" name="subject_code" placeholder="e.g. GENMATH11" required autocomplete="off" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono uppercase focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>

                    <div>
                        <label for="subject_name" class="block font-semibold text-slate-700 mb-1.5">
                            Subject Description / Title
                        </label>
                        <input type="text" id="subject_name" name="subject_name" placeholder="e.g. General Mathematics" required autocomplete="off" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>

                    <div>
                        <label for="units" class="block font-semibold text-slate-700 mb-1.5">
                            Credit Units
                        </label>
                        <input type="number" id="units" name="units" min="1" max="10" placeholder="e.g. 3" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold focus:ring-2 focus:ring-blue-600 focus:outline-none">
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-slate-100">
                    <button type="submit"
                            id="submitBtn"
                            class="btn-primary px-6 py-2.5 rounded-xl font-bold text-xs tracking-wide shadow-md transition duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        <span>Add to Subject Bank</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- SUBJECT LIST -->
        <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-collection-fill text-amber-300"></i>
                    <h2 class="font-bold text-base tracking-tight">Master Subject Catalog</h2>
                </div>
                <span class="text-xs bg-white/10 px-3 py-1 rounded-full text-blue-100 font-medium">
                    <?= count($subjects) ?> Registered
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 border-b border-slate-200 text-slate-700 font-semibold text-xs">
                        <tr>
                            <th class="px-6 py-3.5">Code</th>
                            <th class="px-6 py-3.5">Subject Description</th>
                            <th class="px-6 py-3.5 text-center">Units</th>
                            <th class="px-6 py-3.5 text-center">Curriculum Usage</th>
                            <th class="px-6 py-3.5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php if (empty($subjects)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <i class="bi bi-journal-x text-3xl block mb-2 text-slate-300"></i>
                                    No subjects currently registered in the catalog.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($subjects as $subject): ?>
                                <tr class="hover:bg-slate-50/80 transition" data-subject-id="<?= $subject['subject_id'] ?>">
                                    <td class="px-6 py-4 font-mono font-bold text-blue-800">
                                        <?= htmlspecialchars($subject['subject_code']) ?>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-800 text-sm">
                                        <?= htmlspecialchars($subject['subject_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-slate-700">
                                        <?= htmlspecialchars($subject['units']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($subject['usage_count'] > 0): ?>
                                            <span class="badge-pill bg-blue-50 text-blue-700 border border-blue-200 text-[11px]">
                                                <?= (int) $subject['usage_count'] ?> curriculum<?= $subject['usage_count'] == 1 ? '' : 's' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-pill bg-slate-100 text-slate-500 border border-slate-200 text-[11px]">
                                                Unassigned
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button"
                                                onclick='openEditModal(<?= json_encode($subject) ?>)'
                                                class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 flex items-center justify-center transition cursor-pointer" title="Edit Subject">
                                                <i class="bi bi-pencil-fill text-xs"></i>
                                            </button>

                                            <form method="POST" action="../../controllers/subject_bank.php" class="delete-subject-form inline-block">
                                                <input type="hidden" name="action" value="delete_subject">
                                                <input type="hidden" name="subject_id" value="<?= $subject['subject_id'] ?>">
                                                <button type="submit"
                                                    <?= $subject['usage_count'] > 0 ? 'disabled title="Cannot delete: assigned to active curriculum."' : 'title="Delete Subject"' ?>
                                                    class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed">
                                                    <i class="bi bi-trash-fill text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- EDIT SUBJECT MODAL -->
<div id="editModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200">
        <div class="flex justify-between items-center border-b border-slate-200 pb-4">
            <h3 class="font-extrabold text-xl text-slate-900">Edit Master Subject</h3>
            <button type="button" onclick="closeEditModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-lg font-bold">&times;</button>
        </div>

        <form method="POST" action="../../controllers/subject_bank.php" class="space-y-4 text-xs">
            <input type="hidden" name="action" value="update_subject">
            <input type="hidden" name="subject_id" id="edit_subject_id">

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Subject Code</label>
                <input type="text" name="subject_code" id="edit_subject_code" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono uppercase">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Subject Description</label>
                <input type="text" name="subject_name" id="edit_subject_name" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Credit Units</label>
                <input type="number" name="units" id="edit_units" min="1" max="10" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 border border-slate-300 rounded-xl font-semibold text-slate-700 hover:bg-slate-100 cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl font-bold cursor-pointer shadow-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    function openEditModal(subject) {
        document.getElementById('edit_subject_id').value = subject.subject_id;
        document.getElementById('edit_subject_code').value = subject.subject_code;
        document.getElementById('edit_subject_name').value = subject.subject_name;
        document.getElementById('edit_units').value = subject.units;

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>

<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/subject-creation.js"></script>