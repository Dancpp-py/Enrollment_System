<?php
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
        <a href="../professor/professor-dashboard" class="flex items-center gap-3">
            <img src="/enrollment_system/assets/img/logo.png" class="w-12 h-12 rounded-full p-0.5 sidebar-logo" alt="Masinag SHS Logo">
            <div class="sidebar-name min-w-0">
                <h1 class="text-lg font-bold tracking-tight text-white leading-tight truncate">Masinag SHS</h1>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-1 rounded-full text-[11px] font-semibold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <?= htmlspecialchars($role ?? 'Teacher') ?> 
                </span>
            </div>
        </a>
    </div>

    <!-- NAVIGATION MENU -->
    <nav class="flex-1 px-4 py-5 overflow-y-auto space-y-1">
    
        <!-- Dashboard Section -->
        <div class="sidebar-section-title">Overview</div>
        
        <a href="../professor/professor-dashboard" class="sidebar-nav-item <?= $current_page === 'professor-dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <!-- Grades Section -->
        <div class="sidebar-section-title mt-4">Grades Management</div>
        
        <a href="../professor/professor-grade-entry" class="sidebar-nav-item <?= in_array($current_page, ['professor-grades.php', 'professor-grade-entry.php']) ? 'active' : '' ?>">
            <i class="bi bi-journal-check"></i>
            <span>Grade Entry</span>
        </a>

        <!-- Sections Section -->
        <div class="sidebar-section-title mt-4">Class Management</div>

        <!-- Subjects Section -->
        <a href="../professor/professor-subjects" class="sidebar-nav-item <?= $current_page === 'professor-subjects.php' ? 'active' : '' ?>">
            <i class="bi bi-book-fill"></i>
            <span>Assigned Subjects & Sections</span>
        </a>

        <a href="../professor/professor-upload" class="sidebar-nav-item <?= $current_page === 'professor-upload.php' ? 'active' : '' ?>">
            <i class="bi bi-cloud-upload"></i>
            <span>Upload Materials</span>
        </a>

        <a href="../professor/professor-quiz" class="sidebar-nav-item <?= $current_page === 'professor-quiz.php' ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i>
            <span>Quiz Management</span>
        </a>

    </nav>
    

    <!-- FOOTER LOGOUT -->
    <div class="border-t border-white/10 p-4 bg-black/20">
        <form action="../../controllers/professor_logout" method="POST">
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
                    window.location.href = "../../controllers/professor_logout";
                }
            });
        });
    }
</script>