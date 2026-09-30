 document.addEventListener('DOMContentLoaded', function () {
    const questionsContainer = document.getElementById('questions-container');
    const addQuestionButton = document.getElementById('add-question');
    const questionCount = document.getElementById('question-count');

    let questionNumber = 1;

    function updateQuestionNumbers() {
        const questionBlocks = document.querySelectorAll('.question-block');

        questionBlocks.forEach(function (block, index) {
            const number = index + 1;
            block.dataset.questionNumber = number;

            const numberBadge = block.querySelector('.question-number');
            const title = block.querySelector('h3');

            if (numberBadge) {
                numberBadge.textContent = number;
            }

            if (title) {
                title.textContent = 'Question ' + number;
            }
        });

        questionNumber = questionBlocks.length;

        questionCount.innerHTML = `
            <i class="bi bi-list-check"></i>
            <span>${questionBlocks.length} ${questionBlocks.length === 1 ? 'Question' : 'Questions'}</span>
        `;

        document.querySelectorAll('.remove-question').forEach(function (button) {
            if (questionBlocks.length > 1) {
                button.classList.remove('hidden');
            } else {
                button.classList.add('hidden');
            }
        });
    }

    function createQuestionBlock(number) {
        const questionBlock = document.createElement('div');

        questionBlock.className =
            'question-block rounded-2xl border border-slate-200 bg-white overflow-hidden';

        questionBlock.dataset.questionNumber = number;

        questionBlock.innerHTML = `
            <div class="flex items-center justify-between gap-4 px-5 py-4 bg-slate-50 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="question-number w-9 h-9 rounded-xl bg-[#0A1931] text-white flex items-center justify-center text-sm font-bold">
                        ${number}
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">
                            Question ${number}
                        </h3>
                        <p class="text-xs text-slate-500">
                            Enter the question and its choices.
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    class="remove-question w-9 h-9 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition"
                    title="Remove question"
                >
                    <i class="bi bi-trash3"></i>
                </button>
            </div>

            <div class="p-5 space-y-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2">
                        Question
                    </label>
                    <textarea
                        name="question[]"
                        rows="3"
                        placeholder="Enter the question here..."
                        class="w-full resize-none rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-3">
                        Choices
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2">
                                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                                    A
                                </span>
                                Choice A
                            </label>
                            <input
                                type="text"
                                name="choice_a[]"
                                placeholder="Enter choice A"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >
                        </div>

                        <div>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2">
                                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                                    B
                                </span>
                                Choice B
                            </label>
                            <input
                                type="text"
                                name="choice_b[]"
                                placeholder="Enter choice B"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >
                        </div>

                        <div>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2">
                                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                                    C
                                </span>
                                Choice C
                            </label>
                            <input
                                type="text"
                                name="choice_c[]"
                                placeholder="Enter choice C"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >
                        </div>

                        <div>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2">
                                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                                    D
                                </span>
                                Choice D
                            </label>
                            <input
                                type="text"
                                name="choice_d[]"
                                placeholder="Enter choice D"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-600 mb-2">
                        Correct Answer
                    </label>
                    <div class="relative">
                        <i class="bi bi-check-circle absolute left-4 top-1/2 -translate-y-1/2 text-emerald-500"></i>
                        <select
                            name="correct_answer[]"
                            class="w-full appearance-none rounded-xl border border-slate-300 bg-white py-3 pl-11 pr-10 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                        >
                            <option value="">
                                Select the correct answer
                            </option>
                            <option value="A">Choice A</option>
                            <option value="B">Choice B</option>
                            <option value="C">Choice C</option>
                            <option value="D">Choice D</option>
                        </select>
                        <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>
            </div>
        `;

        return questionBlock;
    }

    addQuestionButton.addEventListener('click', function () {
        const newNumber = document.querySelectorAll('.question-block').length + 1;
        const newQuestion = createQuestionBlock(newNumber);

        questionsContainer.appendChild(newQuestion);
        updateQuestionNumbers();

        newQuestion.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    });

    questionsContainer.addEventListener('click', function (event) {
        const removeButton = event.target.closest('.remove-question');

        if (!removeButton) {
            return;
        }

        const questionBlock = removeButton.closest('.question-block');

        if (!questionBlock) {
            return;
        }

        const questionBlocks = document.querySelectorAll('.question-block');

        if (questionBlocks.length <= 1) {
            return;
        }

        questionBlock.remove();
        updateQuestionNumbers();
    });

    document.getElementById('submit-quiz').addEventListener('click', function () {
        console.log('Quiz submission placeholder.');
    });

    updateQuestionNumbers();
});