document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchQuery");
    const filterStrand = document.getElementById("filterStrand");
    const filterGrade = document.getElementById("filterGrade");
    const filterType = document.getElementById("filterType");
    const resetBtn = document.getElementById("resetFiltersBtn");
    const statusButtons = document.querySelectorAll(".status-btn");
    const rows = document.querySelectorAll(".enrollment-row");
    const visibleCountEl = document.getElementById("visibleCount");
    const recordCountBadge = document.getElementById("recordCountBadge");

    let currentStatus = "all";

    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();
        const strand = filterStrand.value;
        const grade = filterGrade.value;
        const type = filterType.value;

        let visibleCount = 0;

        rows.forEach((row) => {
            const rowStatus = row.getAttribute("data-status");
            const rowStrand = row.getAttribute("data-strand");
            const rowGrade = row.getAttribute("data-grade");
            const rowType = row.getAttribute("data-type");
            const rowSearch = row.getAttribute("data-search");

            let matchesStatus = (currentStatus === "all" || rowStatus === currentStatus);
            let matchesStrand = (!strand || rowStrand === strand);
            let matchesGrade = (!grade || rowGrade === grade);
            let matchesType = (!type || rowType === type);
            let matchesSearch = (!query || rowSearch.includes(query));

            if (matchesStatus && matchesStrand && matchesGrade && matchesType && matchesSearch) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        if (visibleCountEl) visibleCountEl.textContent = visibleCount;
        if (recordCountBadge) recordCountBadge.textContent = visibleCount + " Records";
    }

    // Status Tab Buttons
    statusButtons.forEach((btn) => {
        btn.addEventListener("click", function () {
            statusButtons.forEach((b) => {
                b.classList.remove("active", "bg-[#0A1931]", "text-white", "shadow-sm");
                b.classList.add("bg-slate-100", "text-slate-700");
            });

            this.classList.add("active", "bg-[#0A1931]", "text-white", "shadow-sm");
            this.classList.remove("bg-slate-100", "text-slate-700");

            currentStatus = this.getAttribute("data-status-filter");
            applyFilters();
        });
    });

    // Inputs & Dropdowns
    if (searchInput) searchInput.addEventListener("input", applyFilters);
    if (filterStrand) filterStrand.addEventListener("change", applyFilters);
    if (filterGrade) filterGrade.addEventListener("change", applyFilters);
    if (filterType) filterType.addEventListener("change", applyFilters);

    // Reset Button
    if (resetBtn) {
        resetBtn.addEventListener("click", function () {
            searchInput.value = "";
            filterStrand.value = "";
            filterGrade.value = "";
            filterType.value = "";

            statusButtons[0].click();
        });
    }
});