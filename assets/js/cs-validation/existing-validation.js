document.querySelector("form").addEventListener("submit", function (e) {

    let valid = true;
    let firstInvalid = null;

    const studentInput = document.querySelector("[name='student_number']");
    const confirmCheck = document.getElementById("confirmCheck");

    studentInput.classList.remove("input-error");
    confirmCheck.classList.remove("checkbox-error");

    // Student number required
    if (studentInput.value.trim() === "") {

        studentInput.classList.add("input-error");

        valid = false;
        firstInvalid = studentInput;
    }

    // Must search first (name should be populated)
    if (document.getElementById("student_name").value.trim() === "") {

        studentInput.classList.add("input-error");

        valid = false;

        if (!firstInvalid) {
            firstInvalid = studentInput;
        }
    }

    // Checkbox required
    if (!confirmCheck.checked) {

        confirmCheck.classList.add("checkbox-error");

        valid = false;

        if (!firstInvalid) {
            firstInvalid = confirmCheck;
        }
    }

    if (!valid) {

        e.preventDefault();

        firstInvalid.scrollIntoView({
            behavior: "smooth",
            block: "center"
        });

        firstInvalid.focus();

    }

    

});



