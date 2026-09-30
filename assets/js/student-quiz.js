document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('quizSearch');
    const filterButtons = document.querySelectorAll('.quiz-filter-btn');
    const quizItems = document.querySelectorAll('.quiz-item');
    const noResults = document.getElementById('noQuizResults');

    let currentFilter = 'all';

    function filterQuizzes() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        quizItems.forEach(function (quiz) {
            const status = quiz.dataset.status || '';
            const searchText = quiz.dataset.search || '';

            const matchesFilter = currentFilter === 'all' || status === currentFilter;
            const matchesSearch = searchText.includes(searchTerm);

            if (matchesFilter && matchesSearch) {
                quiz.classList.remove('hidden');
                visibleCount++;
            } else {
                quiz.classList.add('hidden');
            }
        });

        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }

    searchInput.addEventListener('input', filterQuizzes);

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            currentFilter = this.dataset.filter;

            filterButtons.forEach(function (btn) {
                btn.classList.remove('bg-[#0A1931]', 'text-white');
                btn.classList.add('border', 'border-slate-200', 'bg-white', 'text-slate-600');
            });

            this.classList.remove('border', 'border-slate-200', 'bg-white', 'text-slate-600');
            this.classList.add('bg-[#0A1931]', 'text-white');

            filterQuizzes();
        });
    });

    const modal = document.getElementById('quizModal');
    const closeModalButton = document.getElementById('closeQuizModal');
    const cancelModalButton = document.getElementById('cancelQuizModal');

    const modalTitle = document.getElementById('modalQuizTitle');
    const modalSubject = document.getElementById('modalQuizSubject');
    const modalItems = document.getElementById('modalQuizItems');
    const modalDuration = document.getElementById('modalQuizDuration');
    const modalSchedule = document.getElementById('modalQuizSchedule');
    const modalTopics = document.getElementById('modalQuizTopics');
    const modalStartLink = document.getElementById('modalStartQuizLink');

    function openQuizModal(button) {
        modalTitle.textContent = button.dataset.title || 'Quiz';
        modalSubject.textContent = button.dataset.subject || 'Subject';
        modalItems.textContent = button.dataset.items || '—';
        modalDuration.textContent = button.dataset.duration || '—';
        modalSchedule.textContent = button.dataset.schedule || '—';
        modalTopics.textContent = button.dataset.topics || '—';

        // Wire the "Start Quiz" link to the actual quiz this card represents.
        const quizId = button.dataset.quizId;
        if (quizId && modalStartLink) {
            modalStartLink.href = 'student-quiz-v2.php?quiz_id=' + encodeURIComponent(quizId);
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeQuizModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('.open-guidelines-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            openQuizModal(this);
        });
    });

    closeModalButton.addEventListener('click', closeQuizModal);
    cancelModalButton.addEventListener('click', closeQuizModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeQuizModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeQuizModal();
        }
    });
});