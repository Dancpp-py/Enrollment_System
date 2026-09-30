<?php

require_once '../../config/db.php';
require_once '../includes/auth.php';

// session_start();

requireRole('Registrar', 'Super Admin');

$page_title = "Student Record Profile - Masinag SHS";


include '../includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ---------------------------------------------------------------
// SELECT STUDENT RECORD
// ---------------------------------------------------------------

if (isset($_POST['enrollment_id'])) {
    $posted_enrollment_id = (int) $_POST['enrollment_id'];

    if ($posted_enrollment_id <= 0) {
        showError("Invalid Request!", "No enrollment was specified.");
        exit;
    }

    // Only allow records that are currently part of the enrolled
    // and confirmed student directory.
    $stmt = $conn->prepare("
        SELECT enrollment_id
        FROM enrollments
        WHERE enrollment_id = ?
          AND stage = 'Enrolled'
          AND status = 'Confirmed'
        LIMIT 1
    ");

    $stmt->bind_param("i", $posted_enrollment_id);
    $stmt->execute();

    $validEnrollment = $stmt->get_result()->num_rows > 0;

    $stmt->close();

    if (!$validEnrollment) {
        showError(
            "Not Found!",
            "This student record could not be found."
        );
        exit;
    }

    $_SESSION['student_record_enrollment_id'] = $posted_enrollment_id;

    header("Location: admin-summary-v2");
    exit;
}

// Get the selected enrollment from the session.
$enrollment_id = (int) (
    $_SESSION['student_record_enrollment_id'] ?? 0
);

if ($enrollment_id <= 0) {
    showError("Invalid Request!", "No enrollment was specified.");
    exit;
}

// ---------------------------------------------------------------
// GET STUDENT RECORD
// ---------------------------------------------------------------

$stmt = $conn->prepare("
    SELECT
        e.enrollment_id,
        e.status,
        e.stage,
        e.grade_level,
        s.student_id,
        s.student_number,
        s.student_type,
        s.first_name,
        s.last_name,
        s.middle_name,
        s.email,
        st.strand_name
    FROM enrollments e
    INNER JOIN students s
        ON e.student_id = s.student_id
    INNER JOIN strands st
        ON e.strand_id = st.strand_id
    WHERE e.enrollment_id = ?
      AND e.stage = 'Enrolled'
      AND e.status = 'Confirmed'
    LIMIT 1
");

$stmt->bind_param("i", $enrollment_id);
$stmt->execute();

$row = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$row) {
    showError(
        "Not Found!",
        "This enrollment record could not be found."
    );
    exit;
}

$studentName = trim(
    $row['last_name'] . ', ' .
    $row['first_name'] . ' ' .
    ($row['middle_name'] ?? '')
);

// ---------------------------------------------------------------
// REQUIRED DOCUMENTS
// ---------------------------------------------------------------

$requiredDocuments = [
    'PSA Birth Certificate',
    'Grade 10 Report Card (Form 138)',
    'Certificate of Good Moral',
    'Recent 2x2 ID Picture',
];

if ($row['student_type'] === 'Transferee') {
    $requiredDocuments[] = 'Transcript of Records / Form 137';
}

// ---------------------------------------------------------------
// GET UPLOADED DOCUMENTS
// ---------------------------------------------------------------

$stmt = $conn->prepare("
    SELECT document_type, file_path
    FROM enrollment_documents
    WHERE enrollment_id = ?
");

$stmt->bind_param("i", $enrollment_id);
$stmt->execute();

$uploadedRows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();

$documents = [];
$uploadedTypes = [];

foreach ($uploadedRows as $doc) {
    $uploadedTypes[] = $doc['document_type'];

    $documents[] = [
        'type' => $doc['document_type'],
        'url' => '/enrollment_system/' . $doc['file_path'],
    ];
}

$missingDocuments = array_values(
    array_diff($requiredDocuments, $uploadedTypes)
);

$hasMissingDocuments = !empty($missingDocuments);

include '../includes/header.php';
?>

<main class="page-transition p-6 sm:p-8 min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto space-y-8">

        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-person-badge-fill"></i>
                    Student Record
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                    <?= htmlspecialchars($studentName) ?>
                </h1>

                <p class="text-blue-100 text-sm mt-1">
                    Student No:
                    <span class="font-mono font-bold text-amber-300">
                        <?= htmlspecialchars($row['student_number'] ?? 'PENDING') ?>
                    </span>
                    • Strand:
                    <span class="font-semibold">
                        <?= htmlspecialchars($row['strand_name']) ?>
                    </span>
                </p>
            </div>

            <div class="relative z-10">
                <?php if ($hasMissingDocuments): ?>

                    <span class="badge-pill badge-incomplete px-4 py-1.5 text-xs font-bold shadow-sm">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        Incomplete Requirements
                    </span>

                <?php else: ?>

                    <span class="badge-pill badge-complete px-4 py-1.5 text-xs font-bold shadow-sm">
                        <i class="bi bi-check-circle-fill"></i>
                        Requirements Complete
                    </span>

                <?php endif; ?>
            </div>
        </div>

        <div class="space-y-8">

            <!-- STUDENT INFORMATION -->
            <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">

                <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2.5">
                    <i class="bi bi-person-fill text-amber-300"></i>
                    <h2 class="font-bold text-base tracking-tight">
                        Student Information
                    </h2>
                </div>

                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-5 p-6 sm:p-8 text-sm">

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Full Name
                        </label>

                        <input
                            type="text"
                            readonly
                            value="<?= htmlspecialchars($studentName) ?>"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Student Category
                        </label>

                        <input
                            type="text"
                            readonly
                            value="<?= htmlspecialchars($row['student_type']) ?>"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Grade Level
                        </label>

                        <input
                            type="text"
                            readonly
                            value="<?= htmlspecialchars($row['grade_level']) ?>"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-slate-800"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Assigned Strand
                        </label>

                        <input
                            type="text"
                            readonly
                            value="<?= htmlspecialchars($row['strand_name']) ?>"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Email Address
                        </label>

                        <input
                            type="text"
                            readonly
                            value="<?= htmlspecialchars($row['email']) ?>"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-slate-800"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Official Student Number
                        </label>

                        <input
                            type="text"
                            readonly
                            value="<?= htmlspecialchars($row['student_number'] ?? 'PENDING') ?>"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-mono text-blue-700 font-bold"
                        >
                    </div>

                </div>
            </div>

            <!-- REQUIREMENTS SECTION -->
            <div class="grid md:grid-cols-2 gap-8">

                <!-- Uploaded Requirements -->
                <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">

                    <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-file-earmark-check-fill text-amber-300"></i>
                            <h3 class="font-bold text-sm tracking-tight">
                                Submitted Documents
                            </h3>
                        </div>

                        <span class="text-xs bg-white/10 px-3 py-1 rounded-full text-blue-100 font-medium">
                            <?= count($documents) ?> Files
                        </span>
                    </div>

                    <div class="p-6">

                        <?php if (empty($documents)): ?>

                            <p class="text-slate-400 text-xs text-center py-4">
                                No uploaded requirements on file.
                            </p>

                        <?php else: ?>

                            <div class="space-y-3">

                                <?php foreach ($documents as $doc): ?>

                                    <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-xs">

                                        <span class="font-semibold text-slate-800">
                                            <?= htmlspecialchars($doc['type']) ?>
                                        </span>

                                        <button
                                            type="button"
                                            onclick="openDocumentModal(
                                                '<?= htmlspecialchars($doc['url'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($doc['type'], ENT_QUOTES) ?>'
                                            )"
                                            class="btn-primary px-3 py-1.5 rounded-xl text-xs font-semibold cursor-pointer shadow-sm"
                                        >
                                            View
                                        </button>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; ?>

                    </div>
                </div>

                <!-- Missing Requirements / Status -->
                <div class="card-elevated overflow-hidden border border-slate-200/80 bg-white">

                    <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-4 flex items-center gap-2">
                        <i class="bi bi-clipboard-check-fill text-amber-300"></i>

                        <h3 class="font-bold text-sm tracking-tight">
                            Compliance Checklist
                        </h3>
                    </div>

                    <div class="p-6">

                        <?php if ($hasMissingDocuments): ?>

                            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 text-xs space-y-3">

                                <div class="font-bold text-rose-800 flex items-center gap-2 text-sm">
                                    <i class="bi bi-exclamation-triangle-fill text-rose-600"></i>
                                    <span>Missing Requirements</span>
                                </div>

                                <ul class="list-disc list-inside text-rose-700 space-y-1.5 font-medium pl-1">
                                    <?php foreach ($missingDocuments as $missing): ?>
                                        <li>
                                            <?= htmlspecialchars($missing) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                            </div>

                        <?php else: ?>

                            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center text-emerald-800 space-y-2">

                                <i class="bi bi-check-circle-fill text-emerald-500 text-3xl block"></i>

                                <h4 class="font-bold text-sm">
                                    All Documents Completed
                                </h4>

                                <p class="text-xs text-emerald-700">
                                    This student has satisfied all mandatory enrollment documentary requirements.
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<!-- DOCUMENT PREVIEW MODAL -->
<div
    id="documentModal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4 sm:p-6 transition-all duration-300"
>
    <div
        class="absolute inset-0"
        onclick="closeDocumentModal()"
    ></div>

    <div
        id="documentModalContainer"
        class="relative w-full max-w-5xl h-[88vh] bg-white rounded-3xl shadow-2xl overflow-hidden scale-95 opacity-0 transition-all duration-300 flex flex-col z-10"
    >
        <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200 bg-white">

            <div>
                <h3
                    id="documentModalTitle"
                    class="text-base font-bold text-slate-900"
                >
                    Requirement
                </h3>

                <p class="text-xs text-slate-500">
                    Uploaded document preview
                </p>
            </div>

            <button
                onclick="closeDocumentModal()"
                class="w-9 h-9 rounded-full bg-slate-100 hover:bg-red-100 hover:text-red-600 text-xl font-bold flex items-center justify-center transition cursor-pointer"
            >
                &times;
            </button>

        </div>

        <div
            id="documentModalBody"
            class="flex-1 bg-slate-100 flex items-center justify-center overflow-auto p-4 sm:p-6"
        ></div>
    </div>
</div>

<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/admin-enrollment-review-v2.js"></script>

<?php include '../includes/footer.php'; ?>