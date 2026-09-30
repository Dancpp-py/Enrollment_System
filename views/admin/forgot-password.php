<?php 
require_once "../../config/db.php"; 
$page_title = "Forgot Password - Masinag Senior High School";
include '../includes/header.php'; 

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
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
                Forgot Password
            </h1>

            <p class="text-blue-200 text-xs mt-1.5 max-w-xs font-normal">
                Enter your registered email address to receive password reset instructions.
            </p>
        </div>

        <!-- Form Container -->
        <div class="p-8 space-y-6">
            
            <!-- Success Message -->
            <?php if (!empty($success)): ?>
                <div class="text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 flex items-center gap-2.5">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-base"></i>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-2.5">
                    <i class="bi bi-exclamation-circle-fill text-red-500 text-base"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="../../controllers/send_reset_password.php" method="POST" class="space-y-5">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Registered Email Address
                    </label>

                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-envelope-fill text-lg"></i>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="e.g. yourname@gmail.com"
                            required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm text-slate-800 transition"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full btn-primary py-3.5 rounded-xl font-bold text-sm tracking-wide shadow-lg transition duration-200 cursor-pointer flex items-center justify-center gap-2">
                    <i class="bi bi-send-fill text-base"></i>
                    <span>Send Reset Link</span>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="text-center pt-2 border-t border-slate-100">
                <a href="admin-login" class="inline-flex items-center gap-2 text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                    <i class="bi bi-arrow-left"></i>
                    <span>Back to Admin Login</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/cs-validation/forgot-password-validation.js"></script>

<?php if (isset($_GET['success'])): ?>
    <script>
        Swal.fire({
            title: 'Email Sent!',
            text: 'If that email is registered, a password reset link has been sent.',
            icon: 'success',
            confirmButtonColor: '#0A1931',
            confirmButtonText: 'Done'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        })
    </script>
<?php endif; ?>