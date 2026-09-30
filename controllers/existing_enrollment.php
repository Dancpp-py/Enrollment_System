<?php

require_once "../config/db.php";
require '../views/includes/helpers.php';

if($_SERVER["REQUEST_METHOD"]!="POST"){

    header("Location: ../views/student/existing-student-form");
    exit;

}

$studentNumber=trim($_POST['student_number']);

$sql="
SELECT student_id
FROM students
WHERE student_number=?
LIMIT 1
";

$stmt=$conn->prepare($sql);

$stmt->bind_param("s",$studentNumber);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){
    die("Student not found.");
}

$student=$result->fetch_assoc();

$studentID=$student['student_id'];

$stmt->close();

$sql="

SELECT
grade_level,
semester,
strand_id

FROM enrollments

WHERE
student_id=?
AND status='Confirmed'

ORDER BY enrollment_id DESC

LIMIT 1

";

$stmt=$conn->prepare($sql);

$stmt->bind_param("i",$studentID);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

    //die("No previous enrollment found.");
    showError(
        "Previous School Not Found!",
        "Please enter a valid previous school."
    );
}

$enrollment=$result->fetch_assoc();

$stmt->close();


$currentGrade=$enrollment['grade_level'];

$currentSemester=$enrollment['semester'];

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

    //die("Student already completed Grade 12.");
    showError(
            "Enrollment Cannot Proceed!",
            "The student has already completed Grade 12.."
        );

}

$strandID=$enrollment['strand_id'];


$result=$conn->query("
SELECT school_year_id
FROM school_years
WHERE is_active=1
LIMIT 1
");

$schoolYear=$result->fetch_assoc();

$schoolYearID=$schoolYear['school_year_id'];


$sql="

SELECT enrollment_id

FROM enrollments

WHERE

student_id=?

AND school_year_id=?

AND status='Pending'

LIMIT 1

";

$stmt=$conn->prepare($sql);

$stmt->bind_param("ii",$studentID,$schoolYearID);

$stmt->execute();

$stmt->store_result();

if($stmt->num_rows>0){

    //die("You already have a pending enrollment.");
    showError(
            "Enrollment Already Pending!",
            "Your enrollment application is currently under review. 
             A notification will be sent to your email address once the review has been completed."
        );

}

$stmt->close();


$sql="

INSERT INTO enrollments(

student_id,
strand_id,
grade_level,
semester,
school_year_id,
status

)

VALUES(

?,?,?,?,?,
'Pending'

)

";

$stmt=$conn->prepare($sql);

$stmt->bind_param(

"iissi",

$studentID,
$strandID,
$nextGrade,
$nextSemester,
$schoolYearID

);

$stmt->execute();

$stmt->close();

header("Location: ../views/student/existing-student-form?success=1");

exit;