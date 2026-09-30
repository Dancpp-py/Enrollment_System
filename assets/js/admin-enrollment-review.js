function sh(id, v) {
    const e = document.getElementById(id);
    e.classList.toggle("hidden", !v);
    e.classList.toggle("flex", v);
}

function closeReview() {
    sh("review", false);
}

async function openReview(enrollmentId) {

    sh("review", true);

    document.getElementById("enrollment_id").value = enrollmentId;

    try {

        const response = await fetch(
            `/enrollment_system/controllers/get_enrollment_review_application.php?enrollment_id=${enrollmentId}`
        );

        const data = await response.json();

        // ---------------------------
        // Applicant Information
        // ---------------------------

        document.getElementById("rv_name").value =
            data.applicant.last_name + ", " + data.applicant.first_name;

        document.getElementById("rv_type").value =
            data.applicant.student_type;

        document.getElementById("rv_grade").value =
            data.applicant.grade_level;

        document.getElementById("rv_strand").value =
            data.applicant.strand_name;

        document.getElementById("rv_email").value =
            data.applicant.email;

        document.getElementById("rv_examno").value =
            data.applicant.exam_number;

        document.getElementById("rv_status").value =
            data.exam.exam_status;

        // ---------------------------
        // Scores
        // ---------------------------

        document.getElementById("rv_scores").innerHTML = `
            <tr>
                <td class="py-2">Math</td>
                <td class="text-right">${data.exam.math_score}</td>
            </tr>

            <tr>
                <td class="py-2">English</td>
                <td class="text-right">${data.exam.english_score}</td>
            </tr>

            <tr>
                <td class="py-2">Filipino</td>
                <td class="text-right">${data.exam.filipino_score}</td>
            </tr>

            <tr>
                <td class="py-2">Science</td>
                <td class="text-right">${data.exam.science_score}</td>
            </tr>

            <tr class="border-t font-bold">
                <td class="pt-3">Average</td>
                <td class="text-right pt-3">${data.exam.average_score}</td>
            </tr>
        `;

        // ---------------------------
        // Documents
        // ---------------------------

        const docs = document.getElementById("rv_documents");

        docs.innerHTML = "";

        if (data.documents.length === 0) {

            docs.innerHTML = `
                <p class="text-gray-500">
                    No uploaded documents.
                </p>
            `;

        } else {

            data.documents.forEach(doc => {

                docs.innerHTML += `
                    <div class="flex justify-between items-center border border-gray-300 rounded-lg p-3">
                        <span>${doc.type}</span>

                        <button
                            type="button"
                            onclick="openDocumentModal('${doc.url}', '${doc.type}')"
                            class="text-blue-600 hover:underline cursor-pointer"
                        >
                            View
                        </button>
                    </div>
                `;

            });

        }

    } catch (err) {

        console.error(err);

        alert("Failed to load applicant information.");

        closeReview();

    }

}



// ---------------------------
// Search
// ---------------------------

document.getElementById("searchInput").addEventListener("keyup", function () {

    const value = this.value.toLowerCase();

    document.querySelectorAll("tbody tr").forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value)
            ? ""
            : "none";

    });

});

// ---------------------------
// Filter
// ---------------------------

document.querySelectorAll("[data-filter]").forEach(button => {

    button.addEventListener("click", () => {

        const filter = button.dataset.filter;

        document.querySelectorAll("tbody tr").forEach(row => {

            if (filter === "All") {

                row.style.display = "";

            } else {

                row.style.display =
                    row.dataset.status === filter ? "" : "none";

            }

        });

    });

});