<?php
$page_title = "Exam Scoring - Masinag SHS";
require_once "../../config/db.php";
require_once '../includes/auth.php';
requireRole('Registrar', 'Super Admin');

include '../includes/header.php';
include '../includes/sidebar.php';

$sql = "
    SELECT
        e.enrollment_id,
        s.first_name,
        s.last_name,
        s.student_type,
        s.exam_number,
        e.grade_level,
        st.strand_name
    FROM enrollments e
    INNER JOIN students s
        ON e.student_id = s.student_id
    INNER JOIN strands st
        ON e.strand_id = st.strand_id
    WHERE e.status = 'Confirmed'
    AND e.stage = 'Exam Scheduled'
    ORDER BY e.enrollment_id DESC
";

$result = $conn->query($sql);
$pending_count = $result ? $result->num_rows : 0;
?>

<main class="page-transition lg:ml-72 pt-20 p-6 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-clipboard2-check-fill"></i> Testing Directorate
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight">
                    Entrance Exam Scoring
                </h1>
                <p class="text-blue-100 text-sm mt-1 max-w-2xl">
                    Encode entrance examination subject scores and evaluate admission qualification rankings.
                </p>
            </div>
            
            <div class="relative z-10 bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 text-center min-w-[140px]">
                <span class="text-3xl font-extrabold text-amber-300"><?= $pending_count ?></span>
                <p class="text-xs text-blue-100 font-medium mt-0.5">Awaiting Scores</p>
            </div>
        </div>

        <!-- Table Container -->
        <div class="card-elevated overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-white flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i class="bi bi-search"></i>
                    </span>
                    <input class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm" placeholder="Search applicant by name..." id="searchInput">
                </div>
                <span class="text-xs font-semibold text-slate-500">
                    Showing <?= $pending_count ?> scheduled examinee(s)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white">
                        <tr>
                            <th class="px-6 py-4">Examinee Name</th>
                            <th class="px-6 py-4 text-center">Exam ID</th>
                            <th class="px-6 py-4 text-center">Type</th>
                            <th class="px-6 py-4 text-center">Grade Level</th>
                            <th class="px-6 py-4 text-center">Strand</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        <?= htmlspecialchars($row['last_name']) ?>, <?= htmlspecialchars($row['first_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-blue-700 text-xs">
                                        <?= htmlspecialchars($row['exam_number'] ?? 'N/A') ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="badge-pill <?= $row['student_type'] === 'New' ? 'badge-new' : 'badge-transferee' ?>">
                                            <?= htmlspecialchars($row['student_type']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-slate-700 font-medium"><?= htmlspecialchars($row['grade_level']) ?></td>
                                    <td class="px-6 py-4 text-center font-semibold text-slate-800"><?= htmlspecialchars($row['strand_name']) ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="badge-pill badge-scheduled">Scheduled</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="admin-exam-scoring-v2" method="POST" target="_blank" class="inline">
                                            <input
                                                type="hidden"
                                                name="enrollment_id"
                                                value="<?= htmlspecialchars($row['enrollment_id']) ?>">

                                            <button
                                                type="submit"
                                                class="inline-flex items-center gap-1.5 bg-[#0A1931] hover:bg-[#1E4DB7] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition duration-200 cursor-pointer">
                                                <i class="bi bi-pencil-square"></i>
                                                <span>Encode Scores</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400">
                                    <i class="bi bi-check2-circle text-4xl text-emerald-500 block mb-2"></i>
                                    <p class="font-bold text-slate-700">No Pending Exam Scores</p>
                                    <p class="text-xs text-slate-500 mt-1">No applicants currently awaiting entrance exam scoring.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Hidden Modal Preserved for JS validation & modal handling -->
<form id="scoringForm" action="../../controllers/process_exam_scoring.php" method="POST">
    <!-- <input type="hidden" id="enrollment_id" name="enrollment_id"> -->

    <div id="modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-3xl w-full max-w-5xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-200 pb-4">
                <h2 class="text-2xl font-extrabold text-slate-900">Applicant Review & Exam Scoring</h2>
                <button type="button" onclick="closeModal()" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xl font-bold transition">&times;</button>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="card-elevated border border-slate-200 p-6 space-y-4">
                        <h3 class="font-bold text-base text-slate-900">Applicant Information</h3>

                        <div class="grid md:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Name</label>
                                <input type="text" id="ex_name" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Email</label>
                                <input type="text" id="ex_email" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Type</label>
                                <input type="text" id="ex_type" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Exam No.</label>
                                <input type="text" id="ex_examNo" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-mono" readonly>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Year Level</label>
                                <input type="text" id="ex_yrLvl" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-medium" readonly>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Strand</label>
                                <input type="text" id="ex_strand" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-semibold" readonly>
                            </div>
                        </div>
                    </div>

                    <section class="card-elevated border border-slate-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-3.5">
                            <h3 class="font-bold text-sm">Admission Requirements</h3>
                        </div>
                        <div id="documents" class="p-6 space-y-4">
                            <p class="text-slate-400 text-xs">Loading documents...</p>
                        </div>
                    </section>
                </div>

                <div class="space-y-6">
                    <div class="card-elevated border border-slate-200 p-6 space-y-3 text-xs">
                        <h3 class="font-bold text-base text-slate-900 mb-2">Subject Scores</h3>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Mathematics</label>
                            <input type="number" min="0" max="100" placeholder="0 - 100" class="w-full rounded-xl border border-slate-300 px-4 py-2.5" name="math" id="math">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">English</label>
                            <input type="number" min="0" max="100" placeholder="0 - 100" class="w-full rounded-xl border border-slate-300 px-4 py-2.5" name="english" id="english">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Filipino</label>
                            <input type="number" min="0" max="100" placeholder="0 - 100" class="w-full rounded-xl border border-slate-300 px-4 py-2.5" name="filipino" id="filipino">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">General Science</label>
                            <input type="number" min="0" max="100" placeholder="0 - 100" class="w-full rounded-xl border border-slate-300 px-4 py-2.5" name="science" id="science">
                        </div>
                    </div>

                    <div class="card-elevated border border-slate-200 p-6 space-y-4">
                        <div id="passFailBadge" class="bg-slate-100 text-slate-500 rounded-xl p-3 text-center text-xs font-bold">
                            Enter all four scores
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="submit" id="Reject" name="action" value="Reject" class="bg-red-600 hover:bg-red-700 text-white rounded-xl cursor-pointer py-3 text-xs font-bold transition shadow-sm">Reject</button>
                            <button type="submit" name="action" value="Approve" class="btn-primary rounded-xl cursor-pointer py-3 text-xs font-bold shadow-sm">Approve</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="/enrollment_system/assets/js/cs-validation/admin-exam-scoring-validation.js"></script>
<script src="/enrollment_system/assets/js/swal.js"></script>

<?php if (isset($_GET['success'])): ?>
    <script>
        Swal.fire({
            title: 'Approved!',
            text: 'The exam scores have been saved successfully.',
            icon: 'success',
            confirmButtonColor: '#0A1931',
            confirmButtonText: 'Done'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        })
    </script> 
<?php endif; ?>