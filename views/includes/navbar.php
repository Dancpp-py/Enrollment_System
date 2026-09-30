<header class="fixed top-0 left-0 w-full z-50">
    <nav class="navbar backdrop-blur-md shadow-lg transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-20">

                <div class="flex items-center gap-3 min-w-0">
                    <a href="/enrollment_system/" class="flex items-center group">
                        <img src="/enrollment_system/assets/img/logo.png" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full p-1 navbar-logo" alt="Masinag SHS Logo">
                    </a>
                    
                    <div class="min-w-0">
                        <a href="/enrollment_system/" class="block">
                            <h2 class="text-white text-lg font-bold sm:text-xl tracking-tight truncate hover:text-amber-300 transition duration-200">
                                Masinag Senior High School
                            </h2>
                            <p class="text-xs text-blue-200 hidden sm:block font-medium">Official Enrollment Portal</p>
                        </a>
                    </div>
                </div>

                <ul class="hidden md:flex items-center gap-8 text-slate-100 font-medium text-sm lg:text-base">
                    <li>
                        <a href="/enrollment_system/#home" class="nav-link hover:text-amber-400 duration-200">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="/enrollment_system/#about" class="nav-link hover:text-amber-400 duration-200">
                            About
                        </a>
                    </li>
                    <li>
                        <a href="/enrollment_system/#announcement" class="nav-link hover:text-amber-400 duration-200">
                            Announcements
                        </a>
                    </li>
                    <li>
                        <a href="/enrollment_system/#contact" class="nav-link hover:text-amber-400 duration-200">
                            Contact
                        </a>
                    </li>
                </ul>

                <div class="flex items-center gap-3">
                    <div class="flex gap-2.5 enroll-btn">
                        <a href="/enrollment_system/student-teacher-login" class="px-5 py-2 rounded-full font-semibold text-sm text-white bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-sm transition duration-200 flex items-center gap-1.5 shadow-sm">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Login
                        </a>

                        <a href="/enrollment_system/views/student/requirements" class="btn-accent px-5 py-2 rounded-full font-bold text-sm text-slate-900 transition duration-200 flex items-center gap-1.5 shadow-md">
                            <i class="bi bi-pencil-square"></i>
                            Enroll Now
                        </a>
                    </div>

                    <!-- HAMBURGER MENU -->
                    <button id="menu-btn" class="md:hidden w-10 h-10 flex items-center justify-center text-white bg-white/10 hover:bg-white/20 rounded-xl border border-white/15 transition focus:outline-none" aria-label="Toggle Navigation Menu">
                        <i id="menu-icon" class="bi bi-list text-2xl font-bold"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- MOBILE MENU -->
        <div id="mobile-menu" class="hidden md:hidden pb-6 text-white border-t border-white/10">
            <ul class="text-center space-y-3 p-4">
                <li>
                    <a href="/enrollment_system/#home" class="block py-2 text-slate-200 hover:text-amber-400 font-medium rounded-lg hover:bg-white/5 transition duration-200">
                        <i class="bi bi-house me-2 text-amber-400"></i>
                        Home
                    </a>
                </li>
                <li>
                    <a href="/enrollment_system/#about" class="block py-2 text-slate-200 hover:text-amber-400 font-medium rounded-lg hover:bg-white/5 transition duration-200">
                        <i class="bi bi-info-circle me-2 text-amber-400"></i>
                        About
                    </a>
                </li>
                <li>
                    <a href="/enrollment_system/#announcement" class="block py-2 text-slate-200 hover:text-amber-400 font-medium rounded-lg hover:bg-white/5 transition duration-200">
                        <i class="bi bi-megaphone me-2 text-amber-400"></i>
                        Announcements
                    </a>
                </li>
                <li>
                    <a href="/enrollment_system/#contact" class="block py-2 text-slate-200 hover:text-amber-400 font-medium rounded-lg hover:bg-white/5 transition duration-200">
                        <i class="bi bi-envelope me-2 text-amber-400"></i>
                        Contact
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>