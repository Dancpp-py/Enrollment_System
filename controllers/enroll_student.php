<?php

require '../config/db.php';
require '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../views/student/transferee-student-form");
    exit();
}

try {


    $student_type = trim($_POST['student_type']);
    $strand_id = (int)$_POST['strand_id'];
    $grade_level = trim($_POST['grade_level']);

    $last_name = trim($_POST['per_Lname']);
    $first_name = trim($_POST['per_Fname']);
    $middle_name = trim($_POST['per_Mname']);
    $suffix = trim($_POST['per_suffix']);
    $birth_date = $_POST['per_date_birth'];
    $gender = $_POST['per_gender'];
    $email = trim($_POST['per_email']);
    $contact = trim($_POST['per_contact']);
    $address = trim($_POST['per_add']);

    $mo_last = trim($_POST['mo_Lname']);
    $mo_first = trim($_POST['mo_Fname']);
    $mo_middle = trim($_POST['mo_Mname']);
    $mo_suffix = trim($_POST['mo_suffix']);
    $mo_contact = trim($_POST['mo_contact']);
    $mo_address = trim($_POST['mo_add']);


    $fa_last = trim($_POST['fa_Lname']);
    $fa_first = trim($_POST['fa_Fname']);
    $fa_middle = trim($_POST['fa_Mname']);
    $fa_suffix = trim($_POST['fa_suffix']);
    $fa_contact = trim($_POST['fa_contact']);
    $fa_address = trim($_POST['fa_add']);


    $gu_last = trim($_POST['gu_Lname']);
    $gu_first = trim($_POST['gu_Fname']);
    $gu_middle = trim($_POST['gu_Mname']);
    $gu_suffix = trim($_POST['gu_suffix']);
    $gu_relation = $_POST['gu_relation'] ?? '';
    $gu_contact = trim($_POST['gu_contact']);
    $gu_address = trim($_POST['gu_add']);


    $elementary = trim($_POST['elem']);
    $elementary_year = $_POST['elem_yr'];

    $jhs = trim($_POST['jhs']);
    $jhs_year = $_POST['jhs_yr'];

    $shs = trim($_POST['shs']);
    $shs_year = !empty($_POST['shs_yr']) ? $_POST['shs_yr'] : NULL;

    $lrn = trim($_POST['lrn']);


    $semester = "1st Semester";

    if (
        empty($student_type) ||
        empty($strand_id) ||
        empty($grade_level) ||
        empty($last_name) ||
        empty($first_name) ||
        empty($birth_date) ||
        empty($gender) ||
        empty($email) ||
        empty($contact) ||
        empty($address) ||
        empty($mo_last) ||
        empty($mo_first) ||
        empty($mo_contact) ||
        empty($mo_address) ||
        empty($fa_last) ||
        empty($fa_first) ||
        empty($fa_contact) ||
        empty($fa_address) ||
        empty($gu_last) ||
        empty($gu_first) ||
        empty($gu_relation) ||
        empty($gu_contact) ||
        empty($gu_address) ||
        empty($elementary) ||
        empty($elementary_year) ||
        empty($jhs) ||
        empty($jhs_year) ||
        empty($lrn)
    ) {
        // throw new Exception("Please complete all required fields.");
        showError(
            "Incomplete Form.",
            "Please complete all required fields before submitting your enrollment application."
        );
        // -- 
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //throw new Exception("Invalid email address.");

        showError(
            "Invalid Email Address!",
            "Please enter a valid email address."
        );
    }

    if (!preg_match('/^\d{11}$/', $contact)) {
        //throw new Exception("Invalid contact number.");

        showError(
            "Invalid contact number!",
            "Please enter a valid 11-digit contact number."
        );
    }
    if (!preg_match('/^\d{11}$/', $mo_contact)) {
        //throw new Exception("Invalid contact number.");

        showError(
            "Invalid contact number!",
            "Please enter a valid 11-digit contact number."
        );
  
    }
    if (!preg_match('/^\d{11}$/', $fa_contact)) {
        //throw new Exception("Invalid contact number.");

        showError(
            "Invalid contact number!",
            "Please enter a valid 11-digit contact number."
        );
    }
    if (!preg_match('/^\d{11}$/', $gu_contact)) {
        //throw new Exception("Invalid contact number.");
        showError(
            "Invalid contact number!",
            "Please enter a valid 11-digit contact number."
        );
    }
    if (!preg_match('/^\d{12}$/', $lrn)) {
        //throw new Exception("LRN must contain exactly 12 digits.");
        showError(
            "Invalid LRN!",
            "Please enter a valid 12-digit LRN number."
        );
    }
    if(!preg_match('/^\d{4}$/', $elementary_year)){
       // throw new Exception("Elementary year must contain exactly 4 digits.");
         showError(
            "Invalid Elementary Year!",
            "Please enter a valid 4-digit number."
        );
    }
    if(!preg_match('/^\d{4}$/', $jhs_year)){
        // throw new Exception("Junior Highschool year must contain exactly 4 digits.");
         showError(
            "Invalid Junior Year!",
            "Please enter a valid 4-digit number."
        );
    }
    // if(!preg_match('/^\d{4}$/', $shs_year)){
    //      throw new Exception("Senior Highschool year must contain exactly 4 digits.");
    //      echo "<script>
    //              alert('Senior Highschool year must contain exactly 4 digits..');
    //              window.history.back();
    //          </script>";
    //      exit();
    // }
    if (strtotime($birth_date) > time()) {
        //throw new Exception("Invalid birth date.");
        showError(
            "Invalid Birthdate!",
            "Please ensure the selected date is earlier than the current date."
        );
    }
    
    $result = $conn->query("SELECT school_year_id FROM school_years WHERE is_active = 1 LIMIT 1");

    if ($result->num_rows == 0) {
        // throw new Exception("No active school year.");
         showError(
            "School Year Required!",
            "An active school year is required before processing enrollment."
        );
    }

    $school_year = $result->fetch_assoc();
    $school_year_id = $school_year['school_year_id'];

    $stmt = $conn->prepare("
        SELECT student_id
        FROM students
        WHERE email = ?
    ");

    $stmt->bind_param("s", $email);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        //throw new Exception("Email is already registered.");

        showError(
            "Email Already Registered!",
            "The email address you entered is already in use."
        );
    }

    $stmt->close();


    $conn->begin_transaction();


    $stmt = $conn->prepare("
        INSERT INTO students
        (
            student_type,
            last_name,
            first_name,
            middle_name,
            suffix,
            birth_date,
            gender,
            contact_number,
            email,
            address
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssssss",
        $student_type,
        $last_name,
        $first_name,
        $middle_name,
        $suffix,
        $birth_date,
        $gender,
        $contact,
        $email,
        $address
    );

    $stmt->execute();

    $student_id = $conn->insert_id;

    $stmt->close();


    $stmt = $conn->prepare("
        INSERT INTO student_family_members
        (
            student_id,
            relationship,
            last_name,
            first_name,
            middle_name,
            suffix,
            contact_number,
            address
        )
        VALUES
        (?, 'Father', ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issssss",
        $student_id,
        $fa_last,
        $fa_first,
        $fa_middle,
        $fa_suffix,
        $fa_contact,
        $fa_address
    );

    $stmt->execute();

    $stmt->close();


    $stmt = $conn->prepare("
        INSERT INTO student_family_members
        (
            student_id,
            relationship,
            last_name,
            first_name,
            middle_name,
            suffix,
            contact_number,
            address
        )
        VALUES
        (?, 'Mother', ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issssss",
        $student_id,
        $mo_last,
        $mo_first,
        $mo_middle,
        $mo_suffix,
        $mo_contact,
        $mo_address
    );

    $stmt->execute();

    $stmt->close();


        $stmt = $conn->prepare("
            INSERT INTO student_family_members
            (
                student_id,
                relationship,
                last_name,
                first_name,
                middle_name,
                suffix,
                contact_number,
                address
            )
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssssss",
            $student_id,
            $gu_relation,
            $gu_last,
            $gu_first,
            $gu_middle,
            $gu_suffix,
            $gu_contact,
            $gu_address
        );

        $stmt->execute();

        $stmt->close();
    

    $stmt = $conn->prepare("
        SELECT education_id
        FROM educational_background
        WHERE lrn = ?
    ");

    $stmt->bind_param("s", $lrn);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        // throw new Exception("LRN already exists.");
         showError(
            "LRN Already Exists!",
            "The Learner Reference Number (LRN) is already registered in the system."
        );
    }

    $stmt->close();

    $stmt = $conn->prepare("
        INSERT INTO educational_background
        (
            student_id,
            elementary_school,
            elementary_year,
            junior_high_school,
            junior_high_year,
            senior_high_school,
            senior_high_year,
            lrn
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isisisis",
        $student_id,
        $elementary,
        $elementary_year,
        $jhs,
        $jhs_year,
        $shs,
        $shs_year,
        $lrn
    );

    $stmt->execute();

    $stmt->close();


    $stmt = $conn->prepare("
        INSERT INTO enrollments
        (
            student_id,
            strand_id,
            grade_level,
            semester,
            school_year_id,
            status
        )
        VALUES
        (?, ?, ?, ?, ?, 'Pending')
    ");

    $stmt->bind_param(
        "iissi",
        $student_id,
        $strand_id,
        $grade_level,
        $semester,
        $school_year_id
    );

    $stmt->execute();

    $stmt->close();


    $conn->commit();
       
    header("Location: ../views/student/transferee-student-form?success=1");
    exit();

} catch (Exception $e) {

    $conn->rollback();

    header("Location: ../views/student/transferee-student-form?error=" . urlencode($e->getMessage()));
    exit();

}