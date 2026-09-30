<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Mobile Backdrop Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm hidden lg:hidden transition-opacity duration-300"></div>

<!-- Mobile Hamburger Toggle Button -->
<button id="menuBtn" type="button" aria-label="Toggle Navigation Menu" class="fixed top-3.5 left-3.5 z-50 p-2.5 rounded-xl bg-[#0A1931]/90 text-white shadow-lg lg:hidden border border-white/15 backdrop-blur-md hover:bg-[#132A52] focus:outline-none focus:ring-2 focus:ring-amber-400 transition duration-200">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

<aside id="sidebar" class="fixed top-0 left-0 z-50 sidebar h-screen w-72 text-white shadow-2xl flex flex-col -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out bg-gradient-to-b from-[#0A1931] via-[#132A52] to-[#0A1931]">
    
    <!-- BRAND HEADER -->
    <div class="border-b border-white/10 px-5 py-4 flex items-center justify-between bg-black/20">
        <a href="../student/student-dashboard" class="flex items-center gap-3 min-w-0">
            <img src="/enrollment_system/assets/img/logo.png" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full p-0.5 border border-white/20 sidebar-logo flex-shrink-0" alt="Masinag SHS Logo">
            <div class="sidebar-name min-w-0">
                <h1 class="text-base sm:text-lg font-extrabold tracking-tight text-white leading-tight truncate">Masinag SHS</h1>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <?= htmlspecialchars($role ?? 'Student') ?> 
                </span>
            </div>
        </a>
        <!-- Mobile Close Button inside Sidebar -->
        <button id="closeSidebarBtn" type="button" aria-label="Close Sidebar" class="lg:hidden text-blue-200 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
            <i class="bi bi-x-lg text-lg"></i>
        </button>
    </div>

    <!-- NAVIGATION MENU -->
    <nav class="flex-1 px-3.5 py-4 overflow-y-auto space-y-1.5 scrollbar-thin scrollbar-thumb-white/10">
    
        <!-- Dashboard Section -->
        <div class="sidebar-section-title px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-blue-200/60">
            Overview
        </div>
        
        <a href="../student/student-dashboard" class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150 <?= in_array($current_page, ['student-dashboard.php', 'student-dashboard']) ? 'active bg-white/15 text-amber-300 shadow-sm border border-white/10' : 'text-blue-100 hover:bg-white/10 hover:text-white' ?>">
            <i class="bi bi-speedometer2 text-base"></i>
            <span>Dashboard</span>
        </a>

         <a href="../student/student-calendar" class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150 <?= in_array($current_page, ['student-calendar.php', 'student-calendar']) ? 'active bg-white/15 text-amber-300 shadow-sm border border-white/10' : 'text-blue-100 hover:bg-white/10 hover:text-white' ?>">
            <i class="bi bi-calendar3 text-base"></i>
            <span>Academic Calendar</span>
        </a>

        <a href="../student/student-curriculum" class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150 <?= in_array($current_page, ['student-curriculum.php', 'student-curriculum']) ? 'active bg-white/15 text-amber-300 shadow-sm border border-white/10' : 'text-blue-100 hover:bg-white/10 hover:text-white' ?>">
            <i class="bi bi-clipboard-check-fill text-base"></i>
            <span>Curriculum</span>
        </a>

        <div class="pt-3">
            <div class="sidebar-section-title px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-blue-200/60">
                My Subjects
            </div>
            
            <a href="../student/student-subject-view" class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150 <?= in_array($current_page, ['student-subject-view.php', 'student-subject-view']) ? 'active bg-white/15 text-amber-300 shadow-sm border border-white/10' : 'text-blue-100 hover:bg-white/10 hover:text-white' ?>">
                <i class="bi bi-journal-check text-base"></i>
                <span>Subjects</span>
            </a>
        </div>

        <div class="pt-3">
            <div class="sidebar-section-title px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-blue-200/60">
                Grades
            </div>
            
            <a href="../student/student-grades" class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150 <?= in_array($current_page, ['student-grades.php', 'student-grades']) ? 'active bg-white/15 text-amber-300 shadow-sm border border-white/10' : 'text-blue-100 hover:bg-white/10 hover:text-white' ?>">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Grades Viewing</span>
            </a>
        </div>

        <div class="pt-3">
            <div class="sidebar-section-title px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-blue-200/60">
                My Quizzes
            </div>
            
            <a href="../student/student-quiz" class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150 <?= in_array($current_page, ['student-quiz.php', 'student-quiz-v2.php']) ? 'active bg-white/15 text-amber-300 shadow-sm border border-white/10' : 'text-blue-100 hover:bg-white/10 hover:text-white' ?>">
                <i class="bi bi-clipboard-check-fill"></i>
                <span>Quiz</span>
            </a>
        </div>

    </nav>
    
    <!-- FOOTER LOGOUT -->
    <div class="border-t border-white/10 p-4 bg-black/20">
        <form action="../../controllers/student_logout.php" method="POST">
            <button id="logoutBtn" type="submit" class="w-full flex items-center justify-center gap-2 bg-red-600/80 hover:bg-red-600 active:scale-[0.98] text-white rounded-xl py-2.5 text-xs sm:text-sm font-bold transition duration-200 cursor-pointer border border-red-500/30 shadow-md" name="logoutBtn">
               <i class="bi bi-box-arrow-right text-base"></i>
               <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<script src="/enrollment_system/assets/js/swal.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Mobile Sidebar Drawer Toggle Logic
        const menuBtn = document.getElementById("menuBtn");
        const closeSidebarBtn = document.getElementById("closeSidebarBtn");
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("sidebarOverlay");

        function openSidebar() {
            if (sidebar) sidebar.classList.remove("-translate-x-full");
            if (overlay) overlay.classList.remove("hidden");
        }

        function closeSidebar() {
            if (sidebar) sidebar.classList.add("-translate-x-full");
            if (overlay) overlay.classList.add("hidden");
        }

        if (menuBtn) menuBtn.addEventListener("click", openSidebar);
        if (closeSidebarBtn) closeSidebarBtn.addEventListener("click", closeSidebar);
        if (overlay) overlay.addEventListener("click", closeSidebar);

        // Logout SweetAlert Modal Logic
        const logoutBtnEl = document.getElementById("logoutBtn");
        if (logoutBtnEl) {
            logoutBtnEl.addEventListener("click", function (e) {
                e.preventDefault();

                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirm Logout',
                        text: 'Are you sure you want to end your session?',
                        showCancelButton: true,
                        confirmButtonColor: '#DC2626',
                        cancelButtonColor: '#475569',
                        confirmButtonText: 'Yes, Logout',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'rounded-xl px-4 py-2 font-bold',
                            cancelButton: 'rounded-xl px-4 py-2 font-bold'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "../../controllers/student_logout.php";
                        }
                    });
                } else {
                    if (confirm("Are you sure you want to end your session?")) {
                        window.location.href = "../../controllers/student_logout.php";
                    }
                }
            });
        }
    });
</script>