<?php 
require_once "../../config/db.php"; 
$page_title = "Staff & Admin Login - Masinag Senior High School";
include '../includes/header.php'; 
$error = $_GET['error'] ?? null;
?>

<div class="page-transition min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-[#060F1E] via-[#0A1931] to-[#1E4DB7] relative overflow-hidden">
    <!-- Background Glows -->
    <div class="hero-glow-blob w-96 h-96 bg-blue-600/20 -top-24 -right-24"></div>
    <div class="hero-glow-blob w-96 h-96 bg-amber-500/10 -bottom-24 -left-24"></div>

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-white/20 relative z-10">
        
        <!-- Header -->
        <div class="bg-gradient-to-b from-[#0A1931] to-[#0E254A] py-8 px-6 flex flex-col items-center text-center relative border-b border-white/10">
            <a href="/enrollment_system/index" class="absolute top-4 left-4 text-xs font-semibold text-blue-200 hover:text-amber-300 transition flex items-center gap-1">
                <i class="bi bi-arrow-left"></i> Home
            </a>

            <div class="w-20 h-20 rounded-full bg-white/10 border-2 border-amber-400/40 p-1 flex items-center justify-center shadow-lg backdrop-blur-sm">
                <img src="/enrollment_system/assets/img/logo.png" alt="Masinag Senior High School Logo" class="w-full h-full object-contain rounded-full">
            </div>
            
            <h1 class="text-white text-2xl font-extrabold tracking-tight mt-4">
                Staff & Admin Portal
            </h1>
            <p class="text-blue-200 text-xs mt-1 font-medium">
                Authorized Personnel Only
            </p>
        </div>

        <!-- Form Container -->
        <div class="p-8 space-y-6">
            <?php if (!empty($error)): ?>
                <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-2.5">
                    <i class="bi bi-exclamation-circle-fill text-red-500 text-base"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="../../controllers/process_admin_login.php" method="POST" class="space-y-5">
                
                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Staff Username
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-person-badge-fill text-lg"></i>
                        </span>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            placeholder="Enter admin username" 
                            required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm text-slate-800 transition"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="text-xs font-bold uppercase tracking-wider text-slate-600">
                            Password
                        </label>
                        <a href="forgot-password" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
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
                            placeholder="Enter password"
                            required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm text-slate-800 transition"
                        >
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full btn-primary py-3.5 rounded-xl font-bold text-sm tracking-wide shadow-lg transition duration-200 cursor-pointer flex items-center justify-center gap-2">
                    <i class="bi bi-shield-lock-fill text-base"></i>
                    <span>Authenticate & Login</span>
                </button>
            </form>

            <div class="pt-2 text-center text-xs text-slate-500 border-t border-slate-100">
                <span>Student or Teacher? </span>
                <a href="../../student-teacher-login" class="font-bold text-blue-600 hover:text-blue-800 transition">
                    User Login
                </a>
            </div>
        </div>
    </div>
</div>

<script src="/enrollment_system/assets/js/cs-validation/admin-login-validation.js"></script>