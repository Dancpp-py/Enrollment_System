<?php 
session_start();

include '../../config/db.php';  
require_once '../includes/auth.php';
requireRole('Admissions', 'Super Admin');
$page_title = "Application Review Detail - Masinag SHS";

include '../includes/helpers.php';

/* ===========================================
   GET ENROLLMENT ID
=========================================== */


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $posted_id = $_POST['enrollment_id'] ?? null;

    if (empty($posted_id) || !ctype_digit((string)$posted_id)) {
        showError("Error!", "Invalid enrollment ID.");
    }

    $_SESSION['application_review_enrollment_id'] = (int)$posted_id;

    header("Location: admin-dashboard-v2");
    exit;
}

$enrollment_id = $_SESSION['application_review_enrollment_id'] ?? null;

if (empty($enrollment_id) || !is_int($enrollment_id)) {
    showError("Error!", "No application selected for review.");
}

include '../includes/header.php';  

/* ===========================================
   GET APPLICATION DETAILS
=========================================== */
$stmt = $conn->prepare("
    SELECT
        e.enrollment_id,
        e.status,
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

        sy.school_year,
        st.strand_name

    FROM enrollments e
    INNER JOIN students s ON e.student_id = s.student_id
    INNER JOIN school_years sy ON e.school_year_id = sy.school_year_id
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

/* ===========================================
   CHECK APPLICATION STATUS
=========================================== */
if ($student['status'] !== 'Pending') {
    showError("Error!", "This application is no longer available for review.");
}

/* ===========================================
   GET UPLOADED REQUIREMENTS
=========================================== */
$stmt = $conn->prepare("
    SELECT
        document_type,
        file_path
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
        "url"  => "/enrollment_system/" . $doc["file_path"]
    ];
}
$stmt->close();

$studentName = $student['last_name'] . ', ' . $student['first_name'];
?>

<main class="page-transition p-6 sm:p-8 min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-file-earmark-person"></i> Applicant Profile
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                    <?= htmlspecialchars($studentName) ?>
                </h1>
                <p class="text-blue-100 text-sm mt-1">
                    Application ID: <span class="font-mono font-bold text-amber-300">#<?= htmlspecialchars($enrollment_id) ?></span> • Type: <span class="font-semibold"><?= htmlspecialchars($student['student_type']) ?></span>
                </p>
            </div>

            <div class="relative z-10 flex items-center gap-3">
                <span id="modal_status" class="badge-pill badge-pending px-4 py-1.5 text-xs font-bold shadow-sm">
                    Status: <?= htmlspecialchars($student['status']) ?>
                </span>
            </div>
        </div>

        <form id="applicationForm" action="../../controllers/process_enrollment.php" method="POST" class="space-y-8">
            <input type="hidden" id="enrollment_id" name="enrollment_id" value="<?= htmlspecialchars($enrollment_id) ?>"> 

            <!-- STUDENT INFORMATION -->
            <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
                <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2.5">
                    <i class="bi bi-person-fill text-amber-300"></i>
                    <h2 class="font-bold text-base tracking-tight">
                        Personal Information
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-6 sm:p-8 text-sm">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Student Type</label>
                        <input type="text" value="<?= htmlspecialchars($student['student_type']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold text-slate-800" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Full Name</label>
                        <input type="text" value="<?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['suffix']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold text-slate-800" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Birth Date</label>
                        <input type="text" value="<?= htmlspecialchars($student['birth_date']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-800" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Gender</label>
                        <input type="text" value="<?= htmlspecialchars($student['gender']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-800" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Contact Number</label>
                        <input type="text" value="<?= htmlspecialchars($student['contact_number']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-800" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email Address</label>
                        <input type="text" value="<?= htmlspecialchars($student['email']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-800" readonly>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Complete Address</label>
                        <input type="text" value="<?= htmlspecialchars($student['address']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-800" readonly>
                    </div>
                </div>
            </section>

            <!-- ACADEMIC INFORMATION -->
            <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
                <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2.5">
                    <i class="bi bi-mortarboard-fill text-amber-300"></i>
                    <h2 class="font-bold text-base tracking-tight">
                        Academic Program
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-6 sm:p-8 text-sm">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Selected Strand</label>
                        <input type="text" value="<?= htmlspecialchars($student['strand_name']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold text-slate-800" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Grade Level & Semester</label>
                        <input type="text" value="<?= htmlspecialchars($student['grade_level'] . ' • ' . $student['semester']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 font-semibold text-slate-800" readonly>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">School Year</label>
                        <input type="text" value="<?= htmlspecialchars($student['school_year']) ?>" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 font-mono text-slate-800" readonly>
                    </div>
                </div>
            </section>

            <!-- REQUIREMENTS -->
            <section class="card-elevated overflow-hidden border border-slate-200/80 bg-white">
                <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-folder-check text-amber-300"></i>
                        <h2 class="font-bold text-base tracking-tight">
                            Submitted Documents & Requirements
                        </h2>
                    </div>
                    <span class="text-xs bg-white/10 px-3 py-1 rounded-full text-blue-100 font-medium">
                        <?= count($documents) ?> File(s)
                    </span>
                </div>

                <div class="p-6 sm:p-8">
                    <?php if (empty($documents)): ?>
                        <div class="text-center py-6 text-slate-400">
                            <i class="bi bi-file-earmark-x text-3xl block mb-1"></i>
                            <p class="text-sm">No uploaded documents found for this application.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <?php foreach ($documents as $doc): ?>
                                <div class="flex justify-between items-center bg-slate-50 border border-slate-200 rounded-2xl p-4 hover:border-blue-300 transition duration-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg shrink-0">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </div>
                                        <span class="font-semibold text-slate-800 text-sm">
                                            <?= htmlspecialchars($doc['type']) ?>
                                        </span>
                                    </div>

                                    <button
                                        type="button"
                                        onclick="openDocumentModal('<?= htmlspecialchars($doc['url'], ENT_QUOTES) ?>','<?= htmlspecialchars($doc['type'], ENT_QUOTES) ?>')"
                                        class="btn-primary px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer shadow-sm">
                                        <i class="bi bi-eye mr-1"></i> View
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- ACTION BUTTONS -->
            <div class="flex flex-wrap justify-end gap-4 pt-4">
                <button
                    id="Reject"
                    type="submit"
                    name="action"
                    value="Reject"
                    class="px-8 py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition duration-200 cursor-pointer shadow-md flex items-center gap-2 text-sm">
                    <i class="bi bi-x-circle-fill"></i>
                    <span>Reject Application</span>
                </button>

                <button
                    id="Approve"
                    type="submit"
                    name="action"
                    value="Approve"
                    class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition duration-200 cursor-pointer shadow-md flex items-center gap-2 text-sm">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Approve Application</span>
                </button>
            </div>
        </form>
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
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div>
                    <h3 id="documentModalTitle" class="text-base font-bold text-slate-900">
                        Requirement Preview
                    </h3>
                    <p class="text-xs text-slate-500">Official document preview</p>
                </div>
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
            <!-- JS injects preview here -->
        </div>
    </div>
</div>

<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/admin-enrollment-review-v2.js"></script>
