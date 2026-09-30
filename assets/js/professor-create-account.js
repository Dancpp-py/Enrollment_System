    const createProfessorModal = document.getElementById('createProfessorModal');
    const openCreateProfessorModal = document.getElementById('openCreateProfessorModal');
    const closeCreateProfessorModal = document.getElementById('closeCreateProfessorModal');
    const cancelCreateProfessor = document.getElementById('cancelCreateProfessor');
    const createProfessorBackdrop = document.getElementById('createProfessorBackdrop');

    function openProfessorModal() {
        createProfessorModal.classList.remove('hidden');
        createProfessorModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function closeProfessorModal() {
        createProfessorModal.classList.add('hidden');
        createProfessorModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }

    openCreateProfessorModal.addEventListener('click', openProfessorModal);
    closeCreateProfessorModal.addEventListener('click', closeProfessorModal);
    cancelCreateProfessor.addEventListener('click', closeProfessorModal);
    createProfessorBackdrop.addEventListener('click', closeProfessorModal);

    document.addEventListener('keydown', function (event) {
        if (
            event.key === 'Escape' &&
            !createProfessorModal.classList.contains('hidden')
        ) {
            closeProfessorModal();
        }
    });

    const professorSearch = document.getElementById('professorSearch');
    const professorRows = document.querySelectorAll('.professor-row');

    professorSearch.addEventListener('input', function () {
        const searchValue = this.value.toLowerCase().trim();

        professorRows.forEach(function (row) {
            const rowText = row.textContent.toLowerCase();

            if (rowText.includes(searchValue)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    });

    function confirmAccountStatus(activate, professorName) {
        if (activate) {
            return confirm('Activate the account of ' + professorName + '?');
        }

        return confirm('Disable the account of ' + professorName + '?');
    }