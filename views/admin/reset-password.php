<?php
require_once "../../config/db.php";
$page_title = "Reset Password - Masinag Senior High School";
include '../includes/header.php';

$token = $_GET['token'] ?? '';
$error = $_GET['error'] ?? null;
 
$tokenValid = false;
 
if (!empty($token)) {
    $stmt = $conn->prepare("
        SELECT reset_id, expires_at, used
        FROM admin_password_resets
        WHERE token = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $reset = $stmt->get_result()->fetch_assoc();
    $stmt->close();
 
    if ($reset && (int)$reset['used'] === 0 && strtotime($reset['expires_at']) > time()) {
        $tokenValid = true;
    }
}
?>

<div class="page-transition min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-[#060F1E] via-[#0A1931] to-[#1E4DB7] relative overflow-hidden">
    <!-- Ambient Glows -->
    <div class="hero-glow-blob w-96 h-96 bg-blue-500/20 -top-24 -left-24"></div>
    <div class="hero-glow-blob w-96 h-96 bg-amber-500/10 -bottom-24 -right-24"></div>

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-white/20 relative z-10">

        <!-- Header -->
        <div class="bg-gradient-to-b from-[#0A1931] to-[#0E254A] py-8 px-6 flex flex-col items-center text-center relative border-b border-white/10">
            <div class="w-20 h-20 rounded-full bg-white/10 border-2 border-amber-400/40 p-1 flex items-center justify-center shadow-lg backdrop-blur-sm">
                <img src="/enrollment_system/assets/img/logo.png" alt="School Logo" class="w-full h-full object-contain rounded-full">
            </div>

            <h1 class="text-white text-2xl font-extrabold tracking-tight mt-4">
                Create New Password
            </h1>

            <p class="text-blue-200 text-xs mt-1.5 max-w-xs font-normal">
                Set a strong and secure password for your account.
            </p>
        </div>

        <div class="p-8 space-y-6">
            <?php if (!empty($error)): ?>
                <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-2.5">
                    <i class="bi bi-exclamation-circle-fill text-red-500 text-base"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>
 
            <?php if (!$tokenValid): ?>
                <div class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-xl p-4 text-center space-y-3">
                    <i class="bi bi-shield-exclamation text-amber-600 text-3xl block"></i>
                    <p class="font-medium">This reset link is invalid, expired, or has already been used.</p>
                    <a href="forgot-password" class="inline-flex items-center gap-2 text-xs font-bold text-blue-700 hover:underline">
                        <i class="bi bi-arrow-left"></i> Request a new reset link
                    </a>
                </div>
                
            <?php else: ?>

                <form id="resetPasswordForm" action="../../controllers/process_reset_password.php" method="POST" class="space-y-5">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            New Password
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i class="bi bi-key-fill text-lg"></i>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter new password"
                                required
                                minlength="8"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm text-slate-800 transition">
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm_password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i class="bi bi-lock-fill text-lg"></i>
                            </span>
                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="Confirm new password"
                                required
                                minlength="8"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm text-slate-800 transition">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <button
                        type="submit"
                        class="w-full btn-primary py-3.5 rounded-xl font-bold text-sm tracking-wide shadow-lg transition duration-200 cursor-pointer flex items-center justify-center gap-2">
                        <i class="bi bi-check-circle-fill text-base"></i>
                        <span>Update Password</span>
                    </button>
                </form>
            <?php endif; ?>

            <div class="text-center pt-2 border-t border-slate-100">
                <a href="admin-login" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-blue-700 transition">
                    <i class="bi bi-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</div>

<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/cs-validation/reset-password-validation.js"></script>

<?php if (isset($_GET['success'])): ?>
    <script>
        Swal.fire({
            title: 'Changed!',
            text: 'Your password has been changed.',
            icon: 'success',
            confirmButtonColor: '#0A1931',
            confirmButtonText: 'Done'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        })
    </script>
<?php endif; ?>