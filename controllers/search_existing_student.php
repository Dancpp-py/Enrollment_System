<?php
require_once "../config/db.php";
require '../views/includes/helpers.php';


header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit;
}

$studentNumber = trim($_POST['student_number']);

if (empty($studentNumber)) {
    // echo json_encode([
    //     "success" => false,
    //     "message" => "Please enter a student number."
    // ]);
    showError(
        "Error!",
        "Please enter a student number."
    );
}

$sql = "
SELECT
    s.student_id,
    s.first_name,
    s.middle_name,
    s.last_name,

    e.grade_level,
    e.semester,

    st.strand_id,
    st.strand_name

FROM students s

INNER JOIN enrollments e
ON s.student_id = e.student_id

INNER JOIN strands st
ON e.strand_id = st.strand_id

WHERE
    s.student_number = ?
AND
    e.status='Confirmed'

ORDER BY e.enrollment_id DESC
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentNumber);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){
    // echo json_encode([
    //     "success"=>false,
    //     "message"=>"Student not found."
    // ]);
    showError(
        "Error!",
        "Student not found."
    );
         
    exit;
}

$row = $result->fetch_assoc();

$currentGrade = $row['grade_level'];
$currentSemester = $row['semester'];

if($currentGrade=="Grade 11" && $currentSemester=="1st Semester"){

    $nextGrade="Grade 11";
    $nextSemester="2nd Semester";

}
elseif($currentGrade=="Grade 11" && $currentSemester=="2nd Semester"){

    $nextGrade="Grade 12";
    $nextSemester="1st Semester";

}
elseif($currentGrade=="Grade 12" && $currentSemester=="1st Semester"){

    $nextGrade="Grade 12";
    $nextSemester="2nd Semester";

}
else{
    // echo json_encode([
    //     "success"=>false,
    //     "message"=>"Student has already completed Grade 12."
    // ]);
    showError(
        "Error!",
        "Student has already completed Grade 12."
    );

}

$active = $conn->query("
SELECT school_year
FROM school_years
WHERE is_active=1
LIMIT 1
");

$schoolYear = $active->fetch_assoc();


$name = trim(
    $row['first_name']." ".
    $row['middle_name']." ".
    $row['last_name']
);

echo json_encode([

    "success"=>true,

    "student_name"=>$name,

    "current_grade"=>$currentGrade,

    "current_semester"=>$currentSemester,

    "next_grade"=>$nextGrade,

    "next_semester"=>$nextSemester,

    "strand"=>$row['strand_name'],

    "school_year"=>$schoolYear['school_year']

]);

$stmt->close();
$conn->close();