    <!-- FOOTER SECTION -->
    <footer id="contact" class="footer-section bg-[#07132B] text-white pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-6 sm:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 lg:gap-16">

                <!-- School Info -->
                <div class="footer-header space-y-4">
                    <div class="flex items-center gap-3.5">
                        <img src="/enrollment_system/assets/img/logo.png" alt="Masinag SHS Logo" class="w-14 h-14 rounded-full p-1 bg-white/10 border border-white/20">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-white">Masinag SHS</h2>
                            <p class="text-xs text-amber-400 font-semibold tracking-wider uppercase">Senior High School</p>
                        </div>
                    </div>

                    <p class="text-slate-300 text-sm leading-relaxed">
                        Dedicated to academic excellence, leadership development, and empowering students with world-class 21st-century education.
                    </p>

                    <div class="flex gap-3 pt-2 footer-icon">
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 hover:bg-amber-400 hover:text-slate-900 duration-200 flex items-center justify-center border border-white/15" aria-label="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 hover:bg-amber-400 hover:text-slate-900 duration-200 flex items-center justify-center border border-white/15" aria-label="Twitter">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 hover:bg-amber-400 hover:text-slate-900 duration-200 flex items-center justify-center border border-white/15" aria-label="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 hover:bg-amber-400 hover:text-slate-900 duration-200 flex items-center justify-center border border-white/15" aria-label="YouTube">
                            <i class="bi bi-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-white tracking-wide flex items-center gap-2">
                        <i class="bi bi-link-45deg text-amber-400"></i> Quick Links
                    </h3>
                    <div class="w-12 h-1 bg-amber-400 rounded-full"></div>
                    <ul class="space-y-2.5 text-sm text-slate-300">
                        <li>
                            <a href="/enrollment_system/index#home" class="hover:text-amber-400 transition duration-200 flex items-center gap-2">
                                <i class="bi bi-chevron-right text-xs text-amber-400"></i> Home Portal
                            </a>
                        </li>
                        <li>
                            <a href="/enrollment_system/views/student/requirements" class="hover:text-amber-400 transition duration-200 flex items-center gap-2">
                                <i class="bi bi-chevron-right text-xs text-amber-400"></i> Admission Requirements
                            </a>
                        </li>
                        <li>
                            <a href="/enrollment_system/student-teacher-login" class="hover:text-amber-400 transition duration-200 flex items-center gap-2">
                                <i class="bi bi-chevron-right text-xs text-amber-400"></i> Student & Teacher Login
                            </a>
                        </li>
                        <li>
                            <a href="/enrollment_system/views/admin/admin-login" class="hover:text-amber-400 transition duration-200 flex items-center gap-2">
                                <i class="bi bi-chevron-right text-xs text-amber-400"></i> Staff & Admin Portal
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="footer-contact space-y-4">
                    <h3 class="text-lg font-bold text-white tracking-wide flex items-center gap-2">
                        <i class="bi bi-geo-alt-fill text-amber-400"></i> Contact Us
                    </h3>
                    <div class="w-12 h-1 bg-amber-400 rounded-full"></div>

                    <div class="space-y-3.5 text-sm text-slate-300">
                        <div class="flex items-start gap-3">
                            <i class="bi bi-envelope-fill mt-1 text-amber-400 text-base"></i>
                            <span class="break-all">masinag.shs.admissions@gmail.com</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="bi bi-telephone-fill mt-1 text-amber-400 text-base"></i>
                            <span>(046) 416-0000 / 0949-946-3161</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="bi bi-pin-map-fill mt-1 text-amber-400 text-base"></i>
                            <span>
                                114 Pasong Bayog, Brgy. Masinag,<br>
                                Dasmariñas City, Cavite
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="border-t border-white/10 mt-14 pt-8 text-center text-xs text-slate-400 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p>© <?= date('Y') ?> Masinag Senior High School. All Rights Reserved.</p>
                <p class="text-slate-400">Secure Online Enrollment Management System</p>
            </div>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <script src="/enrollment_system/assets/js/script.js"></script>
    <script src="/enrollment_system/assets/js/navbar.js"></script>
    <script src="/enrollment_system/assets/js/sidebar.js"></script>
    <script src="/enrollment_system/assets/js/swal.js"></script>
</body>
</html>