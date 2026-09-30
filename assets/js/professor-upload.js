document.addEventListener('DOMContentLoaded', function () {
    const materialFile = document.getElementById('material-file');
    const selectedFileName = document.getElementById('selected-file-name');

    if (materialFile && selectedFileName) {
        materialFile.addEventListener('change', function () {
            if (this.files.length > 0) {
                selectedFileName.textContent = this.files[0].name;
                selectedFileName.classList.remove('hidden');
            } else {
                selectedFileName.textContent = '';
                selectedFileName.classList.add('hidden');
            }
        });
    }

    const materialSearch = document.getElementById('material-search');
    const materialRows = document.querySelectorAll('.material-row');
    const noMaterialResults = document.getElementById('no-material-results');

    if (materialSearch) {
        materialSearch.addEventListener('input', function () {
            const searchValue = this.value.toLowerCase().trim();
            let visibleRows = 0;

            materialRows.forEach(function (row) {
                const searchableText = row.dataset.search || row.textContent.toLowerCase();

                if (searchableText.includes(searchValue)) {
                    row.classList.remove('hidden');
                    visibleRows++;
                } else {
                    row.classList.add('hidden');
                }
            });

            if (materialRows.length > 0 && visibleRows === 0) {
                noMaterialResults.classList.remove('hidden');
            } else {
                noMaterialResults.classList.add('hidden');
            }
        });
    }

    const editModal = document.getElementById('edit-material-modal');
    const closeEditModal = document.getElementById('close-edit-modal');
    const cancelEditModal = document.getElementById('cancel-edit-modal');
    const editMaterialId = document.getElementById('edit-material-id');
    const editMaterialTitle = document.getElementById('edit-material-title');
    const editMaterialDescription = document.getElementById('edit-material-description');
    const editCurrentFile = document.getElementById('edit-current-file');
    const editMaterialFile = document.getElementById('edit-material-file');
    const editSelectedFileName = document.getElementById('edit-selected-file-name');
    const editButtons = document.querySelectorAll('.edit-material-button');

    function openEditModal() {
        editModal.classList.remove('hidden');
        editModal.classList.add('flex');
    }

    function closeEditMaterialModal() {
        editModal.classList.add('hidden');
        editModal.classList.remove('flex');
        editMaterialFile.value = '';
        editSelectedFileName.textContent = 'No new file selected';
    }

    editButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            editMaterialId.value = this.dataset.id;
            editMaterialTitle.value = this.dataset.title;
            editMaterialDescription.value = this.dataset.description || '';
            editCurrentFile.textContent = this.dataset.file;
            openEditModal();
        });
    });

    closeEditModal.addEventListener('click', closeEditMaterialModal);
    cancelEditModal.addEventListener('click', closeEditMaterialModal);

    editModal.addEventListener('click', function (event) {
        if (event.target === editModal) {
            closeEditMaterialModal();
        }
    });

    editMaterialFile.addEventListener('change', function () {
        if (this.files.length > 0) {
            editSelectedFileName.textContent = this.files[0].name;
        } else {
            editSelectedFileName.textContent = 'No new file selected';
        }
    });

    const deleteModal = document.getElementById('delete-material-modal');
    const deleteMaterialName = document.getElementById('delete-material-name');
    const deleteMaterialId = document.getElementById('delete-material-id');
    const cancelDelete = document.getElementById('cancel-delete');
    const deleteButtons = document.querySelectorAll('.delete-material-button');

    function openDeleteModal() {
        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
    }

    function closeDeleteModal() {
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex');
    }

    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            deleteMaterialId.value = this.dataset.id;
            deleteMaterialName.textContent = '"' + this.dataset.title + '"';
            openDeleteModal();
        });
    });

    cancelDelete.addEventListener('click', closeDeleteModal);

    deleteModal.addEventListener('click', function (event) {
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });
});