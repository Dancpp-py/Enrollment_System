const loginForm = document.querySelector("form");

if (loginForm) {

    const username = document.getElementById("username");
    const password = document.getElementById("password");

    function showError(input) {

        input.classList.remove("input-error");
        void input.offsetWidth;
        input.classList.add("input-error");

    }

    function clearError(input) {

        input.classList.remove("input-error");

    }

    function validateUsername() {

        if (username.value.trim() === "") {

            showError(username);
            return false;

        }

        clearError(username);
        return true;

    }

    function validatePassword() {

        if (password.value.trim() === "") {

            showError(password);
            return false;

        }

        clearError(password);
        return true;

    }

    username.addEventListener("input", validateUsername);
    password.addEventListener("input", validatePassword);

    loginForm.addEventListener("submit", function (e) {

        let valid = true;

        if (!validateUsername()) valid = false;
        if (!validatePassword()) valid = false;

        if (!valid) {

            e.preventDefault();

        }

    });

}