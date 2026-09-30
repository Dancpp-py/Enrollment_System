<?php 
$page_title = "Re-enrollment Applications - Masinag SHS";
include '../../config/db.php'; 
require_once '../includes/auth.php';
requireRole('Admissions', 'Super Admin');

include '../includes/header.php';  
include '../includes/sidebar.php';

$sql = "SELECT
            e.enrollment_id,
            s.first_name,
            s.last_name,
            s.student_type,
            e.grade_level,
            e.semester,
            sy.school_year,
            st.strand_name
        FROM enrollments e
        INNER JOIN students s
            ON e.student_id = s.student_id
        INNER JOIN school_years sy
            ON e.school_year_id = sy.school_year_id
        INNER JOIN strands st
            ON e.strand_id = st.strand_id
        WHERE e.status = 'Pending'
            AND s.student_type = 'Existing'
        ORDER BY e.enrollment_id DESC";

$result = $conn->query($sql);
$pending_count = $result ? $result->num_rows : 0;
?>

<main class="page-transition lg:ml-72 pt-20 p-6 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-arrow-repeat"></i> Continuing Students
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight">
                    Re-Enrollment Review
                </h1>
                <p class="text-blue-100 text-sm mt-1 max-w-2xl">
                    Review and confirm re-enrollment applications submitted by existing Grade 11 students advancing to Grade 12.
                </p>
            </div>

            <div class="relative z-10 bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 text-center min-w-[140px]">
                <span class="text-3xl font-extrabold text-amber-300"><?= $pending_count ?></span>
                <p class="text-xs text-blue-100 font-medium mt-0.5">Pending Review</p>
            </div>
        </div>

        <!-- Table Container -->
        <div class="card-elevated overflow-hidden">
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="bi bi-people-fill text-amber-300 text-lg"></i>
                    <h2 class="font-bold text-base tracking-tight">
                        Continuing Student Applications
                    </h2>
                </div>
                <span class="text-xs font-semibold bg-white/10 px-3 py-1 rounded-full text-blue-100">
                    <?= $pending_count ?> Records
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 border-b border-slate-200 text-slate-700 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Student Name</th>
                            <th class="px-6 py-4">Student Type</th>
                            <th class="px-6 py-4">Grade Level</th>
                            <th class="px-6 py-4">Semester</th>
                            <th class="px-6 py-4">School Year</th>
                            <th class="px-6 py-4">Strand</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80">
                       <?php if($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        <?= htmlspecialchars($row['last_name']) ?>, <?= htmlspecialchars($row['first_name']) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="badge-pill badge-existing">Existing</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 font-medium">
                                        <?= htmlspecialchars($row['grade_level']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <?= htmlspecialchars($row['semester']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 font-mono text-xs">
                                        <?= htmlspecialchars($row['school_year']) ?>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-800">
                                        <?= htmlspecialchars($row['strand_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button
                                            class="viewBtn inline-flex items-center gap-1.5 bg-[#0A1931] hover:bg-[#1E4DB7] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition duration-200 cursor-pointer"
                                            data-id="<?= $row['enrollment_id'] ?>">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Review</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400">
                                    <i class="bi bi-check2-circle text-4xl text-emerald-500 block mb-2"></i>
                                    <p class="font-bold text-slate-700">No Pending Re-enrollments</p>
                                    <p class="text-xs text-slate-500 mt-1">All continuing student applications are reviewed.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Hidden Modal for Review -->
        <form id="applicationForm" action="../../controllers/process_reenrollment.php" method="POST">
            <input type="hidden" id="enrollment_id" name="enrollment_id">                           
            <div id="applicationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">

                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 space-y-6">

                    <div class="flex justify-between items-start border-b border-slate-200 pb-4">
                        <div>
                            <span id="modal_status" class="badge-pill badge-pending mb-2">
                                Pending
                            </span>
                            <h2 class="text-2xl font-extrabold text-slate-900 mt-1">
                                Re-Enrollment Application Review
                            </h2>
                            <p class="text-slate-500 text-xs mt-0.5">
                                Review continuing student clearance and documentary submissions.
                            </p>
                        </div>
                        <button
                            type="button"
                            id="closeModal"
                            class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xl font-bold transition">
                            &times;
                        </button>
                    </div>

                    <!-- STUDENT INFORMATION SECTION -->
                    <section class="card-elevated overflow-hidden border border-slate-200">
                        <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-3.5">
                            <h3 class="font-bold text-sm">
                                Student Personal Information
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6 text-xs">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Student Type</label>
                                <input type="text" id="student_type" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Last Name</label>
                                <input type="text" id="last_name" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">First Name</label>
                                <input type="text" id="first_name" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Middle Name</label>
                                <input type="text" id="middle_name" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Suffix</label>
                                <input type="text" id="suffix" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Birth Date</label>
                                <input type="text" id="birth_date" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Gender</label>
                                <input type="text" id="gender" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Complete Address</label>
                                <input type="text" id="address" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Contact Number</label>
                                <input type="text" id="contact_number" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Email Address</label>
                                <input type="email" id="email" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                        </div>
                    </section>

                    <!-- ACADEMIC INFORMATION SECTION -->
                    <section class="card-elevated overflow-hidden border border-slate-200">
                        <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-3.5">
                            <h3 class="font-bold text-sm">
                                Academic Program Information
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6 text-xs">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Track</label>
                                <input type="text" id="modal_track" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Strand</label>
                                <input type="text" id="modal_strand" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Semester</label>
                                <input type="text" id="modal_semester" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Grade Level</label>
                                <input type="text" id="modal_grade_level" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">School Year</label>
                                <input type="text" id="modal_school_year" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>
                        </div>
                    </section>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                        <button
                            type="submit"
                            name="action"
                            value="Reject"
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold cursor-pointer transition shadow-sm flex items-center gap-1.5">
                            <i class="bi bi-x-circle"></i> Reject Application
                        </button>
                        <button
                            type="submit"
                            name="action"
                            value="Approve"
                            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold cursor-pointer transition shadow-sm flex items-center gap-1.5">
                            <i class="bi bi-check-circle"></i> Approve Application
                        </button>
                    </div>
                </div>            
            </div>
        </form>
    </div>
</main>

<script src="../../assets/js/enrollment_application.js"></script>
<script src="/enrollment_system/assets/js/swal.js"></script>

<?php if (isset($_GET['success'])): ?>
<script>
    Swal.fire({
        title: 'Action Completed!',
        text: 'The student re-enrollment has been successfully processed.',
        icon: 'success',
        confirmButtonColor: '#0A1931',
        confirmButtonText: 'Done'
    }).then(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
    })
</script> 
<?php endif; ?>