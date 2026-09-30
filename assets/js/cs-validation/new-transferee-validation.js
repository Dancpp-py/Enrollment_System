document.addEventListener("DOMContentLoaded", () => {

    const form = document.querySelector("form");

    if (!form) return;

    form.addEventListener("submit", function (e) {

        let valid = true;
        let firstInvalid = null;

        document.querySelectorAll("input, select, textarea").forEach(field => {

            field.classList.remove("input-error");

            // Skip optional fields
            if (
                [
                    "per_Mname",
                    "per_suffix",
                    "fa_Mname",
                    "fa_suffix",
                    "mo_Mname",
                    "mo_suffix",
                    "gu_Mname",
                    "gu_suffix",
                    "shs",
                    "shs_yr"
                ].includes(field.name)
            ) {
                return;
            }

            // Skip optional TOR upload (remove this if you want it required)
            if (field.name === "up_tor") {
                return;
            }

            // Checkbox
            if (field.type === "checkbox") {

                if (!field.checked) {
                    valid = false;

                    if (!firstInvalid) {
                        firstInvalid = field;
                    }
                }

                return;
            }

            // File upload
            if (field.type === "file") {
                if (!field.required) return;

                if (field.files.length === 0) {

                    field.classList.add("input-error");
                    valid = false;

                    if (!firstInvalid) {
                        firstInvalid = field;
                    }
                }

                return;
            }
            if (field.value.trim() === "") {

                field.classList.add("input-error");
                valid = false;

                if (!firstInvalid) {
                    firstInvalid = field;
                }
            }

        });

        if (!valid) {

            e.preventDefault();

            firstInvalid.scrollIntoView({
                behavior: "smooth",
                block: "center"
            });

            firstInvalid.focus();

        }

    });

    // Remove red border while typing
    document.querySelectorAll("input, select, textarea").forEach(field => {

        field.addEventListener("input", () => {
            field.classList.remove("input-error");
        });

        field.addEventListener("change", () => {
            field.classList.remove("input-error");
        });

    });

});