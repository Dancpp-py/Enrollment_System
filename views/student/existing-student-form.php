<?php 
$page_title = "Returning Student Re-enrollment - Masinag SHS";

require_once "../../config/db.php"; 
include '../includes/header.php'; 
include '../includes/navbar.php'; 
require '../includes/helpers.php'; 
?>

<main class="page-transition pt-28 pb-20 min-h-screen bg-slate-50">
    <form action="../../controllers/existing_enrollment.php" method="POST" class="max-w-4xl mx-auto px-4 sm:px-6 space-y-8">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl text-white relative overflow-hidden text-center">
            <div class="hero-glow-blob w-72 h-72 bg-blue-400/20 -top-20 -left-20"></div>
            <div class="relative z-10 space-y-2 max-w-2xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15">
                    <i class="bi bi-arrow-repeat"></i> Grade 11 &rarr; Grade 12 Advancement
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                    Returning Student Re-Enrollment
                </h1>
                <p class="text-blue-100 text-sm">
                    Enter your official Student ID Number to automatically load your academic record and submit continuing enrollment.
                </p>
            </div>
        </div>
        
        <!-- STUDENT ID SEARCH & ACADEMIC INFO -->
        <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2.5">
                <i class="bi bi-search text-amber-300"></i>
                <h2 class="font-bold text-base tracking-tight">
                    Student Identification & Academic Status
                </h2>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                <!-- Search Box -->
                <div class="max-w-md">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Official Student Number
                    </label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i class="bi bi-person-badge"></i>
                            </span>
                            <input
                                type="text"
                                id="student_number"
                                name="student_number"
                                placeholder="e.g. 2024-00123"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 font-mono text-sm uppercase focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        </div>
                        
                        <button type="button" id="searchStudent" class="btn-primary px-5 py-3 rounded-xl text-xs font-bold flex items-center gap-2 shadow-md cursor-pointer">
                            <i class="bi bi-search"></i>
                            <span>Find</span>
                        </button>
                    </div>
                </div>

                <hr class="border-slate-100">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                    <div class="md:col-span-2">
                        <label class="block font-semibold text-slate-600 mb-1">Full Student Name</label>
                        <input type="text" id="student_name" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-bold text-slate-800 text-sm" readonly placeholder="Will populate on search...">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Current Grade Level</label>
                        <input type="text" id="current_grade" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-slate-700" readonly>
                    </div>

                    <div>
                        <label class="block font-bold text-blue-700 mb-1">Advancing To Grade</label>
                        <input type="text" id="next_grade" class="w-full rounded-xl border border-blue-200 bg-blue-50/50 px-4 py-2.5 font-bold text-blue-800 text-sm" readonly>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Current Semester</label>
                        <input type="text" id="current_semester" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-slate-700" readonly>
                    </div>

                    <div>
                        <label class="block font-bold text-blue-700 mb-1">Next Semester</label>
                        <input type="text" id="next_semester" class="w-full rounded-xl border border-blue-200 bg-blue-50/50 px-4 py-2.5 font-bold text-blue-800 text-sm" readonly>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Academic Strand</label>
                        <input type="text" id="strand" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800" readonly>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Target School Year</label>
                        <input type="text" id="school_year" readonly class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-mono text-slate-700">
                    </div>
                </div>
            </div>
        </section>

        <!-- VERIFICATION SECTION -->
        <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2.5">
                <i class="bi bi-shield-check text-amber-300"></i>
                <h2 class="font-bold text-base tracking-tight">
                    Student Certification & Agreement
                </h2>
            </div>
            
            <div class="p-6 sm:p-8 space-y-6">
                <label class="flex items-start gap-3.5 cursor-pointer bg-slate-50 border border-slate-200 p-4 rounded-2xl hover:bg-blue-50/40 transition">
                    <input id="confirmCheck" name="confirm_information" type="checkbox" class="mt-0.5 w-5 h-5 rounded text-blue-700 accent-blue-700 cursor-pointer shrink-0">
                    <span class="text-xs text-slate-700 leading-relaxed font-medium">
                        I hereby certify that all information retrieved and displayed above is accurate and belongs to me. I understand that submitting this re-enrollment request initiates official registrar verification for the upcoming academic period.
                    </span>
                </label>
                
                <div class="flex justify-end">
                    <button type="submit" id="submitBtn" disabled class="btn-primary px-8 py-3.5 rounded-xl font-bold text-sm tracking-wide shadow-md transition disabled:opacity-40 disabled:cursor-not-allowed">
                        Submit Re-Enrollment Application
                    </button>
                </div>
            </div>
        </section>
    </form>
</main>

<script src="../../assets/js/cs-validation/existing-validation.js"></script>

<script>
document.getElementById("searchStudent").addEventListener("click", function(){
    const studentNumber = document.querySelector("[name='student_number']").value;
    const studentInput = document.querySelector("[name='student_number']");

    if (studentNumber.trim() === "") {
        studentInput.classList.add("border-red-500");
        return;
    }

    const formData = new FormData();
    formData.append("student_number", studentNumber);

    fetch("../../controllers/search_existing_student.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            Swal.fire({
                icon: "error",
                title: "Student Not Found",
                text: data.message,
                confirmButtonColor: '#0A1931',
                confirmButtonText: 'OK'
            });
            return;
        }

        document.getElementById("student_name").value = data.student_name;
        document.getElementById("current_grade").value = data.current_grade;
        document.getElementById("next_grade").value = data.next_grade;
        document.getElementById("current_semester").value = data.current_semester;
        document.getElementById("next_semester").value = data.next_semester;
        document.getElementById("strand").value = data.strand;
        document.getElementById("school_year").value = data.school_year;
    });
});
</script>

<?php 
include '../includes/footer.php'; 

if (isset($_GET['success'])): ?>
 <script>
    Swal.fire({
        title: 'Application Submitted!',
        text: 'Thank you for submitting your re-enrollment request. Your application is now queued for registrar review.',
        icon: 'success',
        confirmButtonColor: '#0A1931',
        confirmButtonText: 'Done'
    }).then(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
    })
</script> 
<?php endif; ?>
