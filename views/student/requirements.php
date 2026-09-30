<?php 
require_once "../../config/db.php"; 
$page_title = "Admission Requirements - Masinag Senior High School";
include '../includes/header.php'; 
include '../includes/navbar.php'; 
?>

<section class="page-transition min-h-screen bg-slate-50 pt-28 pb-20">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
        
        <!-- Header Section -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-widest text-blue-700 bg-blue-100 px-4 py-1.5 rounded-full inline-block">
                Admissions S.Y. 2026-2027
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold mt-4 text-slate-900 tracking-tight">
                Student Enrollment Categories
            </h1>
            <p class="text-slate-600 mt-3 text-base sm:text-lg">
                Please select your applicant category below to review the required documents and proceed with your online application.
            </p>
        </div>

        <!-- 3 Enrollment Cards Grid -->
        <div class="grid lg:grid-cols-3 gap-8">
            
            <!-- 1. New Student -->
            <div class="card-elevated flex flex-col justify-between overflow-hidden border border-slate-200/80 bg-white">
                <div>
                    <!-- Card Top Banner -->
                    <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white p-6 relative overflow-hidden">
                        <div class="flex items-center justify-between relative z-10">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-300">Category 01</span>
                                <h2 class="text-2xl font-bold text-white mt-0.5">New Student</h2>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-amber-300 text-2xl border border-white/15">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                        </div>
                        <p class="text-blue-100 text-xs mt-2 relative z-10">For incoming Grade 11 junior high school completers</p>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 sm:p-8 space-y-5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">
                            Required Documents
                        </h3>

                        <ul class="space-y-3.5 text-sm text-slate-700">
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>Moving Up Certificate / Photocopy of JHS Diploma</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>2 pcs. PSA Birth Certificate (Photocopy)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>2 pcs. PSA Marriage Contract (if married)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>4 pcs. 2x2 Recent ID Picture (White background with nametag)</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Footer Action -->
                <div class="p-6 sm:p-8 pt-0">
                    <button class="w-full btn-accent py-3.5 rounded-xl font-bold text-sm text-slate-900 flex items-center justify-center gap-2 cursor-pointer shadow-md" onclick="window.location.href='new-student-form'">
                        <span>Proceed as New Student</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- 2. Transferee -->
            <div class="card-elevated flex flex-col justify-between overflow-hidden border border-slate-200/80 bg-white">
                <div>
                    <!-- Card Top Banner -->
                    <div class="bg-gradient-to-r from-[#1E4DB7] to-[#2563EB] text-white p-6 relative overflow-hidden">
                        <div class="flex items-center justify-between relative z-10">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-300">Category 02</span>
                                <h2 class="text-2xl font-bold text-white mt-0.5">Transferee</h2>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-amber-300 text-2xl border border-white/15">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>
                        </div>
                        <p class="text-blue-100 text-xs mt-2 relative z-10">For students transferring from other SHS institutions</p>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 sm:p-8 space-y-5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">
                            Required Documents
                        </h3>

                        <ul class="space-y-3.5 text-sm text-slate-700">
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>Form 138 / Report Card (Original & Photocopy)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>Certificate of Good Moral Character</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>Moving Up Certificate / JHS Diploma</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>2 pcs. PSA Birth Certificate (Photocopy)</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>4 pcs. 2x2 ID Picture with Nametag</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Footer Action -->
                <div class="p-6 sm:p-8 pt-0">
                    <button class="w-full btn-accent py-3.5 rounded-xl font-bold text-sm text-slate-900 flex items-center justify-center gap-2 cursor-pointer shadow-md" onclick="window.location.href='transferee-student-form'">
                        <span>Proceed as Transferee</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- 3. Existing Student -->
            <div class="card-elevated flex flex-col justify-between overflow-hidden border border-slate-200/80 bg-white">
                <div>
                    <!-- Card Top Banner -->
                    <div class="bg-gradient-to-r from-[#0F2347] to-[#1E3A8A] text-white p-6 relative overflow-hidden">
                        <div class="flex items-center justify-between relative z-10">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-300">Category 03</span>
                                <h2 class="text-2xl font-bold text-white mt-0.5">Existing Student</h2>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-amber-300 text-2xl border border-white/15">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                        </div>
                        <p class="text-blue-100 text-xs mt-2 relative z-10">For continuing Grade 11 students advancing to Grade 12</p>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 sm:p-8 space-y-5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">
                            Required Documents
                        </h3>

                        <ul class="space-y-3.5 text-sm text-slate-700">
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>Completed Re-enrollment Form</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>Signed Clearance Form</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>Latest Report Card / Copy of Official Grades</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="bi bi-check-circle-fill text-blue-600 text-base shrink-0 mt-0.5"></i>
                                <span>Parent / Guardian Consent Form</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Footer Action -->
                <div class="p-6 sm:p-8 pt-0">
                    <button class="w-full btn-accent py-3.5 rounded-xl font-bold text-sm text-slate-900 flex items-center justify-center gap-2 cursor-pointer shadow-md" onclick="window.location.href='existing-student-form'">
                        <span>Proceed with Re-enrollment</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>