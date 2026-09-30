const checkbox = document.getElementById("confirmCheck");
const submitBtn = document.getElementById("submitBtn");

checkbox.addEventListener("change", function () {

    if (this.checked) {

        submitBtn.disabled = false;

        submitBtn.classList.remove(
            "bg-gray-400",
            "cursor-not-allowed"
        );

        submitBtn.classList.add(
            "submit-btn",
            "hover:bg-blue-700",
            "cursor-pointer",
            "shadow-md"
        );

    } else {

        submitBtn.disabled = true;

        submitBtn.classList.remove(
            "submit-btn",
            "hover:bg-blue-700",
            "cursor-pointer",
            "shadow-md"
        );

        submitBtn.classList.add(
            "bg-gray-400",
            "cursor-not-allowed"
        );
    }
});
