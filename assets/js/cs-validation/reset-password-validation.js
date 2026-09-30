const resetForm = document.querySelector("form");

if (resetForm) {

    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");

    function showError(input) {

        input.classList.remove("input-error");
        void input.offsetWidth;
        input.classList.add("input-error");

    }

    function clearError(input) {

        input.classList.remove("input-error");

    }

    function validatePassword() {

        if (password.value.trim() === "") {

            showError(password);
            return false;

        }

        if (password.value.length < 8) {

            showError(password);
            return false;

        }

        clearError(password);
        return true;

    }

    function validateConfirmPassword() {

        if (confirmPassword.value.trim() === "") {

            showError(confirmPassword);
            return false;

        }

        if (password.value !== confirmPassword.value) {

            showError(confirmPassword);
            return false;

        }

        clearError(confirmPassword);
        return true;

    }

    password.addEventListener("input", function () {

        validatePassword();

        if (confirmPassword.value !== "") {

            validateConfirmPassword();

        }

    });

    confirmPassword.addEventListener("input", validateConfirmPassword);

    resetForm.addEventListener("submit", function (e) {

        let valid = true;

        if (!validatePassword()) valid = false;
        if (!validateConfirmPassword()) valid = false;

        if (!valid) {

            e.preventDefault();

        }

    });

}