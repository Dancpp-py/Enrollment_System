<?php
require_once "../config/db.php";

header("Content-Type: application/json");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // echo json_encode([
    //     "error" => "Invalid enrollment ID."
    // ]);
    showError(
        "Error!",
        "Invalid enrollment ID."
    );
}

$enrollment_id = $_GET['id'];

$sql = "
SELECT
    e.enrollment_id,
    e.status,
    e.grade_level,
    e.semester,

    s.student_type,
    s.student_number,
    s.last_name,
    s.first_name,
    s.middle_name,
    s.suffix,
    s.birth_date,
    s.gender,
    s.address,
    s.contact_number,
    s.email,

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

LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $enrollment_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // echo json_encode([
    //     "error" => "Application not found."
    // ]);
    showError(
        "Error!",
        "Application not found."
    );
}

$row = $result->fetch_assoc();



switch ($row['strand_name']) {

    case "STEM":
    case "ABM":
    case "HUMSS":
    case "GAS":
        $track = "Academic Track";
        break;

    case "TVL":
        $track = "Technical-Vocational-Livelihood (TVL)";
        break;

    default:
        $track = "";
}


$row['birth_date'] = date(
    "F d, Y",
    strtotime($row['birth_date'])
);



$row['track'] = $track;


echo json_encode($row);