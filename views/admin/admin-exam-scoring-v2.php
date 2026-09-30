<?php
session_start();

$page_title = "Exam Scoring Form - Masinag SHS";

require_once "../../config/db.php";
require_once "../includes/auth.php";
requireRole('Registrar', 'Super Admin');

/* =====================================================
   GET ENROLLMENT ID
===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $posted_id = $_POST['enrollment_id'] ?? null;

    if (empty($posted_id) || !ctype_digit((string)$posted_id)) {
        showError("Error!", "Invalid enrollment ID.");
    }

    $_SESSION['exam_scoring_enrollment_id'] = (int)$posted_id;

    header("Location: admin-exam-scoring-v2.php");
    exit;
}

$enrollment_id = $_SESSION['exam_scoring_enrollment_id'] ?? null;

if (empty($enrollment_id) || !ctype_digit((string)$enrollment_id)) {
    showError("Error!", "Invalid enrollment ID.");
}

$enrollment_id = (int)$enrollment_id;

include "../includes/header.php";

/* =====================================================
   LOAD APPLICANT
===================================================== */
$stmt = $conn->prepare("
    SELECT
        e.enrollment_id,
        e.status,
        e.stage,
        e.grade_level,
        e.semester,

        s.student_id,
        s.student_type,
        s.first_name,
        s.middle_name,
        s.last_name,
        s.suffix,
        s.birth_date,
        s.gender,
        s.address,
        s.contact_number,
        s.email,
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
    showError("Error!", "Student application not found.");
}

$student = $result->fetch_assoc();
$stmt->close();

/* =====================================================
   VALIDATE STAGE
===================================================== */
if ($student['status'] !== 'Confirmed' || $student['stage'] !== 'Exam Scheduled') {
    showError("Error!", "This applicant is not waiting for exam scoring.");
}

/* =====================================================
   LOAD REQUIREMENTS
===================================================== */
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
        "type" => $doc["document_type"],
        "url" => "/enrollment_system/" . $doc["file_path"]
    ];
}
$stmt->close();

$studentName = $student['last_name'] . ", " . $student['first_name'];
?>

<main class="page-transition p-6 sm:p-8 min-h-screen bg-slate-50">
    <form id="scoringForm" action="../../controllers/process_exam_scoring.php" method="POST" class="max-w-6xl mx-auto space-y-8">
        <input type="hidden" id="enrollment_id" name="enrollment_id" value="<?= htmlspecialchars($enrollment_id) ?>">

        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-pencil-square"></i> Score Entry Form
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight"><?= htmlspecialchars($studentName) ?></h1>
                <p class="text-blue-100 text-sm mt-1">
                    Exam ID: <span class="font-mono font-bold text-amber-300"><?= htmlspecialchars($student['exam_number']) ?></span> • Strand: <span class="font-semibold"><?= htmlspecialchars($student['strand_name']) ?></span>
                </p>
            </div>

            <div class="relative z-10">
                <span class="badge-pill badge-scheduled px-4 py-1.5 text-xs font-bold shadow-sm">
                    Stage: Exam Scheduled
                </span>
            </div>
        </div>

        <div class="grid lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 space-y-8">
                
                <!-- APPLICANT INFORMATION -->
                <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
                    <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2.5">
                        <i class="bi bi-person-fill text-amber-300"></i>
                        <h2 class="font-bold text-base tracking-tight">Applicant Profile</h2>
                    </div>
                        
                    <div class="grid sm:grid-cols-2 gap-4 p-6 text-sm">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Full Name</label>
                            <input type="text" id="ex_name" readonly value="<?= htmlspecialchars($studentName) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
                            <input type="text" id="ex_email" readonly value="<?= htmlspecialchars($student['email']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-slate-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Student Category</label>
                            <input type="text" id="ex_type" readonly value="<?= htmlspecialchars($student['student_type']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Assigned Exam ID</label>
                            <input type="text" id="ex_examNo" readonly value="<?= htmlspecialchars($student['exam_number']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-mono text-slate-800 font-bold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Grade Level</label>
                            <input type="text" id="ex_yrLvl" readonly value="<?= htmlspecialchars($student['grade_level']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-slate-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Target Strand</label>
                            <input type="text" id="ex_strand" readonly value="<?= htmlspecialchars($student['strand_name']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800">
                        </div>
                    </div>
                </div>

                <!-- DOCUMENTS SECTION -->
                <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
                    <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i class="bi bi-folder-check text-amber-300"></i>
                            <h3 class="font-bold text-base tracking-tight">Admission Requirements</h3>
                        </div>
                        <span class="text-xs bg-white/10 px-3 py-1 rounded-full text-blue-100 font-medium">
                            <?= count($documents) ?> File(s)
                        </span>
                    </div>

                    <div class="p-6">
                        <?php if(empty($documents)): ?>
                            <p class="text-slate-400 text-xs text-center py-4">No uploaded requirements.</p>
                        <?php else: ?>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <?php foreach($documents as $doc): ?>
                                    <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-2xl p-4">
                                        <span class="font-semibold text-xs text-slate-800">
                                            <?= htmlspecialchars($doc['type']) ?>
                                        </span>
                                        <button
                                            type="button"
                                            onclick="openDocumentModal('<?= htmlspecialchars($doc['url'],ENT_QUOTES) ?>','<?= htmlspecialchars($doc['type'],ENT_QUOTES) ?>')"
                                            class="btn-primary px-3 py-1.5 rounded-xl text-xs font-semibold shadow-sm cursor-pointer">
                                            View
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- SCORING INPUT CARD -->
            <div class="lg:col-span-4 space-y-6">
                <div class="card-elevated border border-slate-200/80 bg-white p-6 space-y-5">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="font-bold text-base text-slate-900">Score Input Matrix</h3>
                        <p class="text-xs text-slate-500">Encode raw scores (0 to 100)</p>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <label for="math" class="block font-semibold text-slate-600 mb-1">Mathematics Score</label>
                            <input type="number" min="0" max="100" placeholder="0 - 100" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 font-bold font-mono text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none" name="math" id="math" required>
                        </div>
                        <div>
                            <label for="english" class="block font-semibold text-slate-600 mb-1">English Score</label>
                            <input type="number" min="0" max="100" placeholder="0 - 100" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 font-bold font-mono text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none" name="english" id="english" required>
                        </div>
                        <div>
                            <label for="filipino" class="block font-semibold text-slate-600 mb-1">Filipino Score</label>
                            <input type="number" min="0" max="100" placeholder="0 - 100" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 font-bold font-mono text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none" name="filipino" id="filipino" required>
                        </div>
                        <div>
                            <label for="science" class="block font-semibold text-slate-600 mb-1">General Science Score</label>
                            <input type="number" min="0" max="100" placeholder="0 - 100" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 font-bold font-mono text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none" name="science" id="science" required>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 space-y-4">
                        <div id="passFailBadge" class="bg-slate-100 text-slate-500 rounded-xl p-3 text-center text-xs font-bold">
                            Enter all four scores to evaluate
                        </div>

                        <div class="grid grid-cols-2 gap-3">

                            <button
                                type="submit"
                                id="rejectBtn"
                                name="action"
                                value="Reject"
                                class="bg-red-600 hover:bg-red-700 text-white rounded-xl cursor-pointer py-3 text-xs font-bold transition shadow-sm flex items-center justify-center gap-1">
                                <i class="bi bi-x-circle"></i>
                                Reject
                            </button>
                            <button
                                type="submit"
                                id="approveBtn"
                                name="action"
                                value="Approve"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl cursor-pointer py-3 text-xs font-bold transition shadow-sm flex items-center justify-center gap-1">
                                <i class="bi bi-check-circle"></i>
                                Submit Score
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<!-- DOCUMENT PREVIEW MODAL -->
<div
    id="documentModal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4 sm:p-6 transition-all duration-300">

    <div class="absolute inset-0" onclick="closeDocumentModal()"></div>

    <div
        id="documentModalContainer"
        class="relative w-full max-w-5xl h-[88vh] bg-white rounded-3xl shadow-2xl overflow-hidden scale-95 opacity-0 transition-all duration-300 flex flex-col z-10">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-white">
            <h3 id="documentModalTitle" class="text-base font-bold text-slate-900">Requirement Preview</h3>
            <button
                type="button"
                onclick="closeDocumentModal()"
                class="w-9 h-9 rounded-full bg-slate-100 hover:bg-red-100 hover:text-red-600 transition text-xl font-bold flex items-center justify-center cursor-pointer">
                &times;
            </button>
        </div>

        <div id="documentModalBody" class="flex-1 bg-slate-100 flex items-center justify-center overflow-auto p-4 sm:p-6"></div>
    </div>
</div>

<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/admin-exam-scoring.js"></script>
<script src="/enrollment_system/assets/js/cs-validation/admin-exam-scoring-validation.js"></script>
<script src="/enrollment_system/assets/js/admin-enrollment-review-v2.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const approveBtn = document.getElementById("approveBtn");
    const rejectBtn = document.getElementById("rejectBtn");

    let confirmed = false;

    // APPROVE / SUBMIT SCORE
    if (approveBtn) {

        approveBtn.addEventListener("click", function (e) {

            if (confirmed) return;

            e.preventDefault();

            Swal.fire({
                icon: "question",
                title: "Submit Exam Score?",
                text: "The applicant will be marked as passed and notified via email.",
                showCancelButton: true,
                confirmButtonColor: "#059669",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Yes, Submit",
                cancelButtonText: "Cancel"
            }).then((result) => {

                if (result.isConfirmed) {

                    confirmed = true;
                    approveBtn.click();

                }

            });

        });

    }


    // REJECT
    if (rejectBtn) {

        rejectBtn.addEventListener("click", function (e) {

            if (confirmed) return;

            e.preventDefault();

            Swal.fire({
                icon: "warning",
                title: "Reject Applicant?",
                text: "The applicant will be marked as failed and notified via email.",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Yes, Reject",
                cancelButtonText: "Cancel"
            }).then((result) => {

                if (result.isConfirmed) {

                    confirmed = true;
                    rejectBtn.click();

                }

            });

        });

    }

});
</script>