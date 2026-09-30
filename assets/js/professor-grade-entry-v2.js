document.addEventListener('DOMContentLoaded', function () {

    const modal          = document.getElementById('grade-modal');
    const closeBtn       = document.getElementById('close-grade-modal');
    const editBtn        = document.getElementById('edit-grade-button');
    const midtermInput   = document.getElementById('midterm-grade');
    const finalsInput    = document.getElementById('finals-grade');
    const averageDisplay = document.getElementById('average-grade');

    const nameField       = document.getElementById('modal-student-name');
    const numberField     = document.getElementById('modal-student-number');
    const enrollmentField = document.getElementById('modal-enrollment-id');

    // ---- Open modal (one handler for every row) ----
    document.querySelectorAll('.open-grade-modal').forEach(function (btn) {
        btn.addEventListener('click', function () {
            enrollmentField.value = btn.dataset.enrollmentId;
            nameField.textContent = btn.dataset.studentName;
            numberField.textContent = btn.dataset.studentNumber || '—';

            midtermInput.value = btn.dataset.midterm || '';
            finalsInput.value  = btn.dataset.finals || '';

            // Always reopen in read-only mode; the teacher clicks
            // "Edit Grades" to unlock the fields.
            setEditable(false);
            updateAverage();

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    // ---- Close modal ----
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // ---- Toggle edit mode ----
    function setEditable(editable) {
        midtermInput.disabled = !editable;
        finalsInput.disabled  = !editable;

        [midtermInput, finalsInput].forEach(function (input) {
            input.classList.toggle('bg-slate-100', !editable);
            input.classList.toggle('bg-white', editable);
        });
    }

    if (editBtn) {
        editBtn.addEventListener('click', function () {
            setEditable(true);
            midtermInput.focus();
        });
    }

    // ---- Live average ----
    function updateAverage() {
        const midterm = parseFloat(midtermInput.value);
        const finals  = parseFloat(finalsInput.value);

        if (!isNaN(midterm) && !isNaN(finals)) {
            averageDisplay.textContent = ((midterm + finals) / 2).toFixed(2);
        } else {
            averageDisplay.textContent = '—';
        }
    }

    midtermInput.addEventListener('input', updateAverage);
    finalsInput.addEventListener('input', updateAverage);

    // ---- Student search ----
    const searchInput = document.getElementById('student-search');
    const noResults   = document.getElementById('no-student-results');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = searchInput.value.toLowerCase().trim();
            let visible = 0;

            document.querySelectorAll('.student-row').forEach(function (row) {
                const match = row.textContent.toLowerCase().includes(term);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            noResults.classList.toggle('hidden', visible > 0);
        });
    }
});