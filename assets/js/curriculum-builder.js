document.addEventListener('DOMContentLoaded', function () {
    const filterForm  = document.getElementById('curriculumFilterForm');
    const strandSel   = document.getElementById('strand_id');
    const gradeSel    = document.getElementById('grade_level');
    const semesterSel = document.getElementById('semester');

    // --- Auto-submit the filter form once all three selects have a value ---
    function maybeSubmitCurriculumFilter() {
        if (!filterForm || !strandSel || !gradeSel || !semesterSel) return;

        const strand   = strandSel.value;
        const grade    = gradeSel.value;
        const semester = semesterSel.value;

        if (strand && grade && semester) {
            filterForm.submit();
        }
    }

    if (strandSel)   strandSel.addEventListener('change', maybeSubmitCurriculumFilter);
    if (gradeSel)    gradeSel.addEventListener('change', maybeSubmitCurriculumFilter);
    if (semesterSel) semesterSel.addEventListener('change', maybeSubmitCurriculumFilter);

    // --- Assign Subjects form: guard against submitting with none checked, ---
    // --- and disable the button after submit to avoid duplicate clicks.    ---
    const assignForm = document.getElementById('assignSubjectsForm');
    const assignBtn  = document.getElementById('assignSubjectsBtn');

    if (assignForm) {
        assignForm.addEventListener('submit', function (e) {
            const checked = assignForm.querySelectorAll('.subject-checkbox:checked');

            if (checked.length === 0) {
                e.preventDefault();
                alert('Please select at least one subject to assign.');
                return;
            }

            if (assignBtn) {
                assignBtn.disabled = true;
                assignBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
    }
});