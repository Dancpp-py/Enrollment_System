<?php 
require_once '../../config/db.php';
require_once '../includes/auth.php';
requireRole('Registrar', 'Super Admin');
include '../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['review_enrollment_id'] = $_POST['enrollment_id'] ?? null;

    header("Location: admin-enrollment-review-v2");
    exit();
}

$enrollment_id = $_SESSION['review_enrollment_id'] ?? null;

// if (empty($enrollment_id) || !ctype_digit((string)$enrollment_id)) {
//     showError("Error!", "Invalid enrollment ID.");
// }

$page_title = "Official Enrollment Decision - Masinag SHS";
include '../includes/header.php';

$stmt = $conn->prepare("
    SELECT
        e.enrollment_id,
        e.status,
        e.stage,
        e.grade_level,
        s.first_name,
        s.last_name,
        s.email,
        s.student_type,
        s.exam_number,
        st.strand_name
    FROM enrollments e
    INNER JOIN students s ON e.student_id = s.student_id
    INNER JOIN strands st ON e.strand_id = st.strand_id
    WHERE e.enrollment_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $enrollment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    showError("Error!", "Enrollment not found.");
}

$applicant = $result->fetch_assoc();
$stmt->close();

if ($applicant['status'] !== 'Confirmed' || $applicant['stage'] !== 'Enrollment Review') {
    showError("Error!", "This application is not currently awaiting enrollment review.");
}

$stmt = $conn->prepare("
    SELECT math_score, english_score, filipino_score, science_score, average_score, exam_status
    FROM entrance_exam_results
    WHERE enrollment_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $enrollment_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("
    SELECT document_type, file_path
    FROM enrollment_documents
    WHERE enrollment_id = ?
");

$stmt->bind_param("i", $enrollment_id);
$stmt->execute();
$docsResult = $stmt->get_result();

$documents = [];
while ($doc = $docsResult->fetch_assoc()) {
    $documents[] = [
        'type' => $doc['document_type'],
        'url'  => '/enrollment_system/' . $doc['file_path'],
    ];
}
$stmt->close();

$studentName = $applicant['last_name'] . ', ' . $applicant['first_name'];
$examStatus = $exam['exam_status'] ?? 'Pending';
$badgeClass = match ($examStatus) {
    'Passed' => 'badge-passed',
    'Failed' => 'badge-failed',
    default => 'badge-pending'
};
$average = $exam['average_score'] !== null ? number_format($exam['average_score'], 2) : '—';
?>

<main class="page-transition p-6 sm:p-8 min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto space-y-8">
 
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-patch-check-fill"></i> Official Enrollment Review
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight"><?= htmlspecialchars($studentName) ?></h1>
                <p class="text-blue-100 text-sm mt-1">
                    Application ID: <span class="font-mono font-bold text-amber-300">#<?= htmlspecialchars($enrollment_id) ?></span> • Exam ID: <span class="font-mono font-bold"><?= htmlspecialchars($applicant['exam_number'] ?? 'N/A') ?></span>
                </p>
            </div>

            <div class="relative z-10 flex items-center gap-3">
                <span class="badge-pill <?= $badgeClass ?> px-4 py-1.5 text-xs font-bold shadow-sm">
                    Exam Result: <?= htmlspecialchars(strtoupper($examStatus)) ?>
                </span>
            </div>
        </div>
 
        <div class="grid lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 space-y-8">
 
                <!-- APPLICANT INFORMATION -->
                <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
                    <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2.5">
                        <i class="bi bi-person-fill text-amber-300"></i>
                        <h2 class="font-bold text-base tracking-tight">Applicant Details</h2>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 p-6 text-sm">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Student Name</label>
                            <input type="text" value="<?= htmlspecialchars($studentName) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Student Type</label>
                            <input type="text" value="<?= htmlspecialchars($applicant['student_type']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Grade Level</label>
                            <input type="text" value="<?= htmlspecialchars($applicant['grade_level']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-slate-800" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Strand</label>
                            <input type="text" value="<?= htmlspecialchars($applicant['strand_name']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Email</label>
                            <input type="text" value="<?= htmlspecialchars($applicant['email']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-slate-800" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Temporary Exam ID</label>
                            <input type="text" value="<?= htmlspecialchars($applicant['exam_number'] ?? 'N/A') ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-mono text-slate-800" readonly>
                        </div>
                    </div>
                </div>
 
                <div class="grid md:grid-cols-2 gap-8">
 
                    <!-- ENTRANCE EXAMINATION SCORES -->
                    <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
                        <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-3.5 flex items-center gap-2">
                            <i class="bi bi-clipboard2-data-fill text-amber-300"></i>
                            <h3 class="font-bold text-sm tracking-tight">Examination Scores</h3>
                        </div>

                        <div class="p-6">
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-slate-100">
                                    <tr><td class="py-2.5 text-slate-600 font-medium">Mathematics</td><td class="text-right font-bold text-slate-800 font-mono"><?= htmlspecialchars($exam['math_score'] ?? '—') ?></td></tr>
                                    <tr><td class="py-2.5 text-slate-600 font-medium">English Language</td><td class="text-right font-bold text-slate-800 font-mono"><?= htmlspecialchars($exam['english_score'] ?? '—') ?></td></tr>
                                    <tr><td class="py-2.5 text-slate-600 font-medium">Filipino</td><td class="text-right font-bold text-slate-800 font-mono"><?= htmlspecialchars($exam['filipino_score'] ?? '—') ?></td></tr>
                                    <tr><td class="py-2.5 text-slate-600 font-medium">General Science</td><td class="text-right font-bold text-slate-800 font-mono"><?= htmlspecialchars($exam['science_score'] ?? '—') ?></td></tr>
                                    <tr class="font-bold border-t-2 border-slate-200 text-base">
                                        <td class="pt-3 text-slate-900">Computed Average</td>
                                        <td class="text-right pt-3 font-mono text-blue-700"><?= $average ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
 
                    <!-- REQUIREMENTS -->
                    <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
                        <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-3.5 flex items-center gap-2">
                            <i class="bi bi-folder-check text-amber-300"></i>
                            <h3 class="font-bold text-sm tracking-tight">Document Verification</h3>
                        </div>

                        <div class="p-6 space-y-3">
                            <?php if (empty($documents)): ?>
                                <p class="text-slate-400 text-xs text-center py-4">No uploaded documents.</p>
                            <?php else: ?>
                                <?php foreach ($documents as $doc): ?>
                                    <div class="flex justify-between items-center bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs">
                                        <span class="font-semibold text-slate-800"><?= htmlspecialchars($doc['type']) ?></span>
                                        <button type="button"
                                            onclick="openDocumentModal('<?= htmlspecialchars($doc['url'], ENT_QUOTES) ?>', '<?= htmlspecialchars($doc['type'], ENT_QUOTES) ?>')"
                                            class="btn-primary px-3 py-1 rounded-lg text-xs font-semibold cursor-pointer shadow-sm">
                                            View
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
 
            <!-- ENROLLMENT REVIEW ACTION PANEL -->
            <div class="lg:col-span-4 space-y-6">
                <div class="card-elevated p-6 border border-slate-200/80 bg-white space-y-4">
                    <div class="p-4 rounded-2xl text-center <?= $examStatus === 'Passed' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' ?>">
                        <span class="text-xs font-bold uppercase tracking-wider block">Entrance Exam Rating</span>
                        <span class="text-2xl font-black mt-1 block font-mono"><?= $average ?>%</span>
                        <span class="text-xs font-bold uppercase mt-1 inline-block <?= $examStatus === 'Passed' ? 'text-emerald-700' : 'text-rose-700' ?>"><?= htmlspecialchars($examStatus) ?></span>
                    </div>

                    <div class="border-t border-slate-100 pt-3 text-xs text-slate-500 space-y-1.5">
                        <div class="flex justify-between">
                            <span>Workflow Stage:</span>
                            <strong class="text-slate-800">Enrollment Review</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Target Strand:</span>
                            <strong class="text-slate-800"><?= htmlspecialchars($applicant['strand_name']) ?></strong>
                        </div>
                    </div>
                </div>
 
                <form action="../../controllers/process_enrollment_review.php" method="POST" class="card-elevated p-6 border border-slate-200/80 bg-white space-y-4">
                    <input type="hidden" name="enrollment_id" value="<?= $enrollment_id ?>">
                    <h4 class="font-bold text-slate-900 text-sm">Official Decision</h4>
                    <p class="text-xs text-slate-500">Confirming this application will transition the applicant to officially Enrolled status.</p>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <button type="submit" name="action" value="Reject" class="bg-red-600 hover:bg-red-700 text-white rounded-xl py-3 font-bold text-xs cursor-pointer transition shadow-sm flex items-center justify-center gap-1.5">
                            <i class="bi bi-x-circle"></i> Reject
                        </button>
                        <button type="submit" name="action" value="Approve" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl py-3 font-bold text-xs cursor-pointer transition shadow-sm flex items-center justify-center gap-1.5">
                            <i class="bi bi-check-circle"></i> Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
 
<!-- DOCUMENT PREVIEW MODAL -->
<div
    id="documentModal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4 sm:p-6 transition-all duration-300">

    <div
        class="absolute inset-0"
        onclick="closeDocumentModal()">
    </div>

    <div
        id="documentModalContainer"
        class="relative w-full max-w-5xl h-[88vh] bg-white rounded-3xl shadow-2xl overflow-hidden scale-95 opacity-0 transition-all duration-300 flex flex-col z-10">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-white">
            <div>
                <h3 id="documentModalTitle" class="text-base font-bold text-slate-900">Requirement</h3>
                <p class="text-xs text-slate-500">Uploaded document preview</p>
            </div>

            <button
                type="button"
                onclick="closeDocumentModal()"
                class="w-9 h-9 rounded-full bg-slate-100 hover:bg-red-100 hover:text-red-600 transition text-xl font-bold flex items-center justify-center cursor-pointer">
                &times;
            </button>
        </div>

        <div
            id="documentModalBody"
            class="flex-1 bg-slate-100 flex items-center justify-center overflow-auto p-4 sm:p-6">
        </div>
    </div>
</div>
 
<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/admin-enrollment-review-v2.js"></script>