document.addEventListener('DOMContentLoaded', function () {

    const ENDPOINT = '/enrollment_system/controllers/professor_subjects.php';

    // ---------------------------------------------------------------
    // Search filter (client-side, over the rows already rendered)
    // ---------------------------------------------------------------
    const searchInput = document.getElementById('subject-search');
    const rows = Array.from(document.querySelectorAll('.subject-row'));
    const noResultsRow = document.getElementById('noSearchResults');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = searchInput.value.trim().toLowerCase();
            let visible = 0;

            rows.forEach(function (row) {
                const haystack = row.dataset.search || '';
                const match = term === '' || haystack.includes(term);

                row.classList.toggle('hidden', !match);
                if (match) visible++;
            });

            if (noResultsRow) {
                noResultsRow.classList.toggle('hidden', visible > 0 || rows.length === 0);
            }
        });
    }

    // ---------------------------------------------------------------
    // Student modal
    // ---------------------------------------------------------------
    const modal          = document.getElementById('studentsModal');
    const titleEl        = document.getElementById('modalSubjectTitle');
    const subtitleEl     = document.getElementById('modalSectionSubtitle');
    const loadingEl      = document.getElementById('studentsLoading');
    const errorEl        = document.getElementById('studentsError');
    const errorTextEl    = document.getElementById('studentsErrorText');
    const emptyEl        = document.getElementById('studentsEmpty');
    const tableWrapperEl = document.getElementById('studentsTableWrapper');
    const tableBodyEl    = document.getElementById('studentsTableBody');
    const countEl        = document.getElementById('studentsCount');

    function showOnly(element) {
        [loadingEl, errorEl, emptyEl, tableWrapperEl].forEach(function (el) {
            if (el) el.classList.add('hidden');
        });
        if (element) element.classList.remove('hidden');
    }

    function openModal() {
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
    }

    function renderStudents(students) {
        if (!tableBodyEl) return;

        tableBodyEl.innerHTML = students.map(function (student, index) {
            return `
                <tr class="transition hover:bg-slate-50">
                    <td class="px-6 py-3 text-sm text-slate-400">${index + 1}</td>
                    <td class="px-6 py-3 text-sm font-semibold text-slate-800">${escapeHtml(student.student_number)}</td>
                    <td class="px-6 py-3 text-sm text-slate-700">${escapeHtml(student.full_name)}</td>
                    <td class="px-6 py-3 text-sm text-slate-700">${escapeHtml(student.gender)}</td>
                    <td class="px-6 py-3 text-sm text-slate-700">${escapeHtml(student.contact_number)}</td>
                    <td class="px-6 py-3 text-sm text-slate-700">${escapeHtml(student.email)}</td>
                </tr>
            `;
        }).join('');
    }

    document.querySelectorAll('.view-students-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const scheduleId = button.dataset.scheduleId;

            // Show what we already know immediately, so the modal
            // header isn't blank while the fetch is in flight.
            if (titleEl)    titleEl.textContent = button.dataset.subject || 'Students';
            if (subtitleEl) subtitleEl.textContent = button.dataset.section || '';
            if (countEl)    countEl.textContent = '';

            showOnly(loadingEl);
            openModal();

            fetch(`${ENDPOINT}?action=get_students&schedule_id=${encodeURIComponent(scheduleId)}`, {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function (result) {
                    const data = result.data;

                    if (!result.ok || !data.success) {
                        if (errorTextEl) {
                            errorTextEl.textContent = data.message || 'Unable to load the student list.';
                        }
                        showOnly(errorEl);
                        return;
                    }

                    if (titleEl && data.subject)    titleEl.textContent = data.subject;
                    if (subtitleEl && data.section) subtitleEl.textContent = data.section;

                    const students = data.students || [];

                    if (students.length === 0) {
                        if (countEl) countEl.textContent = '0 students';
                        showOnly(emptyEl);
                        return;
                    }

                    renderStudents(students);

                    if (countEl) {
                        countEl.textContent = students.length +
                            (students.length === 1 ? ' student' : ' students');
                    }

                    showOnly(tableWrapperEl);
                })
                .catch(function () {
                    if (errorTextEl) {
                        errorTextEl.textContent = 'Could not reach the server. Please try again.';
                    }
                    showOnly(errorEl);
                });
        });
    });

    // Close handlers: button, footer button, backdrop click, Escape key
    const closeBtn = document.getElementById('closeStudentsModal');
    const closeBtnFooter = document.getElementById('closeStudentsModalFooter');

    if (closeBtn)       closeBtn.addEventListener('click', closeModal);
    if (closeBtnFooter) closeBtnFooter.addEventListener('click', closeModal);

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});