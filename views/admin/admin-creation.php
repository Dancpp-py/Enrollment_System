<?php
$page_title = "Create Admin Account - Masinag SHS";
require_once '../../config/db.php';
require_once '../includes/auth.php';
requireRole('Super Admin');
include '../includes/header.php';

$error = $_GET['error'] ?? null;
?>

<div class="page-transition min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-[#060F1E] via-[#0A1931] to-[#1E4DB7] relative overflow-hidden">
    <!-- Glows -->
    <div class="hero-glow-blob w-96 h-96 bg-blue-500/20 -top-24 -left-24"></div>
    <div class="hero-glow-blob w-96 h-96 bg-amber-500/10 -bottom-24 -right-24"></div>

    <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden border border-white/20 relative z-10">
        
        <!-- Header -->
        <div class="bg-gradient-to-b from-[#0A1931] to-[#0E254A] py-8 px-6 flex flex-col items-center text-center relative border-b border-white/10">
            <div class="w-20 h-20 rounded-full bg-white/10 border-2 border-amber-400/40 p-1 flex items-center justify-center shadow-lg backdrop-blur-sm">
                <i class="bi bi-person-plus-fill text-amber-300 text-3xl"></i>
            </div>
            
            <h1 class="text-white text-2xl font-extrabold tracking-tight mt-4">
                Create Staff Account
            </h1>
            <p class="text-blue-200 text-xs mt-1 font-medium">
                Super Admin Access Only
            </p>
        </div>

        <div class="p-8 space-y-6">
            <?php if (!empty($error)): ?>
                <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-2.5">
                    <i class="bi bi-exclamation-circle-fill text-red-500 text-base"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="../../controllers/process_admin_create.php" method="POST" class="space-y-4 text-xs">

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-person-fill text-base"></i>
                        </span>
                        <input type="text" name="username" required placeholder="e.g. jdelacruz" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm text-slate-800">
                    </div>
                </div>
                
                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-envelope-fill text-base"></i>
                        </span>
                        <input type="email" name="email" required placeholder="e.g. staff@masinagshs.edu.ph" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Staff Role</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-shield-check text-base"></i>
                        </span>
                        <select name="role" required class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm text-slate-800 bg-white cursor-pointer">
                            <option value="" selected disabled>Select User Role</option>
                            <option value="Registrar">Registrar</option>
                            <option value="Admissions">Admissions</option>
                            <option value="Scheduler">Scheduler</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Temporary Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-key-fill text-base"></i>
                        </span>
                        <input type="password" name="password" required minlength="8" placeholder="Minimum 8 characters" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm text-slate-800">
                    </div>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="submit" class="flex-1 btn-primary py-3.5 rounded-xl font-bold text-sm tracking-wide shadow-lg transition duration-200 cursor-pointer flex items-center justify-center gap-2">
                        <i class="bi bi-person-plus-fill"></i>
                        <span>Create Account</span>
                    </button>
        
                    <a href="admin-dashboard" class="px-5 py-3.5 border border-slate-300 rounded-xl font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer flex items-center justify-center text-sm" title="Back">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>