document.addEventListener("DOMContentLoaded", () => {

    const modal = document.getElementById("applicationModal");
    const closeModal = document.getElementById("closeModal");


    document.querySelectorAll(".viewBtn").forEach(button => {

        button.addEventListener("click", () => {

            const enrollmentID = button.dataset.id;

            fetch("../../controllers/get_enrollment_details.php?id=" + enrollmentID)

                .then(response => response.json())

                .then(data => {

                    if (data.error) {
                        alert(data.error);
                        return;
                    }


                    document.getElementById("enrollment_id").value = data.enrollment_id;

                    document.getElementById("modal_status").textContent = data.status;


                    document.getElementById("student_type").value = data.student_type;
                    document.getElementById("last_name").value = data.last_name;
                    document.getElementById("first_name").value = data.first_name;
                    document.getElementById("middle_name").value = data.middle_name;
                    document.getElementById("suffix").value = data.suffix;
                    document.getElementById("birth_date").value = data.birth_date;
                    document.getElementById("gender").value = data.gender;
                    document.getElementById("address").value = data.address;
                    document.getElementById("contact_number").value = data.contact_number;
                    document.getElementById("email").value = data.email;



                    document.getElementById("modal_track").value = data.track;
                    document.getElementById("modal_strand").value = data.strand_name;
                    document.getElementById("modal_grade_level").value = data.grade_level;
                    document.getElementById("modal_semester").value = data.semester;
                    document.getElementById("modal_school_year").value = data.school_year;
                    loadDocuments(enrollmentID);


                    modal.classList.remove("hidden");
                    modal.classList.add("flex");

                })

                .catch(error => {

                    console.error(error);
                    alert("Unable to load application.");

                });

        });

    });



    closeModal.addEventListener("click", () => {

        modal.classList.remove("flex");
        modal.classList.add("hidden");

    });



    modal.addEventListener("click", (e) => {

        if (e.target === modal) {

            modal.classList.remove("flex");
            modal.classList.add("hidden");

        }

    });

});

function loadDocuments(enrollmentID) {

    fetch("../../controllers/get_documents.php?id=" + enrollmentID)

    .then(response => response.json())

    .then(documents => {

        const container = document.getElementById("documentsContainer");

        container.innerHTML = "";

        if (documents.length === 0) {

            container.innerHTML = `
                <p class="text-gray-500">
                    No uploaded documents found.
                </p>
            `;

            return;
        }

        documents.forEach(doc => {

            container.innerHTML += `
                <div class="flex items-center justify-between border-b border-gray-200 pb-3">

                    <div>
                        <label class="block text-sm text-gray-500">
                            ${doc.document_type}
                        </label>
                    </div>

                    <a
                        href="../../${doc.file_path}"
                        target="_blank"
                        class="text-blue-700 font-semibold hover:text-blue-900"
                    >
                        View
                    </a>

                </div>
            `;

        });

    })

    .catch(error => {

        console.error(error);

    });

}