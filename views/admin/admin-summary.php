<?php

$page_title = "Student Records Directory - Masinag SHS";

require_once '../../config/db.php';
require_once '../includes/auth.php';

// session_start();

requireRole('Registrar', 'Admissions', 'Super Admin');

include '../includes/header.php';
include '../includes/sidebar.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$baseRequiredDocs = [
    'PSA Birth Certificate',
    'Grade 10 Report Card (Form 138)',
    'Certificate of Good Moral',
    'Recent 2x2 ID Picture',
];

$transfereeExtraDoc = 'Transcript of Records / Form 137';

$result = $conn->query("
    SELECT
        e.enrollment_id,
        s.student_id,
        s.student_number,
        s.first_name,
        s.last_name,
        s.student_type,
        s.email
    FROM enrollments e
    INNER JOIN students s ON e.student_id = s.student_id
    WHERE e.stage = 'Enrolled'
      AND e.status = 'Confirmed'
    ORDER BY e.enrollment_id DESC
");

$students = [];
$enrollmentIds = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
    $enrollmentIds[] = (int) $row['enrollment_id'];
}

$docsByEnrollment = [];

if (!empty($enrollmentIds)) {
    $placeholders = implode(',', array_fill(0, count($enrollmentIds), '?'));
    $types = str_repeat('i', count($enrollmentIds));

    $stmt = $conn->prepare("
        SELECT enrollment_id, document_type
        FROM enrollment_documents
        WHERE enrollment_id IN ($placeholders)
    ");

    $stmt->bind_param($types, ...$enrollmentIds);
    $stmt->execute();

    $docsResult = $stmt->get_result();

    while ($docRow = $docsResult->fetch_assoc()) {
        $docsByEnrollment[$docRow['enrollment_id']][] = $docRow['document_type'];
    }

    $stmt->close();
}

$completeCount = 0;
$incompleteCount = 0;

foreach ($students as &$student) {
    $required = $baseRequiredDocs;

    if ($student['student_type'] === 'Transferee') {
        $required[] = $transfereeExtraDoc;
    }

    $uploaded = $docsByEnrollment[$student['enrollment_id']] ?? [];

    $student['missing_docs'] = array_values(
        array_diff($required, $uploaded)
    );

    $student['is_complete'] = empty($student['missing_docs']);

    if ($student['is_complete']) {
        $completeCount++;
    } else {
        $incompleteCount++;
    }
}

unset($student);
?>

<main class="page-transition lg:ml-72 pt-20 min-h-screen p-6 bg-slate-50">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                    <i class="bi bi-folder2-open"></i> Archive & Records
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight">
                    Student Records Management
                </h1>

                <p class="text-blue-100 mt-1 text-sm max-w-2xl">
                    Monitor enrolled students' documentary compliance and send missing requirement notices.
                </p>
            </div>

            <div class="relative z-10 flex gap-3">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 text-center min-w-[110px]">
                    <span class="text-2xl font-extrabold text-emerald-300">
                        <?= $completeCount ?>
                    </span>
                    <p class="text-[11px] text-blue-100 font-medium">
                        Complete
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 text-center min-w-[110px]">
                    <span class="text-2xl font-extrabold text-rose-300">
                        <?= $incompleteCount ?>
                    </span>
                    <p class="text-[11px] text-blue-100 font-medium">
                        Incomplete
                    </p>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="card-elevated overflow-hidden">

            <!-- Filter Bar -->
            <div class="p-5 border-b border-slate-200 bg-white flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        id="searchInput"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm"
                        placeholder="Search student name or ID..."
                    >
                </div>

                <div class="flex gap-2 w-full md:w-auto overflow-x-auto">
                    <button
                        data-filter="All"
                        class="px-4 py-2 rounded-xl bg-[#0A1931] text-white text-xs font-bold cursor-pointer transition shadow-sm"
                    >
                        All (<?= count($students) ?>)
                    </button>

                    <button
                        data-filter="Complete"
                        class="px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold cursor-pointer transition"
                    >
                        Complete (<?= $completeCount ?>)
                    </button>

                    <button
                        data-filter="Incomplete"
                        class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold cursor-pointer transition"
                    >
                        Incomplete (<?= $incompleteCount ?>)
                    </button>
                </div>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white">
                        <tr>
                            <th class="px-6 py-4">Student Profile</th>
                            <th class="px-6 py-4 text-center">Student Number</th>
                            <th class="px-6 py-4 text-center">Category</th>
                            <th class="px-6 py-4 text-center">Requirements Status</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200/80">
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    <i class="bi bi-inbox text-4xl text-slate-300 block mb-2"></i>
                                    <p class="font-bold text-slate-700">
                                        No Enrolled Records Found
                                    </p>
                                </td>
                            </tr>
                        <?php else: ?>

                            <?php foreach ($students as $student): ?>
                                <?php
                                $status = $student['is_complete']
                                    ? 'Complete'
                                    : 'Incomplete';
                                ?>

                                <tr
                                    class="hover:bg-slate-50/80 transition"
                                    data-status="<?= $status ?>"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center text-lg font-bold shrink-0">
                                                <i class="bi bi-person-fill"></i>
                                            </div>

                                            <div>
                                                <p class="font-bold text-slate-900 leading-tight">
                                                    <?= htmlspecialchars($student['last_name']) ?>,
                                                    <?= htmlspecialchars($student['first_name']) ?>
                                                </p>

                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    <?= htmlspecialchars($student['email']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-center font-mono font-bold text-blue-700 text-xs">
                                        <?= htmlspecialchars($student['student_number'] ?? 'PENDING') ?>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="badge-pill <?= $student['student_type'] === 'New' ? 'badge-new' : 'badge-transferee' ?>">
                                            <?= htmlspecialchars($student['student_type']) ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <?php if ($student['is_complete']): ?>

                                            <span class="badge-pill badge-complete">
                                                <i class="bi bi-check-circle-fill"></i>
                                                Complete
                                            </span>

                                        <?php else: ?>

                                            <span
                                                class="badge-pill badge-incomplete"
                                                title="Missing: <?= htmlspecialchars(implode(', ', $student['missing_docs'])) ?>"
                                            >
                                                <i class="bi bi-exclamation-circle-fill"></i>
                                                Incomplete
                                            </span>

                                        <?php endif; ?>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center gap-2">

                                            <form
                                                method="POST"
                                                action="admin-summary-v2"
                                                target="_blank"
                                                class="inline"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="enrollment_id"
                                                    value="<?= (int) $student['enrollment_id'] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    title="View Student Records"
                                                    class="w-9 h-9 rounded-xl bg-[#0A1931] hover:bg-[#1E4DB7] text-white flex items-center justify-center text-xs transition shadow-sm cursor-pointer"
                                                >
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>
                                            </form>

                                            <?php if (!$student['is_complete']): ?>
                                                <button
                                                    type="button"
                                                    title="Send Missing Documents Reminder Email"
                                                    onclick="notifyMissingRequirements(<?= (int) $student['enrollment_id'] ?>, this)"
                                                    class="w-9 h-9 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 flex items-center justify-center text-xs font-bold transition shadow-sm cursor-pointer"
                                                >
                                                    <i class="bi bi-envelope-fill"></i>
                                                </button>
                                            <?php endif; ?>

                                        </div>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/admin-summary.js"></script>