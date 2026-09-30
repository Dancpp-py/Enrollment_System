document.addEventListener('DOMContentLoaded', function () {
    let currentQuestion = 0;

    const questions = document.querySelectorAll('.quiz-question');
    const indicators = document.querySelectorAll('.question-indicator');
    const totalQuestions = questions.length;
    const currentQuestionNumber = document.getElementById('currentQuestionNumber');
    const questionHeadingNumber = document.getElementById('questionHeadingNumber');
    const answeredCount = document.getElementById('answeredCount');
    const quizProgress = document.getElementById('quizProgress');
    const previousButton = document.getElementById('previousQuestion');
    const nextButton = document.getElementById('nextQuestion');
    const submitButton = document.getElementById('submitQuiz');
    const quizForm = document.getElementById('quizForm');

    function showQuestion(index) {
        if (index < 0 || index >= totalQuestions) {
            return;
        }

        currentQuestion = index;

        questions.forEach(function (question, questionIndex) {
            question.classList.toggle('hidden', questionIndex !== index);
        });

        indicators.forEach(function (indicator, indicatorIndex) {
            indicator.classList.toggle('active', indicatorIndex === index);
        });

        currentQuestionNumber.textContent = index + 1;
        questionHeadingNumber.textContent = index + 1;

        const progress = ((index + 1) / totalQuestions) * 100;
        quizProgress.style.width = progress + '%';

        previousButton.disabled = index === 0;

        if (index === totalQuestions - 1) {
            nextButton.classList.add('hidden');
            submitButton.classList.remove('hidden');
        } else {
            nextButton.classList.remove('hidden');
            submitButton.classList.add('hidden');
        }

        updateAnsweredCount();
    }

    function updateAnsweredCount() {
        let answered = 0;

        questions.forEach(function (question) {
            if (question.querySelector('input:checked')) {
                answered++;
            }
        });

        answeredCount.textContent = answered;

        indicators.forEach(function (indicator, index) {
            const question = questions[index];
            if (question && question.querySelector('input:checked')) {
                indicator.classList.add('answered');
            } else {
                indicator.classList.remove('answered');
            }
        });

        if (indicators[currentQuestion]) {
            indicators[currentQuestion].classList.add('active');
        }
    }

    document.querySelectorAll('.answer-radio').forEach(function (radio) {
        radio.addEventListener('change', function () {
            const option = this.closest('.answer-option');
            const question = this.closest('.quiz-question');

            question.querySelectorAll('.answer-option').forEach(function (item) {
                item.classList.remove('selected');
            });

            option.classList.add('selected');
            updateAnsweredCount();
        });
    });

    nextButton.addEventListener('click', function () {
        if (currentQuestion < totalQuestions - 1) {
            showQuestion(currentQuestion + 1);
        }
    });

    previousButton.addEventListener('click', function () {
        if (currentQuestion > 0) {
            showQuestion(currentQuestion - 1);
        }
    });

    indicators.forEach(function (indicator) {
        indicator.addEventListener('click', function () {
            const index = parseInt(this.dataset.question, 10);
            showQuestion(index);
        });
    });

    const submitModal = document.getElementById('submitModal');
    const cancelSubmit = document.getElementById('cancelSubmit');
    const confirmSubmit = document.getElementById('confirmSubmit');

    function openSubmitModal() {
        submitModal.classList.remove('hidden');
        submitModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeSubmitModal() {
        submitModal.classList.add('hidden');
        submitModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    submitButton.addEventListener('click', function () {
        openSubmitModal();
    });

    cancelSubmit.addEventListener('click', function () {
        closeSubmitModal();
    });

    submitModal.addEventListener('click', function (event) {
        if (event.target === submitModal) {
            closeSubmitModal();
        }
    });

    confirmSubmit.addEventListener('click', function () {
        closeSubmitModal();
        quizForm.submit();
    });

    // ---------------------------------------------------------------
    // Timer — driven by window.QUIZ_REMAINING_SECONDS set server-side.
    // null means the quiz is untimed, so no countdown runs at all.
    // ---------------------------------------------------------------
    const timerElement = document.getElementById('quizTimer');
    let remainingSeconds = window.QUIZ_REMAINING_SECONDS;

    if (typeof remainingSeconds === 'number') {
        function updateTimer() {
            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;

            timerElement.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

            if (remainingSeconds <= 300) {
                timerElement.classList.remove('text-amber-300');
                timerElement.classList.add('text-red-300');
            }

            if (remainingSeconds <= 0) {
                clearInterval(timerInterval);
                // Time's up — submit whatever is currently selected.
                quizForm.submit();
                return;
            }

            remainingSeconds--;
        }

        const timerInterval = setInterval(updateTimer, 1000);
    }

    showQuestion(0);
});