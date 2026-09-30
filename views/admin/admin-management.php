<?php
session_start();

include '../../config/db.php';
require_once '../includes/auth.php';
requireRole('Super Admin');

$stmt = $conn->prepare("
    SELECT admin_id, username, role, is_active
    FROM admins
    ORDER BY role ASC, username ASC
");
$stmt->execute();
$result = $stmt->get_result();

$page_title = "Account Management - Masinag SHS";
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/helpers.php';
?>

<main class="page-transition lg:ml-72 pt-20 p-6 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-shield-lock-fill"></i> System Security
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight">Staff Account Management</h1>
                <p class="text-blue-100 mt-1 text-sm max-w-2xl">Manage staff privileges, activate or deactivate administrative credentials, and safeguard portal access.</p>
            </div>

            <div class="relative z-10">
                <a href="admin-creation" class="btn-accent px-5 py-2.5 rounded-xl font-bold text-xs text-slate-900 flex items-center gap-2 shadow-md">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Add New Staff</span>
                </a>
            </div>
        </div>

        <!-- Table Container -->
        <div class="card-elevated overflow-hidden">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="bi bi-people-fill text-amber-300 text-lg"></i>
                    <h2 class="font-bold text-base tracking-tight">Active & Inactive Staff Accounts</h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 border-b border-slate-200 text-slate-700 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Account Profile</th>
                            <th class="px-6 py-4 text-center">System Role</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Access Control</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200/80">
                        <?php while ($admin = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Account -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg font-bold shrink-0 border border-slate-200">
                                        <i class="bi bi-person-badge-fill"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 leading-tight">
                                            <?= htmlspecialchars($admin['username']) ?>
                                        </p>
                                        <p class="text-xs text-slate-500 mt-0.5">Staff ID #<?= htmlspecialchars($admin['admin_id']) ?></p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Role -->
                            <td class="px-6 py-4 text-center">
                                <?php
                                    $roleBadge = match($admin['role']) {
                                        'Super Admin' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        'Registrar'   => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'Admissions'  => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'Scheduler'   => 'bg-amber-100 text-amber-800 border-amber-200',
                                        default       => 'bg-slate-100 text-slate-800 border-slate-200'
                                    };
                                ?>
                                <span class="badge-pill border <?= $roleBadge ?>">
                                    <?= htmlspecialchars($admin['role']) ?>
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 text-center">
                                <?php if ($admin['is_active']): ?>
                                    <span class="badge-pill badge-complete">
                                        <i class="bi bi-check-circle-fill"></i> Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge-pill badge-incomplete">
                                        <i class="bi bi-slash-circle-fill"></i> Inactive
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Action -->
                            <td class="px-6 py-4 text-center">
                                <?php if ($admin['role'] === 'Super Admin' && $admin['username'] === ($_SESSION['admin_username'] ?? '')): ?>
                                    <span class="text-xs text-slate-400 italic">Self (Protected)</span>
                                <?php else: ?>
                                    <form 
                                        action="../../controllers/process_admin_status.php" 
                                        method="POST" 
                                        class="inline-block"
                                        onsubmit="return confirmAdminStatus(this);">

                                        <input
                                            type="hidden"
                                            name="admin_id"
                                            value="<?= $admin['admin_id'] ?>">

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="<?= $admin['is_active'] ? 'deactivate' : 'activate' ?>">

                                        <button
                                            type="submit"
                                            title="<?= $admin['is_active'] ? 'Deactivate Account' : 'Activate Account' ?>"
                                            class="<?= $admin['is_active']
                                                ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border-rose-200'
                                                : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200' ?> border px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-sm cursor-pointer flex items-center gap-1.5 mx-auto">
                                            <?php if ($admin['is_active']): ?>
                                                <i class="bi bi-lock-fill"></i> Deactivate
                                            <?php else: ?>
                                                <i class="bi bi-unlock-fill"></i> Activate
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="/enrollment_system/assets/js/swal.js"></script>

<script>
function confirmAdminStatus(form) {
    const action = form.querySelector('input[name="action"]').value;
    const actionText = action === 'deactivate' ? 'Deactivate this staff account?' : 'Activate this staff account?';
    
    event.preventDefault();
    Swal.fire({
        title: 'Confirm Action',
        text: actionText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: action === 'deactivate' ? '#DC2626' : '#0A1931',
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>

<?php
$stmt->close();
?>

<?php if (isset($_SESSION['success_title'])): ?>
    <script>
        Swal.fire({
            title: <?= json_encode($_SESSION['success_title']) ?>,
            text: <?= json_encode($_SESSION['success_message']) ?>,
            icon: "success",
            confirmButtonColor: '#0A1931',
            confirmButtonText: "Done"
        });
    </script>
<?php
    unset($_SESSION['success_title'], $_SESSION['success_message']);
    endif;
?>
