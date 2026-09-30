<?php 
require_once "config/db.php"; 
$page_title = "User Login - Masinag Senior High School";
include 'views/includes/header.php'; 

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>


<div class="page-transition min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-[#060F1E] via-[#0A1931] to-[#1E4DB7] relative overflow-hidden">
    <!-- Ambient Background Blobs -->
    <div class="hero-glow-blob w-96 h-96 bg-blue-500/20 -top-24 -left-24"></div>
    <div class="hero-glow-blob w-96 h-96 bg-amber-500/10 -bottom-24 -right-24"></div>

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-white/20 relative z-10">
        
        <!-- Header -->
        <div class="bg-gradient-to-b from-[#0A1931] to-[#0E254A] py-8 px-6 flex flex-col items-center text-center relative border-b border-white/10">
            <a href="/enrollment_system/" class="absolute top-4 left-4 text-xs font-semibold text-blue-200 hover:text-amber-300 transition flex items-center gap-1">
                <i class="bi bi-arrow-left"></i> Home
            </a>
            
            <div class="w-20 h-20 rounded-full bg-white/10 border-2 border-amber-400/40 p-1 flex items-center justify-center shadow-lg backdrop-blur-sm">
                <img src="/enrollment_system/assets/img/logo.png" alt="Masinag Senior High School Logo" class="w-full h-full object-contain rounded-full">
            </div>
            
            <h1 class="text-white text-2xl font-extrabold tracking-tight mt-4">
                Portal Login
            </h1>
            <p class="text-blue-200 text-xs mt-1 font-medium">
                Masinag Senior High School
            </p>
        </div>

        <!-- Form -->
        <div class="p-8 space-y-6">
            <?php if (!empty($error)): ?>
                <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-2.5">
                    <i class="bi bi-exclamation-circle-fill text-red-500 text-base"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="controllers/process_user_login.php" method="POST" class="space-y-5">
                
                <!-- Role Selector -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        Select Account Type
                    </label>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="role"
                                value="Student"
                                class="peer hidden"
                                checked>
                            <div class="border-2 border-slate-200 rounded-2xl py-3 text-center transition-all duration-200 text-slate-600 font-semibold text-sm flex items-center justify-center gap-2
                                        peer-checked:bg-[#0A1931]
                                        peer-checked:border-[#0A1931]
                                        peer-checked:text-amber-300
                                        peer-checked:shadow-md
                                        hover:border-slate-300">
                                <i class="bi bi-mortarboard-fill"></i>
                                <span>Student</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="role"
                                value="Teacher"
                                class="peer hidden">
                            <div class="border-2 border-slate-200 rounded-2xl py-3 text-center transition-all duration-200 text-slate-600 font-semibold text-sm flex items-center justify-center gap-2
                                        peer-checked:bg-[#0A1931]
                                        peer-checked:border-[#0A1931]
                                        peer-checked:text-amber-300
                                        peer-checked:shadow-md
                                        hover:border-slate-300">
                                <i class="bi bi-person-workspace"></i>
                                <span>Teacher</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Username / ID Number
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-person-fill text-lg"></i>
                        </span>
                        <input
                            type="text"
                            name="username"
                            id="username"
                            placeholder="Enter your username"
                            required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm text-slate-800 transition">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="text-xs font-bold uppercase tracking-wider text-slate-600">
                            Password
                        </label>
                        <a href="views/admin/forgot-password" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                            Forgot Password?
                        </a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-lock-fill text-lg"></i>
                        </span>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter your password"
                            required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm text-slate-800 transition">
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full btn-primary py-3.5 rounded-xl font-bold text-sm tracking-wide shadow-lg transition duration-200 cursor-pointer flex items-center justify-center gap-2">
                    <i class="bi bi-box-arrow-in-right text-base"></i>
                    <span>Login to Portal</span>
                </button>
            </form>

            <div class="pt-2 text-center text-xs text-slate-500 border-t border-slate-100">
                <span>Staff or Administrator? </span>
                <a href="views/admin/admin-login" class="font-bold text-blue-600 hover:text-blue-800 transition">
                    Admin Login
                </a>
            </div>
        </div>
    </div>
</div>

<script src="/enrollment_system/assets/js/swal.js"></script>

<?php if (isset($_SESSION['success_title'])): ?>
<script>
Swal.fire({
    title: <?= json_encode($_SESSION['success_title']) ?>,
    text: <?= json_encode($_SESSION['success_message']) ?>,
    icon: 'success',
    confirmButtonColor: '#0A1931',
    confirmButtonText: 'Done'
});
</script>
<?php unset($_SESSION['success_title'], $_SESSION['success_message']); ?>

<?php elseif (isset($_SESSION['error_title'])): ?>
<script>
Swal.fire({
    title: <?= json_encode($_SESSION['error_title']) ?>,
    text: <?= json_encode($_SESSION['error_message']) ?>,
    icon: 'error',
    confirmButtonColor: '#DC2626',
    confirmButtonText: 'OK'
});
</script>
<?php unset($_SESSION['error_title'], $_SESSION['error_message']); ?>
<?php endif; ?>