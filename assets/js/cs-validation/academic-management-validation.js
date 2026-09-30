// =======================
// CREATE SECTION FORM
// =======================

const createSectionForm = document.querySelector(
    "#sectionModal form"
);

if (createSectionForm) {

    const sectionName = createSectionForm.querySelector("[name='section_name']");
    const gradeLevel = createSectionForm.querySelector("[name='grade_level']");
    const strand = createSectionForm.querySelector("[name='strand_id']");
    const capacity = createSectionForm.querySelector("[name='capacity']");

    function showError(input) {
        input.classList.remove("input-error");
        void input.offsetWidth;
        input.classList.add("input-error");
    }

    function clearError(input) {
        input.classList.remove("input-error");
    }

    function validateSectionName() {

        if (sectionName.value.trim() === "") {
            showError(sectionName);
            return false;
        }

        clearError(sectionName);
        return true;
    }

    function validateGrade() {

        if (gradeLevel.value === "") {
            showError(gradeLevel);
            return false;
        }

        clearError(gradeLevel);
        return true;
    }

    function validateStrand() {

        if (strand.value === "") {
            showError(strand);
            return false;
        }

        clearError(strand);
        return true;
    }

    function validateCapacity() {

        const value = Number(capacity.value);

        if (
            capacity.value.trim() === "" ||
            isNaN(value) ||
            value < 1 ||
            value > 100
        ) {

            showError(capacity);
            return false;

        }

        clearError(capacity);
        return true;
    }

    sectionName.addEventListener("input", validateSectionName);
    gradeLevel.addEventListener("change", validateGrade);
    strand.addEventListener("change", validateStrand);
    capacity.addEventListener("input", validateCapacity);

    createSectionForm.addEventListener("submit", function (e) {

        let valid = true;

        if (!validateSectionName()) valid = false;
        if (!validateGrade()) valid = false;
        if (!validateStrand()) valid = false;
        if (!validateCapacity()) valid = false;

        if (!valid) {
            e.preventDefault();
        }

    });

}


const scheduleForm = document.querySelector(
    "#scheduleModal form"
);

if (scheduleForm) {

    const professor = scheduleForm.querySelector("[name='professor_id']");
    const startTime = scheduleForm.querySelector("[name='start_time']");
    const endTime = scheduleForm.querySelector("[name='end_time']");
    const days = scheduleForm.querySelectorAll(".sched-day-checkbox");

    function showError(input) {
        input.classList.remove("input-error");
        void input.offsetWidth;
        input.classList.add("input-error");
    }

    function clearError(input) {
        input.classList.remove("input-error");
    }

    function validateProfessor() {

        if (professor.value === "") {
            showError(professor);
            return false;
        }

        clearError(professor);
        return true;
    }

    function validateStart() {

        if (startTime.value === "") {
            showError(startTime);
            return false;
        }

        clearError(startTime);
        return true;
    }

    function validateEnd() {

        if (endTime.value === "") {
            showError(endTime);
            return false;
        }

        clearError(endTime);
        return true;
    }

    function validateDays() {

        const checked = [...days].some(day => day.checked);

        const wrapper = document.getElementById("sched_day");

        if (!checked) {

            wrapper.classList.remove("input-error");
            void wrapper.offsetWidth;
            wrapper.classList.add("input-error");

            return false;
        }

        wrapper.classList.remove("input-error");

        return true;
    }

    professor.addEventListener("change", validateProfessor);
    startTime.addEventListener("input", validateStart);
    endTime.addEventListener("input", validateEnd);

    days.forEach(day => {

        day.addEventListener("change", validateDays);

    });

    scheduleForm.addEventListener("submit", function (e) {

        let valid = true;

        if (!validateProfessor()) valid = false;
        if (!validateStart()) valid = false;
        if (!validateEnd()) valid = false;
        if (!validateDays()) valid = false;

        if (!valid) {

            e.preventDefault();

        }

    });

}