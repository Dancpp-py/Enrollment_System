<?php
require_once '../../config/db.php';
require_once '../includes/auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

requireProfessorLogin();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once '../../controllers/professor_quiz_data.php';
require_once '../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id'])) {
    $quiz_id = (int)$_POST['quiz_id'];

    if ($quiz_id <= 0) {
        showError("Invalid Quiz!", "The selected quiz is invalid.");
        exit;
    }

    $stmt = $conn->prepare("
        SELECT quiz_id
        FROM quizzes
        WHERE quiz_id = ? AND professor_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $quiz_id, $professor_id);
    $stmt->execute();
    $quizExists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if (!$quizExists) {
        showError("Quiz Not Found!", "The selected quiz does not exist or does not belong to you.");
        exit;
    }

    $_SESSION['professor_quiz_id'] = $quiz_id;

    header("Location: professor-quiz-v2");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_quiz'])) {
    unset($_SESSION['professor_quiz_id']);

    header("Location: professor-quiz-v2");
    exit;
}

$editingQuizId = (int)($_SESSION['professor_quiz_id'] ?? 0);
$editingQuiz = null;
$editingQuestions = [];

if ($editingQuizId > 0) {
    $stmt = $conn->prepare("
        SELECT quiz_id, schedule_id, title, time_limit
        FROM quizzes
        WHERE quiz_id = ? AND professor_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $editingQuizId, $professor_id);
    $stmt->execute();
    $editingQuiz = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$editingQuiz) {
        unset($_SESSION['professor_quiz_id']);
        $editingQuizId = 0;
    } else {
        $stmt = $conn->prepare("
            SELECT question_text, choice_a, choice_b, choice_c, choice_d, correct_answer
            FROM quiz_questions
            WHERE quiz_id = ?
            ORDER BY question_order
        ");
        $stmt->bind_param("i", $editingQuizId);
        $stmt->execute();
        $editingQuestions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

$isEditing = $editingQuizId > 0;

if (empty($editingQuestions)) {
    $editingQuestions = [[
        'question_text' => '',
        'choice_a' => '',
        'choice_b' => '',
        'choice_c' => '',
        'choice_d' => '',
        'correct_answer' => ''
    ]];
}

include '../includes/header.php';
?>

<div class="page-transition min-h-screen p-6 bg-slate-50">
    <div class="space-y-8">

        <div class="bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] rounded-3xl p-8 shadow-xl relative overflow-hidden">
            <div class="hero-glow-blob w-72 h-72 bg-blue-500/20 -top-32 -right-20"></div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-3">
                        <i class="bi bi-pencil-square"></i>
                        QUIZ MANAGEMENT
                    </div>

                    <h1 class="text-3xl font-extrabold tracking-tight text-white">
                        <?= $isEditing ? 'Edit Quiz' : 'Create Quiz' ?>
                    </h1>

                    <p class="text-blue-100 mt-2 text-sm">
                        <?= $isEditing
                            ? 'Update this quiz for your assigned subject and section.'
                            : 'Create a quiz for your assigned subject and section.' ?>
                    </p>
                </div>

                <a
                    href="professor-quiz"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-white font-semibold text-sm transition"
                >
                    <i class="bi bi-arrow-left"></i>
                    Back to Quizzes
                </a>
            </div>
        </div>

        <form method="POST" action="../../controllers/professor_quiz_actions.php" id="quizForm">
            <input type="hidden" name="action" value="save_quiz">
            <input type="hidden" name="quiz_id" value="<?= (int)$editingQuizId ?>">

            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                            <i class="bi bi-sliders2 text-lg"></i>
                        </div>

                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Quiz Settings</h2>
                            <p class="text-xs text-slate-500 mt-1">
                                Name the quiz, select where it will be given, and set its time limit.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">

                    <div>
                        <label for="quiz-title" class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2">
                            Quiz Title
                        </label>

                        <div class="relative">
                            <i class="bi bi-card-heading absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                            <input
                                type="text"
                                id="quiz-title"
                                name="title"
                                value="<?= htmlspecialchars($editingQuiz['title'] ?? '') ?>"
                                maxlength="150"
                                required
                                placeholder="e.g. Mathematics Midterm Quiz"
                                class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="lms-class" class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2">
                            Subject / Section
                        </label>

                        <div class="relative">
                            <i class="bi bi-book absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                            <select
                                id="lms-class"
                                name="lms_class_id"
                                required
                                class="w-full appearance-none rounded-xl border border-slate-300 bg-white py-3 pl-11 pr-10 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >
                                <option value="" disabled <?= !$isEditing ? 'selected' : '' ?>>
                                    Select Subject / Section
                                </option>

                                <?php foreach ($professorClasses as $class): ?>
                                    <option
                                        value="<?= (int)$class['schedule_id'] ?>"
                                        <?= $isEditing && (int)$editingQuiz['schedule_id'] === (int)$class['schedule_id'] ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($class['subject_name'] . ' - ' . $class['section_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>

                        <?php if (empty($professorClasses)): ?>
                            <p class="mt-2 text-xs text-rose-500">
                                You don't have any assigned subject/section yet — you can't create a quiz until you do.
                            </p>
                        <?php else: ?>
                            <p class="mt-2 text-xs text-slate-400">
                                Select the subject and section that will receive this quiz.
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="time-limit" class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2">
                            Time Limit
                        </label>

                        <div class="relative">
                            <i class="bi bi-stopwatch absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                            <input
                                type="number"
                                id="time-limit"
                                name="time_limit"
                                min="1"
                                max="300"
                                value="<?= $editingQuiz['time_limit'] !== null ? (int)$editingQuiz['time_limit'] : '' ?>"
                                placeholder="Optional"
                                class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >
                        </div>

                        <p class="mt-2 text-xs text-slate-400">
                            Leave blank for no time limit. Maximum: 300 minutes.
                        </p>
                    </div>

                </div>
            </section>

            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                                <i class="bi bi-question-circle text-lg"></i>
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Quiz Questions</h2>
                                <p class="text-xs text-slate-500 mt-1">
                                    Add your multiple-choice questions and select the correct answer.
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            id="add-question"
                            class="inline-flex cursor-pointer items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1E4DB7] hover:bg-blue-800 text-white text-xs font-bold transition"
                        >
                            <i class="bi bi-plus-lg"></i>
                            Add Question
                        </button>
                    </div>
                </div>

                <div id="questions-container" class="p-6 space-y-6">
                    <?php foreach ($editingQuestions as $index => $question): ?>
                        <div
                            class="question-block rounded-2xl border border-slate-200 bg-slate-50/70 p-6"
                            data-question-index="<?= (int)$index ?>"
                        >
                            <div class="flex items-center justify-between gap-4 mb-5">
                                <div class="flex items-center gap-3">
                                    <div class="question-number w-9 h-9 rounded-xl bg-[#0A1931] text-white flex items-center justify-center text-sm font-bold">
                                        <?= (int)$index + 1 ?>
                                    </div>

                                    <h3 class="font-bold text-slate-800">
                                        Question <?= (int)$index + 1 ?>
                                    </h3>
                                </div>

                                <button
                                    type="button"
                                    class="remove-question cursor-pointer text-rose-500 hover:text-rose-700 text-xs font-bold <?= count($editingQuestions) <= 1 ? 'hidden' : '' ?>"
                                >
                                    <i class="bi bi-trash mr-1"></i>
                                    Remove
                                </button>
                            </div>

                            <div class="space-y-5">

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2">
                                        Question
                                    </label>

                                    <textarea
                                        name="question[]"
                                        rows="3"
                                        required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        placeholder="Enter the question..."
                                    ><?= htmlspecialchars($question['question_text']) ?></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <?php foreach (['a' => 'Choice A', 'b' => 'Choice B', 'c' => 'Choice C', 'd' => 'Choice D'] as $letter => $label): ?>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2">
                                                <?= $label ?>
                                            </label>

                                            <input
                                                type="text"
                                                name="choice_<?= $letter ?>[]"
                                                value="<?= htmlspecialchars($question['choice_' . $letter]) ?>"
                                                required
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                                placeholder="Enter <?= $label ?>..."
                                            >
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2">
                                        Correct Answer
                                    </label>

                                    <select
                                        name="correct_answer[]"
                                        required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                    >
                                        <option value="" disabled <?= empty($question['correct_answer']) ? 'selected' : '' ?>>
                                            Select Correct Answer
                                        </option>

                                        <?php foreach (['A', 'B', 'C', 'D'] as $answer): ?>
                                            <option
                                                value="<?= $answer ?>"
                                                <?= $question['correct_answer'] === $answer ? 'selected' : '' ?>
                                            >
                                                <?= $answer ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-6">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <i class="bi bi-info-circle"></i>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-700">
                            Ready to <?= $isEditing ? 'save these changes' : 'create this quiz' ?>?
                        </p>

                        <p class="text-xs text-slate-500 mt-1">
                            Make sure the title, subject, time limit, questions, and correct answers are properly set.
                            <?= $isEditing
                                ? ' This quiz stays a Draft/Published as-is — change that from the quiz list.'
                                : ' New quizzes start as a Draft — publish it from the quiz list when ready.' ?>
                        </p>
                    </div>
                </div>

                <button
                    type="submit"
                    id="submit-quiz"
                    class="inline-flex cursor-pointer items-center justify-center gap-2 px-6 py-3 rounded-xl bg-[#1E4DB7] hover:bg-blue-800 text-white font-bold text-sm shadow-sm transition"
                >
                    <i class="bi bi-check2-circle"></i>
                    <?= $isEditing ? 'Save Changes' : 'Create Quiz' ?>
                </button>
            </section>
        </form>

    </div>
</div>

<script src="/enrollment_system/assets/js/professor-quiz-v2.js"></script>