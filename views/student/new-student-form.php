<?php 
$page_title = "New Student Registration - Masinag SHS";

require_once "../../config/db.php"; 
include '../includes/header.php'; 
include '../includes/navbar.php'; 
?>

<main class="page-transition pt-28 pb-20 min-h-screen bg-slate-50">
    <form action="../../controllers/student_application.php" method="POST" enctype="multipart/form-data" class="max-w-5xl mx-auto px-4 sm:px-6 space-y-8">
        <input type="hidden" name="student_type" value="New">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 sm:p-10 shadow-xl text-white relative overflow-hidden text-center">
            <div class="hero-glow-blob w-72 h-72 bg-blue-400/20 -top-20 -left-20"></div>
            <div class="hero-glow-blob w-72 h-72 bg-amber-400/10 -bottom-20 -right-20"></div>
            <div class="relative z-10 space-y-2 max-w-2xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15">
                    <i class="bi bi-mortarboard-fill"></i> Senior High School Admission
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                    New Student Registration
                </h1>
                <p class="text-blue-100 text-sm">
                    Complete all mandatory application details and upload required credentials for Grade 11 admission.
                </p>
            </div>
        </div>

        <!-- COURSE/TRACK SECTION -->
        <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2.5">
                <i class="bi bi-compass-fill text-amber-300"></i>
                <h2 class="font-bold text-base tracking-tight">
                    Desired Track & Academic Program
                </h2>
            </div>
            <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Academic Strand / Track <span class="text-rose-500">*</span>
                    </label>
                    <select class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none cursor-pointer" name="strand_id" required>
                        <option value="" disabled selected>Select Track / Course</option>
                        <option value="1">STEM (Science, Technology, Engineering & Math)</option>
                        <option value="2">ABM (Accountancy, Business & Management)</option>
                        <option value="3">HUMSS (Humanities & Social Sciences)</option>
                        <option value="4">GAS (General Academic Strand)</option>
                        <option value="5">TVL (Technical-Vocational-Livelihood)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Grade Level <span class="text-rose-500">*</span>
                    </label>
                    <select class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none cursor-pointer" name="grade_level" required>
                        <option value="" disabled selected>Select Grade</option>
                        <option value="Grade 11">Grade 11</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- PERSONAL INFORMATION SECTION -->
        <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2.5">
                <i class="bi bi-person-fill text-amber-300"></i>
                <h2 class="font-bold text-base tracking-tight">
                    Personal Information
                </h2>
            </div>
            <div class="p-6 sm:p-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 text-xs">
                <div class="sm:col-span-2 md:col-span-1">
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Last Name <span class="text-rose-500">*</span></label>
                    <input type="text" placeholder="e.g. Dela Cruz" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none" name="per_Lname" required>
                </div>
                <div class="sm:col-span-2 md:col-span-1">
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">First Name <span class="text-rose-500">*</span></label>
                    <input type="text" placeholder="e.g. Juan" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none" name="per_Fname" required>
                </div>
                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Middle Name</label>
                    <input type="text" placeholder="e.g. Santos" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none" name="per_Mname">
                </div>
                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Suffix</label>
                    <input type="text" placeholder="Jr., III (Optional)" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none" name="per_suffix">
                </div>
                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Date of Birth <span class="text-rose-500">*</span></label>
                    <input type="date" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none" name="per_date_birth" required>
                </div>
                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Gender <span class="text-rose-500">*</span></label>
                    <select class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none cursor-pointer" name="per_gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" placeholder="example@email.com" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none" name="per_email" required>
                </div>
                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Contact Number <span class="text-rose-500">*</span></label>
                    <input type="number" placeholder="09XXXXXXXXX" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none" name="per_contact" required>
                </div>
                <div class="sm:col-span-2 md:col-span-4">
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">Complete Home Address <span class="text-rose-500">*</span></label>
                    <textarea rows="2" placeholder="House No., Street, Barangay, City, Province" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm resize-none focus:ring-2 focus:ring-blue-600 focus:outline-none" name="per_add" required></textarea>
                </div>
            </div>
        </section>

        <!-- PARENTS & GUARDIAN ACCORDION / SECTIONS -->
        <div class="grid md:grid-cols-2 gap-8">
            
            <!-- MOTHER'S INFORMATION -->
            <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
                <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2">
                    <i class="bi bi-heart-fill text-amber-300"></i>
                    <h2 class="font-bold text-sm tracking-tight">Mother's Information</h2>
                </div>
                <div class="p-6 space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Last Name</label>
                            <input type="text" placeholder="Enter Last Name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="mo_Lname">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">First Name</label>
                            <input type="text" placeholder="Enter First Name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="mo_Fname">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Middle Name</label>
                            <input type="text" placeholder="Middle Name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="mo_Mname">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Suffix</label>
                            <input type="text" placeholder="Optional" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="mo_suffix">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Contact Number</label>
                        <input type="number" placeholder="09XXXXXXXXX" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="mo_contact">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Complete Address</label>
                        <input type="text" placeholder="House No., Street, City, Province" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="mo_add">
                    </div>
                </div>
            </section>

            <!-- FATHER'S INFORMATION -->
            <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
                <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2">
                    <i class="bi bi-shield-fill text-amber-300"></i>
                    <h2 class="font-bold text-sm tracking-tight">Father's Information</h2>
                </div>
                <div class="p-6 space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Last Name</label>
                            <input type="text" placeholder="Enter Last Name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="fa_Lname">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">First Name</label>
                            <input type="text" placeholder="Enter First Name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="fa_Fname">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Middle Name</label>
                            <input type="text" placeholder="Middle Name" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="fa_Mname">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Suffix</label>
                            <input type="text" placeholder="Optional" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="fa_suffix">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Contact Number</label>
                        <input type="number" placeholder="09XXXXXXXXX" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="fa_contact">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Complete Address</label>
                        <input type="text" placeholder="House No., Street, City, Province" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" name="fa_add">
                    </div>
                </div>
            </section>
        </div>

        <!-- GUARDIAN INFORMATION -->
        <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2">
                <i class="bi bi-person-check-fill text-amber-300"></i>
                <h2 class="font-bold text-sm tracking-tight">Guardian Information</h2>
            </div>
            <div class="p-6 sm:p-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 text-xs">
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Guardian Last Name</label>
                    <input type="text" placeholder="Enter Last Name" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" name="gu_Lname">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Guardian First Name</label>
                    <input type="text" placeholder="Enter First Name" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" name="gu_Fname">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Middle Name</label>
                    <input type="text" placeholder="Enter Middle Name" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" name="gu_Mname">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Relationship to Student</label>
                    <select class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white cursor-pointer" name="gu_relation">
                        <option value="" selected disabled>Select Relationship</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Legalguardian">Legal Guardian</option>
                        <option value="Grandfather">Grand Father</option>
                        <option value="Grandmother">Grand Mother</option>
                        <option value="Brother">Brother</option>
                        <option value="Sister">Sister</option>
                        <option value="Otherrelative">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Contact Number</label>
                    <input type="number" placeholder="09XXXXXXXXX" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" name="gu_contact">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Complete Address</label>
                    <input type="text" placeholder="House No., Street, City, Province" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" name="gu_add">
                </div>
            </div>
        </section>

        <!-- EDUCATIONAL BACKGROUND SECTION -->
        <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2">
                <i class="bi bi-mortarboard-fill text-amber-300"></i>
                <h2 class="font-bold text-sm tracking-tight">Educational Background</h2>
            </div>
            <div class="p-6 sm:p-8 grid md:grid-cols-2 gap-5 text-xs">
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Elementary School Attended <span class="text-rose-500">*</span></label>
                    <input type="text" placeholder="School Name" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" name="elem" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Elementary Year Graduated <span class="text-rose-500">*</span></label>
                    <input type="number" placeholder="e.g. 2022" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" name="elem_yr" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Junior High School Attended <span class="text-rose-500">*</span></label>
                    <input type="text" placeholder="School Name" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" name="jhs" required>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">JHS Year Completed <span class="text-rose-500">*</span></label>
                    <input type="number" placeholder="e.g. 2026" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" name="jhs_yr" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold text-slate-600 mb-1">Learner Reference Number (LRN) <span class="text-rose-500">*</span></label>
                    <input type="number" placeholder="12-digit DepEd LRN" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 font-mono text-sm" name="lrn" required>
                </div>
            </div>
        </section>

        <!-- UPLOAD ADMISSION REQUIREMENTS -->
        <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white"> 
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-cloud-arrow-up-fill text-amber-300"></i>
                    <h2 class="font-bold text-sm tracking-tight">Upload Admission Requirements</h2>
                </div>
                <span class="text-xs bg-white/10 px-3 py-1 rounded-full text-blue-100 font-medium">PDF, JPG, PNG (Max 5MB)</span>
            </div>

            <div class="p-6 sm:p-8 space-y-5 text-xs"> 

                <!-- PSA -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="font-bold text-slate-800 text-sm">
                            1. PSA Birth Certificate
                        </label>
                        <span class="badge-pill bg-rose-100 text-rose-700 text-[11px]">Required</span>
                    </div>
                    <input type="file" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0A1931] file:text-white hover:file:bg-[#1E4DB7] file:cursor-pointer" name="up_psa" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <!-- Report Card -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="font-bold text-slate-800 text-sm">
                            2. Grade 10 Report Card (Form 138)
                        </label>
                        <span class="badge-pill bg-yellow-100 text-yellow-700 text-[11px]">Optional</span>
                    </div>
                    <input type="file" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0A1931] file:text-white hover:file:bg-[#1E4DB7] file:cursor-pointer" name="up_f138" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                
                <!-- Good Moral -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="font-bold text-slate-800 text-sm">
                            3. Certificate of Good Moral Character
                        </label>
                        <span class="badge-pill bg-yellow-100 text-yellow-700 text-[11px]">Optional</span>
                    </div>
                    <input type="file" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0A1931] file:text-white hover:file:bg-[#1E4DB7] file:cursor-pointer" name="ip_goodMoral" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <!-- 2x2 -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="font-bold text-slate-800 text-sm">
                            4. Recent 2x2 ID Picture
                        </label>
                        <span class="badge-pill bg-rose-100 text-rose-700 text-[11px]">Required</span>
                    </div>
                    <input type="file" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0A1931] file:text-white hover:file:bg-[#1E4DB7] file:cursor-pointer" name="up_2x2" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
        </section>

        <!-- CONFIRMATION -->
        <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2">
                <i class="bi bi-shield-check text-amber-300"></i>
                <h2 class="font-bold text-sm tracking-tight">Applicant Certification & Data Privacy</h2>
            </div>
            <div class="p-6 sm:p-8 space-y-6">
                <label class="flex items-start gap-3.5 cursor-pointer bg-slate-50 border border-slate-200 p-4 rounded-2xl hover:bg-blue-50/40 transition">
                    <input id="confirmCheck" name="confirm_information" type="checkbox" class="mt-0.5 w-5 h-5 rounded text-blue-700 accent-blue-700 cursor-pointer shrink-0">
                    <span class="text-xs text-slate-700 leading-relaxed font-medium">
                        I hereby certify that all information supplied above is true, correct, and complete. I understand that any false statement or omission may result in forfeiture of admission or disqualification from enrollment.
                    </span>
                </label>

                <div class="flex justify-end">
                    <button type="submit" id="submitBtn" disabled class="btn-primary px-8 py-3.5 rounded-xl font-bold text-sm tracking-wide shadow-md transition disabled:opacity-40 disabled:cursor-not-allowed">
                        Submit Official Application
                    </button>
                </div>
            </div>
        </section>
    </form>
</main>

<script src="../../assets/js/cs-validation/new-transferee-validation.js"></script>

<?php 
include '../includes/footer.php';

if (isset($_GET['success'])): ?>
 <script>
    Swal.fire({
        title: 'Application Submitted!',
        text: 'Thank you for submitting your enrollment application. Your application is now queued for evaluation by the Admissions Office.',
        icon: 'success',
        confirmButtonColor: '#0A1931',
        confirmButtonText: 'Done'
    }).then(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
    })
</script> 
<?php endif; ?>