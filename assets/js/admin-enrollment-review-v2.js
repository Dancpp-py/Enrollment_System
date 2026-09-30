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
                        <button
                            type="button"
                            onclick="openDocumentModal('${doc.url}','${doc.type}')"
                            class="text-blue-600 cursor-pointer hover:underline"
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


// ===================================================
// DOCUMENT MODAL
// ===================================================

function openDocumentModal(fileUrl, title) {

    const modal = document.getElementById("documentModal");
    const container = document.getElementById("documentModalContainer");
    const body = document.getElementById("documentModalBody");
    const modalTitle = document.getElementById("documentModalTitle");

    modalTitle.textContent = title;
    body.innerHTML = "";

    const extension = fileUrl.split(".").pop().toLowerCase();

    // IMAGE
    if (["jpg", "jpeg", "png", "gif", "webp"].includes(extension)) {

        body.innerHTML = `
            <img
                src="${fileUrl}"
                alt="${title}"
                class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl"
            >
        `;

    }

    // PDF
    else if (extension === "pdf") {

        body.innerHTML = `
            <iframe
                src="${fileUrl}"
                class="w-full h-full rounded-xl bg-white"
            ></iframe>
        `;

    }

    // OTHER FILES
    else {

        body.innerHTML = `
            <div class="text-center space-y-4">

                <p class="text-gray-600">
                    Preview is not available for this file type.
                </p>

                <a
                    href="${fileUrl}"
                    target="_blank"
                    class="inline-block px-6 py-3 rounded-xl bg-[#041B4A] text-white hover:bg-blue-700 transition">

                    Open Document

                </a>

            </div>
        `;

    }

    modal.classList.remove("hidden");
    modal.classList.add("flex");

    requestAnimationFrame(() => {

        container.classList.remove("scale-95", "opacity-0");

        container.classList.add("scale-100", "opacity-100");

    });

}


function closeDocumentModal() {

    const modal = document.getElementById("documentModal");
    const container = document.getElementById("documentModalContainer");
    const body = document.getElementById("documentModalBody");

    container.classList.remove("scale-100", "opacity-100");

    container.classList.add("scale-95", "opacity-0");

    setTimeout(() => {

        body.innerHTML = "";

        modal.classList.remove("flex");

        modal.classList.add("hidden");

    }, 250);

}


// ESC KEY CLOSES MODAL
document.addEventListener("keydown", function (e) {

    if (e.key === "Escape") {

        closeDocumentModal();

    }

});