<?php 
$page_title = "Official Enrollment Review - Masinag SHS";
require_once '../../config/db.php';
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
    s.email,
    s.exam_number,
    e.grade_level,
    st.strand_name,
    eer.average_score,
    eer.exam_status
FROM enrollments e
INNER JOIN students s
    ON e.student_id = s.student_id
INNER JOIN strands st
    ON e.strand_id = st.strand_id
INNER JOIN entrance_exam_results eer
    ON eer.enrollment_id = e.enrollment_id
WHERE e.status = 'Confirmed'
  AND e.stage = 'Enrollment Review'
ORDER BY e.enrollment_id DESC
";

$result = $conn->query($sql);
$record_count = $result ? $result->num_rows : 0;
?>

<main class="page-transition lg:ml-72 pt-20 p-6 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-patch-check-fill"></i> Registrar Review
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight">
                    Official Enrollment Review
                </h1>
                <p class="text-blue-100 text-sm mt-1 max-w-2xl">
                    Review post-examination results and make final official enrollment decisions.
                </p>
            </div>
            
            <div class="relative z-10 bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 text-center min-w-[140px]">
                <span class="text-3xl font-extrabold text-amber-300"><?= $record_count ?></span>
                <p class="text-xs text-blue-100 font-medium mt-0.5">Awaiting Decision</p>
            </div>
        </div>
            
        <!-- TABLE CONTAINER -->
        <div class="card-elevated overflow-hidden">
            
            <!-- Controls Bar -->
            <div class="p-5 border-b border-slate-200 bg-white flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i class="bi bi-search"></i>
                    </span>
                    <input id="searchInput" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm" placeholder="Search applicant by name or email...">
                </div>

                <div class="flex gap-2 w-full md:w-auto overflow-x-auto">
                    <button data-filter="All" class="px-4 py-2 rounded-xl bg-[#0A1931] text-white text-xs font-bold cursor-pointer transition shadow-sm">All (<?= $record_count ?>)</button>
                    <button data-filter="Passed" class="px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold cursor-pointer transition">Passed</button>
                    <button data-filter="Failed" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold cursor-pointer transition">Failed</button>
                </div>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white">
                        <tr>
                            <th class="px-6 py-4">Applicant</th>
                            <th class="px-6 py-4 text-center">Type</th>
                            <th class="px-6 py-4 text-center">Strand</th>
                            <th class="px-6 py-4 text-center">Average Score</th>
                            <th class="px-6 py-4 text-center">Exam Result</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                    $examStatus = $row['exam_status'] ?? 'Pending';
                                    $badgeClass = match ($examStatus) {
                                        'Passed' => 'badge-passed',
                                        'Failed' => 'badge-failed',
                                        default => 'badge-pending'
                                    };
                                    $average = $row['average_score'] !== null ? number_format($row['average_score'], 2) : '—';
                                ?>
                                <tr class="hover:bg-slate-50/80 transition" data-status="<?= htmlspecialchars($examStatus) ?>">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900"><?= htmlspecialchars($row['last_name']) ?>, <?= htmlspecialchars($row['first_name']) ?></div>
                                        <div class="text-xs text-slate-500"><?= htmlspecialchars($row['email']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="badge-pill <?= $row['student_type'] === 'New' ? 'badge-new' : 'badge-transferee' ?>">
                                            <?= htmlspecialchars($row['student_type']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-slate-800"><?= htmlspecialchars($row['strand_name']) ?></td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-blue-700 text-sm"><?= $average ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="badge-pill <?= $badgeClass ?>"><?= strtoupper($examStatus) ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                       <form action="admin-enrollment-review-v2" method="POST" target="_blank" class="inline">
                                            <input
                                                type="hidden"
                                                name="enrollment_id"
                                                value="<?= htmlspecialchars($row['enrollment_id']) ?>">

                                            <button
                                                type="submit"
                                                class="inline-flex items-center gap-1.5 bg-[#0A1931] hover:bg-[#1E4DB7] text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition duration-200 cursor-pointer">
                                                <i class="bi bi-pencil-square"></i>
                                                <span>Review</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-400">
                                    <i class="bi bi-check2-circle text-4xl text-emerald-500 block mb-2"></i>
                                    <p class="font-bold text-slate-700">No Applications Awaiting Review</p>
                                    <p class="text-xs text-slate-500 mt-1">All entrance examinees have been reviewed.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="/enrollment_system/assets/js/admin-enrollment-review.js"></script>
<script src="/enrollment_system/assets/js/swal.js"></script>

<?php if (isset($_GET['success'])): ?>
    <script>
        Swal.fire({
            title: 'Submitted!',
            text: 'The application has been successfully processed.',
            icon: 'success',
            confirmButtonColor: '#0A1931',
            confirmButtonText: 'Done'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        })
    </script>
<?php endif; ?>