<?php

$role = $_SESSION['admin_role'] ?? null;

$username = $_SESSION['admin_username'] ?? 'Staff Member';

$current_page = basename($_SERVER['PHP_SELF']);

?>

<!-- Mobile Hamburger Toggle Button -->

<button id="menuBtn" class="fixed top-4 left-4 z-50 p-2.5 rounded-xl bg-slate-900/90 text-white shadow-xl md:hidden border border-white/10 backdrop-blur-md hover:bg-blue-900 transition duration-200">

    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">

        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>

    </svg>

</button>

<aside id="sidebar" class="fixed top-0 left-0 z-50 sidebar h-screen w-72 text-white shadow-2xl flex flex-col -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">

    <!-- BRAND HEADER -->

    <div class="border-b border-white/10 px-6 py-5 flex items-center gap-3.5 bg-black/20">

        <a href="../admin/admin-dashboard" class="flex items-center gap-3">

            <img src="/enrollment_system/assets/img/logo.png" class="w-12 h-12 rounded-full p-0.5 sidebar-logo" alt="Masinag SHS Logo">

            <div class="sidebar-name min-w-0">

                <h1 class="text-lg font-bold tracking-tight text-white leading-tight truncate">Masinag SHS</h1>

                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-1 rounded-full text-[11px] font-semibold bg-amber-400/20 text-amber-300 border border-amber-400/30">

                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>

                    <?= htmlspecialchars($role ?? 'Admin') ?>

                </span>

            </div>

        </a>

    </div>

    <!-- NAVIGATION MENU -->

    <nav class="flex-1 px-4 py-5 overflow-y-auto space-y-1">

        <?php if(in_array($role, ['Admissions', 'Registrar', 'Super Admin'], true)): ?>

        <div class="sidebar-section-title">Admissions & Review</div>

        <?php endif; ?>

        <?php if ($role === 'Super Admin'): ?>

        <a href="../admin/admin-dashboard-summary" class="sidebar-nav-item <?= $current_page === 'admin-dashboard-summary.php' ? 'active' : '' ?>">

            <i class="bi bi-speedometer2"></i>

            <span>Dashboard Summary</span>

        </a>

        <?php endif; ?>

        <?php if(in_array($role, ['Admissions', 'Super Admin'], true)): ?>

        <a href="../admin/admin-dashboard" class="sidebar-nav-item <?= in_array($current_page, ['admin-dashboard.php', 'admin-dashboard-v2.php']) ? 'active' : '' ?>">

            <i class="bi bi-person-lines-fill"></i>

            <span>Application Review</span>

        </a>

        <?php endif; ?>

        <?php if(in_array($role, ['Registrar', 'Super Admin'], true)): ?>

        <a href="../admin/admin-exam-scoring" class="sidebar-nav-item <?= in_array($current_page, ['admin-exam-scoring.php', 'admin-exam-scoring-v2.php']) ? 'active' : '' ?>">

            <i class="bi bi-clipboard2-check-fill"></i>

            <span>Exam Scoring Review</span>

        </a>

        <?php endif; ?>

        <?php if(in_array($role, ['Registrar', 'Super Admin'], true)): ?>

        <a href="../admin/admin-enrollment-review" class="sidebar-nav-item <?= in_array($current_page, ['admin-enrollment-review.php', 'admin-enrollment-review-v2.php']) ? 'active' : '' ?>">

            <i class="bi bi-patch-check-fill"></i>

            <span>Official Enrollment</span>

        </a>

        <?php endif; ?>

        <?php if(in_array($role, ['Admissions', 'Super Admin'], true)): ?>

            <a href="../admin/admin-reenrollment" class="sidebar-nav-item <?= $current_page === 'admin-reenrollment.php' ? 'active' : '' ?>">

                <i class="bi bi-arrow-repeat"></i>

                <span>Re-Enrollment Review</span>

            </a>

        <?php endif; ?>

        <?php if(in_array($role, ['Scheduler', 'Super Admin'], true)): ?>

            <div class="sidebar-section-title mt-4">Academic Planning</div>

            <a href="../scheduler/academic-management" class="sidebar-nav-item <?= $current_page === 'academic-management.php' ? 'active' : '' ?>">

                <i class="bi bi-people-fill"></i>

                <span>Class Management</span>

            </a>

            <a href="../admin/admin-calendar" class="sidebar-nav-item <?= $current_page === 'admin-calendar.php' ? 'active' : '' ?>">

                <i class="bi bi-calendar3 text-base"></i>

                <span>Academic Calendar</span>

            </a>


        <?php endif; ?> 



        <?php if(in_array($role, ['Scheduler', 'Super Admin'], true)): ?>

            <a href="../scheduler/subject-bank" class="sidebar-nav-item <?= $current_page === 'subject-bank.php' ? 'active' : '' ?>">

                <i class="bi bi-journal-bookmark-fill"></i>

                <span>Subject Bank</span>

            </a>

        <?php endif; ?>

        <?php if(in_array($role, ['Scheduler', 'Super Admin'], true)): ?>

            <a href="../scheduler/curriculum-builder" class="sidebar-nav-item <?= in_array($current_page, ['curriculum-builder.php', 'curriculum-version-builder.php']) ? 'active' : '' ?>">

                <i class="bi bi-diagram-3-fill"></i>

                <span>Curriculum Builder</span>

            </a>

        <?php endif; ?>

        <?php if(in_array($role, ['Registrar', 'Super Admin'], true)): ?>

        <a href="../admin/grading-periods" class="sidebar-nav-item <?= $current_page === 'grading-periods.php' ? 'active' : '' ?>">

            <i class="bi bi-calendar2-check-fill"></i>

            <span>Grading Period</span>

        </a>

        <?php endif; ?>



        <?php if(in_array($role, ['Registrar', 'Admissions', 'Super Admin'], true)): ?>

            <div class="sidebar-section-title mt-4">Records & Archive</div>

            <a href="../admin/admin-summary" class="sidebar-nav-item <?= in_array($current_page, ['admin-summary.php', 'admin-summary-v2.php']) ? 'active' : '' ?>">

                <i class="bi bi-folder2-open"></i>

                <span>Student Records</span>

            </a>

            <?php endif; ?>


            <?php if ($role === 'Super Admin'): ?>

                <div class="sidebar-section-title mt-4">System Administration</div>

                <a href="../admin/admin-creation" class="sidebar-nav-item <?= $current_page === 'admin-creation.php' ? 'active' : '' ?>">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Create Account</span>
                </a>

                <a href="../admin/professor-create-account" class="sidebar-nav-item <?= $current_page === 'professor-create-account.php' ? 'active' : '' ?>">
                    <i class="bi bi-person-fill-add"></i>
                    <span>Create Teacher Account</span>
                </a>

                <a href="../admin/admin-management" class="sidebar-nav-item <?= $current_page === 'admin-management.php' ? 'active' : '' ?>">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Account Management</span>
                </a>

        <?php endif; ?>

    </nav>

    <!-- FOOTER LOGOUT -->

    <div class="border-t border-white/10 p-4 bg-black/20">

        <form action="../../controllers/admin_logout.php" method="POST">

            <button id="logoutBtn" type="submit" class="w-full flex items-center justify-center gap-2 bg-red-600/80 hover:bg-red-600 text-white rounded-xl py-2.5 font-medium transition duration-200 cursor-pointer border border-red-500/30 shadow-sm" name="logoutBtn">

               <i class="bi bi-box-arrow-right"></i>

               <span>Logout</span>

            </button>

        </form>

    </div>

</aside>

<script>

    const logoutBtnEl = document.getElementById("logoutBtn");

    if (logoutBtnEl) {

        logoutBtnEl.addEventListener("click", function (e) {

            e.preventDefault();

            Swal.fire({

                icon: 'warning',

                title: 'Confirm Logout',

                text: 'Are you sure you want to end your session?',

                showCancelButton: true,

                confirmButtonColor: '#DC2626',

                cancelButtonColor: '#475569',

                confirmButtonText: 'Yes, Logout',

                cancelButtonText: 'Cancel'

            }).then((result) => {

                if (result.isConfirmed) {

                    window.location.href = "../../controllers/admin_logout.php";

                }

            });

        });

    }

</script>