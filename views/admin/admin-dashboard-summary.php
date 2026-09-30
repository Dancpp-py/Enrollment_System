<?php 
$page_title = "Enrollment Summary Dashboard - Masinag SHS";
require_once '../../config/db.php';
require_once '../includes/auth.php';
requireRole('Super Admin');

include '../includes/header.php';  
include '../includes/sidebar.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// 1. Fetch Real Summary Metrics from Existing Database
$metricsSql = "
    SELECT 
        COUNT(*) AS total_applicants,
        SUM(CASE WHEN e.status = 'Pending' THEN 1 ELSE 0 END) AS waiting_approval,
        SUM(CASE WHEN e.status = 'Confirmed' AND e.stage = 'Exam Scheduled' THEN 1 ELSE 0 END) AS scheduled_exam,
        SUM(CASE WHEN e.status = 'Confirmed' AND e.stage = 'Enrollment Review' THEN 1 ELSE 0 END) AS enrollment_review,
        SUM(CASE WHEN e.status = 'Confirmed' AND e.stage = 'Enrolled' THEN 1 ELSE 0 END) AS officially_enrolled,
        SUM(CASE WHEN e.status = 'Rejected' THEN 1 ELSE 0 END) AS rejected_count
    FROM enrollments e
";
$metricsResult = $conn->query($metricsSql);
$metrics = $metricsResult ? $metricsResult->fetch_assoc() : [
    'total_applicants' => 0,
    'waiting_approval' => 0,
    'scheduled_exam' => 0,
    'enrollment_review' => 0,
    'officially_enrolled' => 0,
    'rejected_count' => 0
];

$totalApplicants    = (int)($metrics['total_applicants'] ?? 0);
$waitingApproval    = (int)($metrics['waiting_approval'] ?? 0);
$scheduledExam      = (int)($metrics['scheduled_exam'] ?? 0);
$enrollmentReview   = (int)($metrics['enrollment_review'] ?? 0);
$officiallyEnrolled = (int)($metrics['officially_enrolled'] ?? 0);
$rejectedCount      = (int)($metrics['rejected_count'] ?? 0);

// 2. Fetch Available Strands for Compact Filter
$strandsList = [];
$strandsResult = $conn->query("SELECT strand_id, strand_name FROM strands ORDER BY strand_name ASC");
if ($strandsResult) {
    while ($st = $strandsResult->fetch_assoc()) {
        $strandsList[] = $st;
    }
}

// 3. Fetch Active School Year
$activeSchoolYear = "Active Academic Year";
$syResult = $conn->query("SELECT school_year FROM school_years WHERE is_active = 1 LIMIT 1");
if ($syResult && $syResult->num_rows > 0) {
    $activeSchoolYear = $syResult->fetch_assoc()['school_year'];
}

// 4. Fetch All Real Enrollment Applications
$sql = "
    SELECT
        e.enrollment_id,
        e.student_id,
        e.strand_id,
        e.grade_level,
        e.semester,
        e.status,
        e.stage,
        e.section_id,
        s.student_number,
        s.exam_number,
        s.student_type,
        s.last_name,
        s.first_name,
        s.middle_name,
        s.suffix,
        s.email,
        s.contact_number,
        s.gender,
        s.birth_date,
        s.address,
        st.strand_name,
        sy.school_year,
        sec.section_name
    FROM enrollments e
    INNER JOIN students s ON e.student_id = s.student_id
    INNER JOIN strands st ON e.strand_id = st.strand_id
    INNER JOIN school_years sy ON e.school_year_id = sy.school_year_id
    LEFT JOIN sections sec ON e.section_id = sec.section_id
    ORDER BY e.enrollment_id DESC
";

$result = $conn->query($sql);
$enrollments = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Map actual database status and stage to human-readable labels and badge classes
        if ($row['status'] === 'Pending') {
            $row['status_key'] = 'waiting_approval';
            $row['status_label'] = 'Waiting for Approval';
            $row['status_badge'] = 'bg-amber-50 text-amber-800 border-amber-200';
            $row['status_icon'] = 'bi-hourglass-split';
        } elseif ($row['status'] === 'Confirmed' && $row['stage'] === 'Exam Scheduled') {
            $row['status_key'] = 'scheduled_exam';
            $row['status_label'] = 'Scheduled for Entrance Exam';
            $row['status_badge'] = 'bg-blue-50 text-blue-800 border-blue-200';
            $row['status_icon'] = 'bi-calendar-check-fill';
        } elseif ($row['status'] === 'Confirmed' && $row['stage'] === 'Enrollment Review') {
            $row['status_key'] = 'enrollment_review';
            $row['status_label'] = 'Enrollment Review';
            $row['status_badge'] = 'bg-indigo-50 text-indigo-800 border-indigo-200';
            $row['status_icon'] = 'bi-clipboard2-check-fill';
        } elseif ($row['status'] === 'Confirmed' && $row['stage'] === 'Enrolled') {
            $row['status_key'] = 'officially_enrolled';
            $row['status_label'] = 'Officially Enrolled';
            $row['status_badge'] = 'bg-emerald-50 text-emerald-800 border-emerald-200';
            $row['status_icon'] = 'bi-patch-check-fill';
        } elseif ($row['status'] === 'Rejected') {
            $row['status_key'] = 'rejected';
            $row['status_label'] = 'Rejected';
            $row['status_badge'] = 'bg-rose-50 text-rose-800 border-rose-200';
            $row['status_icon'] = 'bi-x-circle-fill';
        } else {
            $row['status_key'] = 'pending';
            $row['status_label'] = htmlspecialchars($row['stage'] ?: $row['status']);
            $row['status_badge'] = 'bg-slate-100 text-slate-700 border-slate-200';
            $row['status_icon'] = 'bi-info-circle-fill';
        }

        $enrollments[] = $row;
    }
}
?>

<main class="page-transition lg:ml-72 pt-20 p-4 sm:p-6 min-h-screen bg-slate-50 text-slate-800">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-lg text-white relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-2">
                        <i class="bi bi-speedometer2"></i> Executive Overview
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                        Enrollment Dashboard Summary
                    </h1>
                    <p class="text-blue-100 mt-1 text-xs sm:text-sm max-w-2xl">
                        Monitor senior high school applicant workflows, examinee scheduling, and official enrollment metrics.
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="bg-white/10 backdrop-blur-md rounded-xl sm:rounded-2xl px-4 py-2.5 border border-white/15 text-right">
                        <span class="text-xs text-blue-100 font-medium block">School Year</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300"><?= htmlspecialchars($activeSchoolYear) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Total Applicants -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Applicants</p>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-[#0A1931] mt-1.5"><?= number_format($totalApplicants) ?></h3>
                        <p class="text-[11px] text-slate-400 mt-1">All registered applications</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-[#1E4DB7] flex items-center justify-center text-xl shrink-0 border border-blue-100">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>

            <!-- 2. Waiting for Approval -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Waiting for Approval</p>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1.5"><?= number_format($waitingApproval) ?></h3>
                        <p class="text-[11px] text-slate-400 mt-1">Pending initial review</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0 border border-amber-100">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>

            <!-- 3. Scheduled for Entrance Exam -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Scheduled for Entrance Exam</p>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-blue-600 mt-1.5"><?= number_format($scheduledExam) ?></h3>
                        <p class="text-[11px] text-slate-400 mt-1">Awaiting examination scores</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl shrink-0 border border-sky-100">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                </div>
            </div>

            <!-- 4. Officially Enrolled -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Officially Enrolled</p>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1.5"><?= number_format($officiallyEnrolled) ?></h3>
                        <p class="text-[11px] text-slate-400 mt-1">Confirmed & assigned sections</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 border border-emerald-100">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- Compact Filter Section & Table Container -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            
            <!-- Filter Bar -->
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-white space-y-4">
                
                <!-- Status Quick Filters -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs scrollbar-thin">
                    <button type="button" data-status-filter="all" class="status-btn active px-3.5 py-1.5 rounded-xl font-bold transition duration-150 cursor-pointer bg-[#0A1931] text-white shadow-sm shrink-0">
                        All (<?= count($enrollments) ?>)
                    </button>
                    <button type="button" data-status-filter="waiting_approval" class="status-btn px-3.5 py-1.5 rounded-xl font-bold transition duration-150 cursor-pointer bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-800 border border-transparent hover:border-amber-200 shrink-0">
                        Waiting for Approval (<?= $waitingApproval ?>)
                    </button>
                    <button type="button" data-status-filter="scheduled_exam" class="status-btn px-3.5 py-1.5 rounded-xl font-bold transition duration-150 cursor-pointer bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-800 border border-transparent hover:border-blue-200 shrink-0">
                        Scheduled for Exam (<?= $scheduledExam ?>)
                    </button>
                    <button type="button" data-status-filter="enrollment_review" class="status-btn px-3.5 py-1.5 rounded-xl font-bold transition duration-150 cursor-pointer bg-slate-100 hover:bg-indigo-100 text-slate-700 hover:text-indigo-800 border border-transparent hover:border-indigo-200 shrink-0">
                        Enrollment Review (<?= $enrollmentReview ?>)
                    </button>
                    <button type="button" data-status-filter="officially_enrolled" class="status-btn px-3.5 py-1.5 rounded-xl font-bold transition duration-150 cursor-pointer bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 border border-transparent hover:border-emerald-200 shrink-0">
                        Officially Enrolled (<?= $officiallyEnrolled ?>)
                    </button>
                    <?php if ($rejectedCount > 0): ?>
                    <button type="button" data-status-filter="rejected" class="status-btn px-3.5 py-1.5 rounded-xl font-bold transition duration-150 cursor-pointer bg-slate-100 hover:bg-rose-100 text-slate-700 hover:text-rose-800 border border-transparent hover:border-rose-200 shrink-0">
                        Rejected (<?= $rejectedCount ?>)
                    </button>
                    <?php endif; ?>
                </div>

                <!-- Secondary Filters: Search, Strand, Grade Level, Student Type -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center pt-1 border-t border-slate-100">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-4 relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                            <i class="bi bi-search text-xs"></i>
                        </span>
                        <input
                            type="text"
                            id="searchQuery"
                            placeholder="Search student name, ID, or exam no..."
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#1E4DB7] focus:border-transparent transition bg-slate-50/50">
                    </div>

                    <!-- Strand Filter -->
                    <div class="lg:col-span-3">
                        <select
                            id="filterStrand"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#1E4DB7] focus:border-transparent transition bg-slate-50/50 text-slate-700 font-medium">
                            <option value="">All Academic Strands</option>
                            <?php foreach ($strandsList as $st): ?>
                                <option value="<?= htmlspecialchars($st['strand_name']) ?>">
                                    <?= htmlspecialchars($st['strand_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Grade Level Filter -->
                    <div class="lg:col-span-2">
                        <select
                            id="filterGrade"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#1E4DB7] focus:border-transparent transition bg-slate-50/50 text-slate-700 font-medium">
                            <option value="">All Grade Levels</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>
                    </div>

                    <!-- Applicant Type Filter -->
                    <div class="lg:col-span-2">
                        <select
                            id="filterType"
                            class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#1E4DB7] focus:border-transparent transition bg-slate-50/50 text-slate-700 font-medium">
                            <option value="">All Applicant Types</option>
                            <option value="New">New</option>
                            <option value="Transferee">Transferee</option>
                            <option value="Existing">Existing</option>
                        </select>
                    </div>

                    <!-- Reset Filters Button -->
                    <div class="lg:col-span-1 flex justify-end">
                        <button
                            type="button"
                            id="resetFiltersBtn"
                            title="Reset all filters"
                            class="w-full py-2 px-3 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-600 text-xs font-semibold flex items-center justify-center gap-1.5 transition cursor-pointer">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span class="sm:hidden lg:hidden">Reset</span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Table Header Bar -->
            <div class="bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white px-6 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-table text-amber-300"></i>
                    <h2 class="font-bold text-sm tracking-tight">Applicant Records Summary</h2>
                </div>
                <span id="recordCountBadge" class="text-xs font-semibold bg-white/10 px-3 py-0.5 rounded-full text-blue-100 border border-white/10">
                    <?= count($enrollments) ?> Records
                </span>
            </div>

            <!-- Responsive Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse table-fixed">
                    <thead class="bg-slate-100 text-slate-700 border-b border-slate-200 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 w-4/12">Student Name</th>
                            <th class="px-5 py-3.5 w-2/12">Applicant Type</th>
                            <th class="px-5 py-3.5 w-2/12">Strand</th>
                            <th class="px-5 py-3.5 w-2/12">Grade Level</th>
                            <th class="px-5 py-3.5 w-2/12">Current Status</th>
                        </tr>
                    </thead>
                    <tbody id="enrollmentTableBody" class="divide-y divide-slate-200/70">
                        <?php if (empty($enrollments)): ?>
                            <tr id="noDataRow">
                                <td colspan="5" class="text-center py-12 text-slate-400">
                                    <i class="bi bi-inbox text-4xl text-slate-300 block mb-2"></i>
                                    <p class="font-bold text-slate-700 text-sm">No Enrollment Records Found</p>
                                    <p class="text-xs text-slate-500 mt-0.5">There are no student applications in the system yet.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($enrollments as $row): ?>
                                <?php 
                                    $fullName = htmlspecialchars($row['last_name'] . ', ' . $row['first_name'] . ($row['middle_name'] ? ' ' . substr($row['middle_name'], 0, 1) . '.' : '') . ($row['suffix'] ? ' ' . $row['suffix'] : ''));
                                    $searchableText = strtolower($row['last_name'] . ' ' . $row['first_name'] . ' ' . ($row['student_number'] ?? '') . ' ' . ($row['exam_number'] ?? '') . ' ' . $row['strand_name'] . ' ' . $row['email']);
                                ?>
                                <tr class="enrollment-row hover:bg-slate-50/80 transition duration-100"
                                    data-status="<?= htmlspecialchars($row['status_key']) ?>"
                                    data-strand="<?= htmlspecialchars($row['strand_name']) ?>"
                                    data-grade="<?= htmlspecialchars($row['grade_level']) ?>"
                                    data-type="<?= htmlspecialchars($row['student_type']) ?>"
                                    data-search="<?= htmlspecialchars($searchableText) ?>">
                                    
                                    <!-- Student Name -->
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-xs shrink-0">
                                                <?= strtoupper(substr($row['first_name'], 0, 1)) ?>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 leading-tight truncate" title="<?= $fullName ?>">
                                                    <?= $fullName ?>
                                                </p>
                                                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 font-mono">
                                                    <?php if (!empty($row['student_number'])): ?>
                                                        <span class="text-blue-700 font-semibold" title="Student Number">ID: <?= htmlspecialchars($row['student_number']) ?></span>
                                                    <?php elseif (!empty($row['exam_number'])): ?>
                                                        <span class="text-amber-700 font-semibold" title="Exam Number">Exam: <?= htmlspecialchars($row['exam_number']) ?></span>
                                                    <?php else: ?>
                                                        <span>App #<?= htmlspecialchars($row['enrollment_id']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Applicant Type -->
                                    <td class="px-5 py-3.5">
                                        <?php if ($row['student_type'] === 'New'): ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                New
                                            </span>
                                        <?php elseif ($row['student_type'] === 'Transferee'): ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                                Transferee
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                Existing
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Strand -->
                                    <td class="px-5 py-3.5">
                                        <span class="font-semibold text-slate-800">
                                            <?= htmlspecialchars($row['strand_name']) ?>
                                        </span>
                                    </td>

                                    <!-- Grade Level -->
                                    <td class="px-5 py-3.5">
                                        <div class="text-slate-700 font-medium">
                                            <?= htmlspecialchars($row['grade_level']) ?>
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            <?= htmlspecialchars($row['semester']) ?>
                                        </div>
                                    </td>

                                    <!-- Current Status -->
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border <?= $row['status_badge'] ?>">
                                            <i class="bi <?= $row['status_icon'] ?>"></i>
                                            <?= htmlspecialchars($row['status_label']) ?>
                                        </span>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="p-3.5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-2">
                <div>
                    Showing <span id="visibleCount" class="font-bold text-slate-800"><?= count($enrollments) ?></span> of <span class="font-bold text-slate-800"><?= count($enrollments) ?></span> total applications
                </div>
                <div class="text-[11px] text-slate-400">
                    Masinag Senior High School &bull; Enrollment Management
                </div>
            </div>

        </div>

    </div>
</main>
<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/admin-dashboard-summary.js"></script>