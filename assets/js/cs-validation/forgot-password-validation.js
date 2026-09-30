const forgotPasswordForm = document.querySelector("form");

if (forgotPasswordForm) {

    const email = document.getElementById("email");

    function showError(input) {

        input.classList.remove("input-error");
        void input.offsetWidth;
        input.classList.add("input-error");

    }

    function clearError(input) {

        input.classList.remove("input-error");

    }

    function validateEmail() {

        const value = email.value.trim();

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (value === "") {

            showError(email);
            return false;

        }

        if (!emailPattern.test(value)) {

            showError(email);
            return false;

        }

        clearError(email);
        return true;

    }

    email.addEventListener("input", validateEmail);

    forgotPasswordForm.addEventListener("submit", function (e) {

        if (!validateEmail()) {

            e.preventDefault();

        }

    });

}