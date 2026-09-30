<?php 
$page_title = "Masinag Senior High School - Official Online Enrollment Portal";
require_once "config/db.php"; 
include 'views/includes/header.php'; 
include 'views/includes/navbar.php'; 
?>



<!-- HERO SECTION -->
<section id="home" class="page-transition pt-32 sm:pt-36 hero-section overflow-hidden relative">
    <!-- Ambient Background Glows -->
    <div class="hero-glow-blob w-96 h-96 bg-blue-500/20 -top-20 -left-20"></div>
    <div class="hero-glow-blob w-[32rem] h-[32rem] bg-indigo-600/20 top-40 -right-20"></div>
    
    <div class="max-w-7xl mx-auto px-6 sm:px-8 relative z-10">
        <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            
            <!-- Left Hero Content -->
            <div class="lg:col-span-7 inner-content space-y-6">
                <div class="inline-flex items-center gap-2.5 bg-white/10 backdrop-blur-md text-amber-300 px-4 py-2 rounded-full border border-white/15 text-xs sm:text-sm font-semibold shadow-inner">
                    <i class="bi bi-mortarboard-fill text-amber-400"></i>
                    <span>Senior High School Online Enrollment 2026-2027</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight tracking-tight">
                    Begin Your Academic Journey <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-amber-200">With Us</span>
                </h1>

                <p class="text-slate-200 text-base sm:text-lg leading-relaxed max-w-2xl font-normal">
                    Enroll online quickly and securely. Submit requirements, track your examination schedule, and complete registration seamlessly through one unified digital platform.
                </p>

                <!-- CTA Actions -->
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="views/student/requirements.php" class="btn-accent px-8 py-3.5 rounded-full font-bold text-slate-900 transition duration-200 flex items-center gap-2 text-base shadow-lg">
                        <i class="bi bi-pencil-square"></i>
                        <span>Enroll Now</span>
                    </a>
                    <a href="student-teacher-login.php" class="px-7 py-3.5 rounded-full font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md transition duration-200 flex items-center gap-2 text-base">
                        <i class="bi bi-person-check-fill"></i>
                        <span>Student / Staff Login</span>
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="grid grid-cols-3 gap-4 pt-8 border-t border-white/10 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-amber-400 text-xl">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-white">Fast Track</h4>
                            <p class="text-xs text-slate-300">Easy Online Form</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-amber-400 text-xl">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-white">Encrypted</h4>
                            <p class="text-xs text-slate-300">Secure Records</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-amber-400 text-xl">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-white">24/7 Portal</h4>
                            <p class="text-xs text-slate-300">Anywhere Access</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Hero Image & Floating Cards -->
            <div class="lg:col-span-5 relative flex justify-center items-center right-content">
                <div class="absolute w-72 h-72 sm:w-96 sm:h-96 bg-blue-600/30 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute w-64 h-64 sm:w-80 sm:h-80 border-2 border-white/20 rounded-full animate-spin" style="animation-duration: 25s;"></div>

                <img src="assets/img/logo.png" class="relative w-64 sm:w-80 md:w-96 drop-shadow-2xl z-10 transition-transform duration-500 hover:scale-105" alt="Masinag SHS Badge">

                <!-- Floating Card Top Left -->
                <div class="absolute top-2 -left-4 sm:left-2 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl p-4 flex gap-3.5 items-center border border-slate-100 z-20 hover:-translate-y-1 transition duration-200">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-500 text-2xl">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">DepEd Accredited</h4>
                        <p class="text-slate-500 text-xs font-medium">STEM, ABM, HUMSS, TVL, GAS</p>
                    </div>
                </div>

                <!-- Floating Card Bottom Right -->
                <div class="absolute bottom-2 -right-4 sm:right-2 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl p-4 flex gap-3.5 items-center border border-slate-100 z-20 hover:-translate-y-1 transition duration-200">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 text-2xl">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Now Enrolling</h4>
                        <p class="text-slate-500 text-xs font-medium">S.Y. 2026 - 2027</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Smooth Bottom Curve -->
    <div class="relative w-full overflow-hidden leading-none mt-12">
        <svg class="relative block w-full h-12 text-slate-50" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118.08,130.83,121.31,200.75,108,241.6,100.22,282.16,78.29,321.39,56.44Z" fill="currentColor"></path>
        </svg>
    </div>
</section>

<!-- ABOUT US SECTION -->
<section id="about" class="py-24 bg-slate-50 relative">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">

        <div class="text-center max-w-3xl mx-auto">
            <span class="text-xs font-bold uppercase tracking-widest text-blue-700 bg-blue-100 px-3.5 py-1 rounded-full">Excellence in Education</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 tracking-tight">
                Why Choose Masinag Senior High School?
            </h2>
            <p class="text-slate-600 mt-3 text-base sm:text-lg">
                We empower students with academic rigor, certified vocational training, and modern digital tools designed for seamless student progression.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mt-16">

            <!-- Feature 1 -->
            <div class="card-elevated p-8 flex flex-col justify-between group">
                <div>
                    <div class="w-14 h-14 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-center text-blue-700 text-2xl group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                        <i class="bi bi-book-half"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mt-6 group-hover:text-blue-700 transition">
                        Quality Curriculum
                    </h3>
                    <p class="text-slate-600 mt-3 text-sm leading-relaxed">
                        Industry-aligned tracks and experienced faculty preparing learners for university success and competitive global careers.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center text-xs font-semibold text-blue-700">
                    <span>DepEd K-12 Compliant</span>
                    <i class="bi bi-arrow-right ms-auto"></i>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="card-elevated p-8 flex flex-col justify-between group">
                <div>
                    <div class="w-14 h-14 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center text-emerald-600 text-2xl group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mt-6 group-hover:text-emerald-700 transition">
                        Digital Enrollment Portal
                    </h3>
                    <p class="text-slate-600 mt-3 text-sm leading-relaxed">
                        Say goodbye to long lines. Complete your enrollment, submit documents, and monitor review status in real-time from any device.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center text-xs font-semibold text-emerald-700">
                    <span>Paperless System</span>
                    <i class="bi bi-arrow-right ms-auto"></i>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="card-elevated p-8 flex flex-col justify-between group">
                <div>
                    <div class="w-14 h-14 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-center text-amber-600 text-2xl group-hover:bg-amber-500 group-hover:text-slate-900 transition duration-300">
                        <i class="bi bi-building-check"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mt-6 group-hover:text-amber-700 transition">
                        State-of-the-Art Facilities
                    </h3>
                    <p class="text-slate-600 mt-3 text-sm leading-relaxed">
                        Equipped with science laboratories, computer labs, multimedia library, and student-friendly creative learning hubs.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center text-xs font-semibold text-amber-700">
                    <span>Modern Infrastructure</span>
                    <i class="bi bi-arrow-right ms-auto"></i>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ANNOUNCEMENTS SECTION -->
<section id="announcement" class="py-24 bg-white border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-14">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-blue-700 bg-blue-100 px-3.5 py-1 rounded-full">School Updates</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 tracking-tight">
                    Latest Announcements
                </h2>
                <p class="text-slate-600 mt-2 text-base">
                    Stay informed with our official schedules, admissions guidelines, and campus notices.
                </p>
            </div>
            <a href="views/student/requirements.php" class="inline-flex items-center gap-2 text-sm font-bold text-blue-700 hover:text-blue-900 transition">
                <span>View Enrollment Checklist</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- News 1 -->
            <div class="card-elevated overflow-hidden group">
                <div class="relative h-52 overflow-hidden bg-slate-100">
                    <img src="assets/img/banner-1.png" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Enrollment Schedule">
                    <div class="absolute top-3 left-3 bg-blue-900/90 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-bold">
                        Admissions
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-2">
                        <i class="bi bi-calendar3 text-blue-600"></i>
                        <span>March 28, 2026</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-700 transition leading-snug">
                        Enrollment Schedule Released
                    </h3>
                    <p class="text-slate-600 mt-2.5 text-sm leading-relaxed">
                        Online enrollment for incoming Grade 11 students officially opens. Prepare all digital copies of required certificates.
                    </p>
                </div>
            </div>

            <!-- News 2 -->
            <div class="card-elevated overflow-hidden group">
                <div class="relative h-52 overflow-hidden bg-slate-100">
                    <img src="assets/img/banner-2.png" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Orientation Program">
                    <div class="absolute top-3 left-3 bg-emerald-700/90 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-bold">
                        Campus Life
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-2">
                        <i class="bi bi-calendar3 text-emerald-600"></i>
                        <span>May 18, 2026</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 group-hover:text-emerald-700 transition leading-snug">
                        Orientation Program & Campus Tour
                    </h3>
                    <p class="text-slate-600 mt-2.5 text-sm leading-relaxed">
                        Welcome assembly for freshmen and transferee students to be held in the Main Gymnasium. Attendance is highly encouraged.
                    </p>
                </div>
            </div>

            <!-- News 3 -->
            <div class="card-elevated overflow-hidden group">
                <div class="relative h-52 overflow-hidden bg-slate-100">
                    <img src="assets/img/banner-3.png" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Scholarship Programs">
                    <div class="absolute top-3 left-3 bg-amber-600/90 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-bold">
                        Scholarship
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-2">
                        <i class="bi bi-calendar3 text-amber-600"></i>
                        <span>June 4, 2026</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 group-hover:text-amber-700 transition leading-snug">
                        Academic Scholarship Applications
                    </h3>
                    <p class="text-slate-600 mt-2.5 text-sm leading-relaxed">
                        Qualified honor graduates and voucher recipients may submit their documentary evidence through the scholarship desk.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- CALL TO ACTION BANNER -->
<section class="py-16 bg-gradient-to-r from-[#0A1931] via-[#1E4DB7] to-[#0A1931] text-white relative overflow-hidden">
    <div class="max-w-5xl mx-auto px-6 text-center relative z-10 space-y-6">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
            Ready to Join the Masinag SHS Community?
        </h2>
        <p class="text-blue-100 text-base sm:text-lg max-w-2xl mx-auto">
            Take the first step toward your bright academic and professional future. Register in just a few minutes.
        </p>
        <div class="pt-2 flex flex-wrap justify-center gap-4">
            <a href="views/student/requirements" class="btn-accent px-8 py-3.5 rounded-full font-bold text-slate-900 shadow-xl text-base flex items-center gap-2">
                <i class="bi bi-pencil-square"></i>
                <span>Start Enrollment Now</span>
            </a>
            <a href="student-teacher-login" class="px-8 py-3.5 rounded-full font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-sm transition duration-200">
                <span>Check Application Status</span>
            </a>
        </div>
    </div>
</section>

<?php include 'views/includes/footer.php'; ?>