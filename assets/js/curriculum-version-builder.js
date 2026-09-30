document.addEventListener("DOMContentLoaded", function () {

    /* =====================================================
       ELEMENTS
    ====================================================== */

    const gradeButtons =
        document.querySelectorAll(".grade-filter");

    const semesterButtons =
        document.querySelectorAll(".semester-filter");

    const rows =
        document.querySelectorAll(".curriculum-row");

    const emptyState =
        document.getElementById("emptyState");

    const duplicateBtn =
        document.getElementById("duplicateBtn");

    const saveBtn =
        document.getElementById("saveCurriculumBtn");


    /* =====================================================
       CURRENT FILTER
    ====================================================== */

    let selectedGrade = "Grade 11";

    let selectedSemester = "1st Semester";


    /* =====================================================
       FILTER FUNCTION
    ====================================================== */

    function applyFilters() {

        let visibleRows = 0;

        rows.forEach(function (row) {

            const rowGrade =
                row.dataset.grade;

            const rowSemester =
                row.dataset.semester;


            const matchesGrade =
                rowGrade === selectedGrade;

            const matchesSemester =
                rowSemester === selectedSemester;


            if (matchesGrade && matchesSemester) {

                row.classList.remove("hidden");

                visibleRows++;

            } else {

                row.classList.add("hidden");

            }

        });


        if (emptyState) {

            if (visibleRows === 0) {

                emptyState.classList.remove("hidden");

            } else {

                emptyState.classList.add("hidden");

            }

        }

    }


    /* =====================================================
       GRADE FILTER
    ====================================================== */

    gradeButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            gradeButtons.forEach(function (item) {

                item.classList.remove("bg-[#0B1F4D]");
                item.classList.remove("text-white");
                item.classList.remove("border-[#0B1F4D]");

                item.classList.add("bg-white");
                item.classList.add("text-slate-700");
                item.classList.add("border-slate-300");

            });


            button.classList.remove("bg-white");
            button.classList.remove("text-slate-700");
            button.classList.remove("border-slate-300");

            button.classList.add("bg-[#0B1F4D]");
            button.classList.add("text-white");
            button.classList.add("border-[#0B1F4D]");


            selectedGrade =
                button.dataset.grade;


            applyFilters();

        });

    });


    /* =====================================================
       SEMESTER FILTER
    ====================================================== */

    semesterButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            semesterButtons.forEach(function (item) {

                item.classList.remove("bg-[#0B1F4D]");
                item.classList.remove("text-white");
                item.classList.remove("border-[#0B1F4D]");

                item.classList.add("bg-white");
                item.classList.add("text-slate-700");
                item.classList.add("border-slate-300");

            });


            button.classList.remove("bg-white");
            button.classList.remove("text-slate-700");
            button.classList.remove("border-slate-300");

            button.classList.add("bg-[#0B1F4D]");
            button.classList.add("text-white");
            button.classList.add("border-[#0B1F4D]");


            selectedSemester =
                button.dataset.semester;


            applyFilters();

        });

    });


    /* =====================================================
       CORE CHECKBOX
       Core = All Strands
    ====================================================== */

    rows.forEach(function (row) {

        const coreCheckbox =
            row.querySelector(".core-check");

        const strandCheckboxes =
            row.querySelectorAll(".strand-check");


        if (!coreCheckbox || !strandCheckboxes.length) {
            return;
        }


        coreCheckbox.addEventListener("change", function () {

            strandCheckboxes.forEach(function (checkbox) {

                checkbox.checked =
                    coreCheckbox.checked;

            });

        });

    });


    /* =====================================================
       DUPLICATE
    ====================================================== */

    if (duplicateBtn) {

        duplicateBtn.addEventListener("click", function () {

            const duplicateSelect =
                document.getElementById("duplicate_from");


            if (!duplicateSelect) {
                return;
            }


            const selectedVersion =
                duplicateSelect.value;


            if (!selectedVersion) {

                if (typeof Swal !== "undefined") {

                    Swal.fire({

                        icon: "warning",

                        title: "Select Curriculum",

                        text: "Please select a curriculum version to duplicate.",

                        confirmButtonColor: "#0B1F4D"

                    });

                } else {

                    alert(
                        "Please select a curriculum version to duplicate."
                    );

                }

                return;
            }


            /*
             * Backend duplication logic will be connected here.
             */

            console.log(
                "Duplicate curriculum:",
                selectedVersion
            );

        });

    }


    /* =====================================================
       SAVE CURRICULUM VERSION
    ====================================================== */

    if (saveBtn) {

        saveBtn.addEventListener("click", function () {

            const versionInput =
                document.getElementById("curriculum_version");


            if (!versionInput) {
                return;
            }


            const versionName =
                versionInput.value.trim();


            if (!versionName) {

                if (typeof Swal !== "undefined") {

                    Swal.fire({

                        icon: "warning",

                        title: "Missing Version Name",

                        text: "Please enter a curriculum version name.",

                        confirmButtonColor: "#0B1F4D"

                    });

                } else {

                    alert(
                        "Please enter a curriculum version name."
                    );

                }

                return;
            }


            /*
             * Backend save logic will be connected here.
             */

            console.log(
                "Save curriculum version:",
                versionName
            );

        });

    }


    /* =====================================================
       INITIAL FILTER
    ====================================================== */

    applyFilters();

});