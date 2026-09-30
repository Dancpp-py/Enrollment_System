const PASSING_AVERAGE = 85;

const DOCUMENT_BASE_URL = "/enrollment_system/";

const modal = document.getElementById("modal");
const scoringForm = document.getElementById("scoringForm");
const enrollmentIdInput = document.getElementById("enrollment_id");

const scoreInputs = {
    math: document.getElementById("math"),
    english: document.getElementById("english"),
    filipino: document.getElementById("filipino"),
    science: document.getElementById("science"),
};

const passFailBadge = document.getElementById("passFailBadge");
const documentsContainer = document.getElementById("documents");

// const fields = {
//     name: document.getElementById("ex_name"),
//     email: document.getElementById("ex_email"),
//     type: document.getElementById("ex_type"),
//     examNo: document.getElementById("ex_examNo"),
//     yrLvl: document.getElementById("ex_yrLvl"),
//     strand: document.getElementById("ex_strand"),
// };

function openModal(enrollmentId) {
    resetModal();
    enrollmentIdInput.value = enrollmentId;

    modal.classList.remove("hidden");
    modal.classList.add("flex");

    fetch(`../../controllers/get_exam_applicant.php?enrollment_id=${encodeURIComponent(enrollmentId)}`)
        .then((res) => {
            if (!res.ok) {
                throw new Error("Request failed with status " + res.status);
            }
            return res.json();
        })
        .then((data) => {
            if (data.error) {
                throw new Error(data.error);
            }
            populateApplicant(data);
            renderDocuments(data.documents || []);
        })
        .catch((err) => {
            documentsContainer.innerHTML = `<p class="text-red-600">Failed to load applicant details: ${escapeHtml(err.message)}</p>`;
        });
}

function closeModal() {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    resetModal();
}

function resetModal() {
    scoringForm.reset();
    enrollmentIdInput.value = "";

    Object.values(fields).forEach((el) => (el.value = ""));

    documentsContainer.innerHTML = `<p class="text-gray-500">Loading documents...</p>`;

    updatePassFailBadge();
}

function populateApplicant(data) {
    fields.name.value = data.name ?? "";
    fields.email.value = data.email ?? "";
    fields.type.value = data.student_type ?? "";
    fields.examNo.value = data.exam_number ?? "";
    fields.yrLvl.value = data.grade_level ?? "";
    fields.strand.value = data.strand_name ?? "";
}

function renderDocuments(documents) {
    if (!documents.length) {
        documentsContainer.innerHTML = `<p class="text-gray-500">No documents on file.</p>`;
        return;
    }

    documentsContainer.innerHTML = documents
        .map((doc) => {
            const url = DOCUMENT_BASE_URL + String(doc.file_path).replace(/^\/+/, "");
            return `
                <div class="flex items-center justify-between border border-gray-200 rounded-xl p-3">
                    <span class="font-medium">${escapeHtml(doc.document_type)}</span>
                    <a href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer"
                       class="text-[#1D4ED8] hover:underline">
                        View file
                    </a>
                </div>
            `;
        })
        .join("");
}

function updatePassFailBadge() {
    const values = Object.values(scoreInputs).map((el) => el.value.trim());

    const allFilled = values.every((v) => v !== "");
    if (!allFilled) {
        passFailBadge.textContent = "Enter all four scores";
        passFailBadge.className = "bg-gray-100 text-gray-500 rounded-xl p-3 text-center font-semibold";
        return;
    }

    const numbers = values.map((v) => Number(v));
    const isValid = numbers.every((n) => Number.isInteger(n) && n >= 0 && n <= 100);

    if (!isValid) {
        passFailBadge.textContent = "Scores must be whole numbers from 0 to 100";
        passFailBadge.className = "bg-yellow-100 text-yellow-700 rounded-xl p-3 text-center font-semibold";
        return;
    }

    const average = numbers.reduce((sum, n) => sum + n, 0) / numbers.length;
    const roundedAverage = Math.round(average * 100) / 100;
    const passed = average >= PASSING_AVERAGE;

    passFailBadge.textContent = passed
        ? `Passed — average ${roundedAverage}`
        : `Failed — average ${roundedAverage}`;

    passFailBadge.className = passed
        ? "bg-green-100 text-green-700 rounded-xl p-3 text-center font-semibold"
        : "bg-red-100 text-red-700 rounded-xl p-3 text-center font-semibold";
}

Object.values(scoreInputs).forEach((input) => {
    input.addEventListener("input", updatePassFailBadge);
});

// Table search filter
const searchInput = document.getElementById("searchInput");
if (searchInput) {
    searchInput.addEventListener("input", () => {
        const term = searchInput.value.trim().toLowerCase();
        const rows = document.querySelectorAll("table tbody tr");

        rows.forEach((row) => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? "" : "none";
        });
    });
}

function escapeHtml(value) {
    const div = document.createElement("div");
    div.textContent = value ?? "";
    return div.innerHTML;
}

// Close modal when clicking the backdrop (outside the modal content)
modal.addEventListener("click", (e) => {
    if (e.target === modal) {
        closeModal();
    }
});