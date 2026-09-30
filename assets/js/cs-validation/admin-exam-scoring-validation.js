const math = document.getElementById("math");
const english = document.getElementById("english");
const filipino = document.getElementById("filipino");
const science = document.getElementById("science");

const rejectBtn = document.getElementById("Reject");
const approveBtn = document.querySelector("button[value='Approve']");

function showError(input) {
    input.classList.remove('input-error');

    input.classList.add(
        'input-error'
    );
}

function clearError(input) {
    input.classList.remove(
        'input-error'
    );
}

function validateScore(input) {

    const value = input.value.trim();

    if (value === "") {
        showError(input);
        return false;
    }

    const score = Number(value);

    if (
        isNaN(score) ||
        score < 0 ||
        score > 100 ||
        !Number.isInteger(score)
    ) {
        showError(input);
        return false;
    }

    clearError(input);
    return true;
}

function validateForm() {

    let valid = true;

    [math, english, filipino, science].forEach(input => {

        if (!validateScore(input)) {
            valid = false;
        }

    });

    return valid;
}

[math, english, filipino, science].forEach(input => {

    input.addEventListener("input", function () {

        validateScore(this);

    });

});

approveBtn.addEventListener("click", function(e){

    if(!validateForm()){

        e.preventDefault();

    }

});

rejectBtn.addEventListener("click", function(e){

    e.preventDefault();

    if(!validateForm()){

        return;

    }

    Swal.fire({

        icon:'warning',
        title:'Reject?',
        text:'Are you sure you want to reject this student?',
        showCancelButton:true,
        confirmButtonText:'Yes',
        cancelButtonText:'No'

    }).then((result)=>{

        if(result.isConfirmed){

            scoringForm.requestSubmit(rejectBtn);

        }

    });

});