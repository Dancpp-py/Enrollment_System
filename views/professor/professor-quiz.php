<?php
require_once '../../config/db.php';
require_once '../includes/auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

requireProfessorLogin();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once '../../controllers/professor_quiz_data.php';

$quizzesStmt = $conn->prepare("
    SELECT
        q.quiz_id, q.title, q.time_limit, q.status,
        sub.subject_name, sec.section_name,
        (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id = q.quiz_id) AS total_items
    FROM quizzes q
    INNER JOIN class_schedules cs ON cs.schedule_id = q.schedule_id
    INNER JOIN curriculum c ON c.curriculum_id = cs.curriculum_id
    INNER JOIN subjects sub ON sub.subject_id = c.subject_id
    INNER JOIN sections sec ON sec.section_id = cs.section_id
    WHERE q.professor_id = ?
    ORDER BY q.created_at DESC
");

$quizzesStmt->bind_param("i", $professor_id);
$quizzesStmt->execute();
$quizzes = $quizzesStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$quizzesStmt->close();

include '../includes/header.php';
include '../includes/professor-sidebar.php';
?>

<div class="page-transition min-h-screen bg-slate-50 pt-20 lg:ml-72 p-6">
    <div class="max-w-7xl mx-auto space-y-8">

        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-8 shadow-xl">
            <div class="relative z-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-3">
                        <i class="bi bi-ui-checks-grid"></i>
                        Quiz Management
                    </div>

                    <h1 class="text-3xl font-extrabold tracking-tight text-white">
                        Created Quizzes
                    </h1>

                    <p class="mt-2 text-sm text-blue-100 max-w-2xl">
                        View and manage the quizzes you have created for your assigned subjects and sections.
                    </p>
                </div>

                <form method="POST" action="professor-quiz-v2">
                    <input type="hidden" name="create_quiz" value="1">

                    <button
                        type="submit"
                        class="inline-flex cursor-pointer items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-[#0A1931] font-bold text-sm shadow-lg transition-all duration-200 hover:-translate-y-0.5 whitespace-nowrap"
                    >
                        <i class="bi bi-plus-lg"></i>
                        Create Quiz
                    </button>
                </form>
            </div>
        </div>

        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-[#0A1931]">
                            Quiz Summary
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            List of quizzes created by the professor.
                        </p>
                    </div>

                    <div class="relative w-full md:w-80">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>

                        <input
                            type="text"
                            id="quizSearch"
                            placeholder="Search quizzes..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-sm text-slate-700 outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                        >
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 border-b border-slate-200 text-slate-700 font-semibold text-xs">
                        <tr>
                            <th class="px-6 py-4">Quiz</th>
                            <th class="px-6 py-4">Subject</th>
                            <th class="px-6 py-4">Section</th>
                            <th class="px-6 py-4 text-center">Items</th>
                            <th class="px-6 py-4 text-center">Time Limit</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody id="quizTableBody" class="divide-y divide-slate-100">
                    <?php if (!empty($quizzes)): ?>
                        <?php foreach ($quizzes as $quiz): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900">
                                        <?= htmlspecialchars($quiz['title']) ?>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-slate-700">
                                    <?= htmlspecialchars($quiz['subject_name']) ?>
                                </td>

                                <td class="px-6 py-5 text-slate-700">
                                    <?= htmlspecialchars($quiz['section_name']) ?>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <span class="font-semibold text-slate-700">
                                        <?= (int)$quiz['total_items'] ?>
                                    </span>
                                    <span class="text-slate-400 text-xs">items</span>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <span class="font-semibold text-slate-700">
                                        <?= $quiz['time_limit'] !== null ? (int)$quiz['time_limit'] . ' min' : '—' ?>
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <?php if ($quiz['status'] === 'Published'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Published
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Draft
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-2">

                                        <form method="POST" action="professor-quiz-v2">
                                            <input type="hidden" name="quiz_id" value="<?= (int)$quiz['quiz_id'] ?>">

                                            <button
                                                type="submit"
                                                class="w-10 cursor-pointer h-10 inline-flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition"
                                                title="Edit Quiz"
                                            >
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="../../controllers/professor_quiz_actions.php"
                                            class="toggle-quiz-form"
                                        >
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="quiz_id" value="<?= (int)$quiz['quiz_id'] ?>">

                                            <button
                                                type="submit"
                                                class="w-10 h-10 cursor-pointer inline-flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition"
                                                title="<?= $quiz['status'] === 'Published' ? 'Unpublish' : 'Publish' ?> Quiz"
                                                data-status="<?= htmlspecialchars($quiz['status']) ?>"
                                            >
                                                <i class="bi bi-<?= $quiz['status'] === 'Published' ? 'eye-slash' : 'eye' ?>"></i>
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="../../controllers/professor_quiz_actions.php"
                                            class="delete-quiz-form"
                                        >
                                            <input type="hidden" name="action" value="delete_quiz">
                                            <input type="hidden" name="quiz_id" value="<?= (int)$quiz['quiz_id'] ?>">

                                            <button
                                                type="submit"
                                                class="w-10 h-10 cursor-pointer inline-flex items-center justify-center rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition"
                                                title="Delete Quiz"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-16 text-slate-400">
                                <i class="bi bi-ui-checks-grid text-4xl block mb-3 text-slate-300"></i>
                                <p class="font-bold text-slate-700">No Quizzes Yet</p>
                                <p class="text-xs text-slate-500 mt-1">You haven't created any quizzes yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>

<script src="/enrollment_system/assets/js/swal.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('quizSearch');
    const rows = document.querySelectorAll('#quizTableBody tr');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const search = this.value.toLowerCase().trim();

            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
            });
        });
    }

    document.querySelectorAll('.toggle-quiz-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const button = form.querySelector('button');
            const status = button.dataset.status;
            const actionText = status === 'Published' ? 'Unpublish' : 'Publish';

            Swal.fire({
                icon: 'warning',
                title: actionText + ' Quiz?',
                text: 'Are you sure you want to ' + actionText.toLowerCase() + ' this quiz?',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    document.querySelectorAll('.delete-quiz-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Delete Quiz?',
                text: 'This will permanently delete the quiz and its questions.',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>