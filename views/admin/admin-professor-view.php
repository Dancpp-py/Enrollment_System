<?php
require_once '../../config/db.php';
require_once '../includes/auth.php';
requireRole('Registrar', 'Super Admin');

include '../includes/header.php';
include '../includes/sidebar.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$teachers = $conn->query("
    SELECT professor_id, username, last_name, first_name, middle_name, contact_number, email, is_active
    FROM professors
    ORDER BY last_name, first_name
")->fetch_all(MYSQLI_ASSOC);
?>

<main class="lg:ml-72 min-h-screen p-6">
    <div class="max-w-7xl mx-auto space-y-8">

        <div class="bg-gradient-to-r from-[#0B1F4D] to-[#2563EB] rounded-2xl p-6 shadow-lg flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white">Teacher Accounts</h1>
                <p class="text-blue-100 mt-2">Create and manage teacher login accounts.</p>
            </div>
            <button onclick="document.getElementById('addTeacherModal').classList.remove('hidden')"
                class="bg-white text-[#0B1F4D] font-semibold px-5 py-3 rounded-xl hover:bg-blue-50 transition">
                + Add Teacher
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-100">
                    <tr class="text-left text-sm text-slate-700">
                        <th class="px-6 py-4 font-semibold">Name</th>
                        <th class="px-6 py-4 font-semibold">Username</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold">Contact</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($teachers)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                No teacher accounts yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($teachers as $t): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium">
                                    <?= htmlspecialchars($t['last_name'] . ', ' . $t['first_name']) ?>
                                    <?= $t['middle_name'] ? htmlspecialchars(' ' . $t['middle_name']) : '' ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $t['username'] ? htmlspecialchars($t['username']) : '<span class="text-slate-400">No account</span>' ?>
                                </td>
                                <td class="px-6 py-4"><?= htmlspecialchars($t['email'] ?? '') ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($t['contact_number'] ?? '') ?></td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($t['is_active']): ?>
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">Active</span>
                                    <?php else: ?>
                                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- ADD TEACHER MODAL -->
<div id="addTeacherModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl w-[480px] p-6">
        <h2 class="font-bold text-xl mb-4">Add Teacher</h2>
        <form method="POST" action="../../controllers/teacher_creation.php">
            <input type="hidden" name="action" value="create_teacher">

            <label class="block text-sm font-medium mb-1">First Name</label>
            <input name="first_name" required class="w-full border p-2 mb-3 rounded" placeholder="First Name">

            <label class="block text-sm font-medium mb-1">Last Name</label>
            <input name="last_name" required class="w-full border p-2 mb-3 rounded" placeholder="Last Name">

            <label class="block text-sm font-medium mb-1">Middle Name</label>
            <input name="middle_name" class="w-full border p-2 mb-3 rounded" placeholder="Middle Name (optional)">

            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" required class="w-full border p-2 mb-3 rounded" placeholder="teacher@school.edu">

            <label class="block text-sm font-medium mb-1">Contact Number</label>
            <input name="contact_number" class="w-full border p-2 mb-5 rounded" placeholder="09xxxxxxxxx">

            <p class="text-xs text-slate-500 mb-4">
                A username and temporary password will be generated automatically and shown after creation.
            </p>

            <div class="text-right space-x-2">
                <button type="button" onclick="document.getElementById('addTeacherModal').classList.add('hidden')" class="px-4 py-2 border rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#0B1F4D] text-white rounded">Create Account</button>
            </div>
        </form>
    </div>
</div>
