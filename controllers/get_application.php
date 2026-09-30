<?php

require_once "../config/db.php";

if (!isset($_GET['id'])) {
    // throw new Exception("Enrollment ID is required.");
    showError(
        "Error!",
        "Enrollment ID is required."
    );

}

$enrollment_id = (int) $_GET['id'];

$sql = "
    SELECT
        e.*,
        s.student_number,
        s.exam_number,
        s.student_type,
        s.last_name,
        s.first_name,
        s.middle_name,
        s.suffix,
        s.birth_date,
        s.gender,
        s.contact_number,
        s.email,
        s.address,
        st.strand_name,
        sy.school_year
    FROM enrollments e
    INNER JOIN students s
        ON e.student_id = s.student_id
    INNER JOIN strands st
        ON e.strand_id = st.strand_id
    INNER JOIN school_years sy
        ON e.school_year_id = sy.school_year_id
    WHERE e.enrollment_id = ?
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    throw new Exception("Failed to prepare application query.");
}

mysqli_stmt_bind_param($stmt, "i", $enrollment_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$application = mysqli_fetch_assoc($result);

if (!$application) {
    throw new Exception("Application not found.");
}

$sql = "
    SELECT
        document_type,
        file_path
    FROM enrollment_documents
    WHERE enrollment_id = ?
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    throw new Exception("Failed to prepare document query.");
}

mysqli_stmt_bind_param($stmt, "i", $enrollment_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$documents = [];

while ($row = mysqli_fetch_assoc($result)) {
    $documents[] = $row;
}

$application['documents'] = $documents;

return $application;