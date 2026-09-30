<?php

require_once "../config/db.php";
require '../views/includes/helpers.php';


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../views/student/transferee-student-form");
    exit();
}

function fail($message) {
    header("Location: ../views/student/transferee-student-form?error=" . urlencode($message));
    exit();
}

function mapGuardianRelation($rawValue) {
    $map = [
        'Father'         => 'Father',
        'Mother'         => 'Mother',
        'Legalguardian'  => 'Legal Guardian',
        'Grandfather'    => 'Grandfather',
        'Grandmother'    => 'Grandmother',
        'Brother'        => 'Brother',
        'Sister'         => 'Sister',
        'Otherrelative'  => 'Other',
    ];

    return $map[$rawValue] ?? null;
}

function uploadDocument($conn, $fileInputName, $documentType, $enrollmentId, $required) {

    $allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (empty($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        if ($required) {
            // throw new Exception("{$documentType} is required.");
            showError(
            "{$documentType} is required.",
            "Please upload the required document before continuing."
            );
        }
        return; 
    }


    $file = $_FILES[$fileInputName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        // throw new Exception("There was a problem uploading {$documentType}.");
        showError(
            "Upload failed.".
            "We could not upload your {$documentType}. Please try again."
        );
    }

    if ($file['size'] > $maxSize) {
        // throw new Exception("{$documentType} exceeds the 5MB size limit.");
        showError(
            "File size exceeded.".
            "The uploaded {$documentType} is larger than 5 MB. Please choose a smaller file."
        );
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        // throw new Exception("{$documentType} must be a PDF, JPG, or PNG file.");
        showError(
            "Invalid file format.".
            "Please upload your {$documentType} in PDF, JPG, JPEG, or PNG format only."
        );
    }

    $uploadDir = APP_ROOT . "/uploads/documents";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $safeType = preg_replace('/[^a-zA-Z0-9]/', '_', $documentType);
    $fileName = "{$enrollmentId}_{$safeType}_" . time() . "." . $ext;
    $destination = "{$uploadDir}/{$fileName}";

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
    // throw new Exception("Failed to save {$documentType}. Please try again.");
    showError(
        "Upload Failed.",
        "Failed to save your {$documentType}. Please try uploading the file again."
    );
}

    $relativePath = "uploads/documents/{$fileName}";

    $stmt = $conn->prepare("
        INSERT INTO enrollment_documents (enrollment_id, document_type, file_path)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("iss", $enrollmentId, $documentType, $relativePath);
    $stmt->execute();
    $stmt->close();
}

try {

    $student_type = trim($_POST['student_type'] ?? '');
    $strand_id = (int)($_POST['strand_id'] ?? 0);
    $grade_level = trim($_POST['grade_level'] ?? '');

    $last_name = trim($_POST['per_Lname'] ?? '');
    $first_name = trim($_POST['per_Fname'] ?? '');
    $middle_name = trim($_POST['per_Mname'] ?? '');
    $suffix = trim($_POST['per_suffix'] ?? '');
    $birth_date = $_POST['per_date_birth'] ?? '';
    $gender = $_POST['per_gender'] ?? '';
    $email = trim($_POST['per_email'] ?? '');
    $contact = trim($_POST['per_contact'] ?? '');
    $address = trim($_POST['per_add'] ?? '');

    $fa_last = trim($_POST['fa_Lname'] ?? '');
    $fa_first = trim($_POST['fa_Fname'] ?? '');
    $fa_middle = trim($_POST['fa_Mname'] ?? '');
    $fa_suffix = trim($_POST['fa_suffix'] ?? '');
    $fa_contact = trim($_POST['fa_contact'] ?? '');
    $fa_address = trim($_POST['fa_add'] ?? '');

    $mo_last = trim($_POST['mo_Lname'] ?? '');
    $mo_first = trim($_POST['mo_Fname'] ?? '');
    $mo_middle = trim($_POST['mo_Mname'] ?? '');
    $mo_suffix = trim($_POST['mo_suffix'] ?? '');
    $mo_contact = trim($_POST['mo_contact'] ?? '');
    $mo_address = trim($_POST['mo_add'] ?? '');

    $gu_last = trim($_POST['gu_Lname'] ?? '');
    $gu_first = trim($_POST['gu_Fname'] ?? '');
    $gu_middle = trim($_POST['gu_Mname'] ?? '');
    $gu_suffix = trim($_POST['gu_suffix'] ?? '');
    $gu_relation_raw = $_POST['gu_relation'] ?? '';
    $gu_contact = trim($_POST['gu_contact'] ?? '');
    $gu_address = trim($_POST['gu_add'] ?? '');

    $elementary = trim($_POST['elem'] ?? '');
    $elementary_year = $_POST['elem_yr'] ?? '';

    $jhs = trim($_POST['jhs'] ?? '');
    $jhs_year = $_POST['jhs_yr'] ?? '';

    $shs = trim($_POST['shs'] ?? '');
    $shs_year = !empty($_POST['shs_yr']) ? $_POST['shs_yr'] : NULL;

    $lrn = trim($_POST['lrn'] ?? '');

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
        empty($fa_last) ||
        empty($fa_first) ||
        empty($fa_contact) ||
        empty($fa_address) ||
        empty($mo_last) ||
        empty($mo_first) ||
        empty($mo_contact) ||
        empty($mo_address) ||
        empty($gu_last) ||
        empty($gu_first) ||
        empty($gu_relation_raw) ||
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
}

    if (!in_array($student_type, ['New', 'Transferee'])) {
    // throw new Exception("Invalid student type.");
    showError(
        "Invalid Student Type.",
        "Please select a valid student type before proceeding."
    );
}

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // throw new Exception("Invalid email address.");
    showError(
        "Invalid Email Address.",
        "Please enter a valid email address (e.g., example@email.com)."
    );
}

    if (!preg_match('/^09\d{9}$/', $contact)) {
    // throw new Exception("Invalid contact number.");
    showError(
        "Invalid Contact Number.",
        "Please enter a valid Philippine mobile number starting with 09 and containing exactly 11 digits."
    );
}
    if (!preg_match('/^09\d{9}$/', $fa_contact)) {
    // throw new Exception("Invalid father's contact number.");
    showError(
        "Invalid Father Contact Number.",
        "Please enter a valid 11-digit mobile number for the father."
    );
}
    if (!preg_match('/^09\d{9}$/', $mo_contact)) {
    // throw new Exception("Invalid mother's contact number.");
    showError(
        "Invalid Mother Contact Number.",
        "Please enter a valid 11-digit mobile number for the mother."
    );
}
    if (!preg_match('/^09\d{9}$/', $gu_contact)) {
    // throw new Exception("Invalid guardian contact number.");
    showError(
        "Invalid Guardian Contact Number.",
        "Please enter a valid 11-digit mobile number for the guardian."
    );
}
    if (!preg_match('/^\d{12}$/', $lrn)) {
    // throw new Exception("LRN must contain exactly 12 digits.");
    showError(
        "Invalid Learner Reference Number.",
        "The Learner Reference Number (LRN) must contain exactly 12 numeric digits."
    );
}
    if (strtotime($birth_date) > time()) {
    // throw new Exception("Invalid birth date.");
    showError(
        "Invalid Birth Date.",
        "The birth date cannot be a future date. Please select a valid date."
    );
}

    $gu_relation = mapGuardianRelation($gu_relation_raw);

    if ($gu_relation === null) {
    // throw new Exception("Invalid guardian relationship selected.");
    showError(
        "Invalid Guardian Relationship.",
        "Please select a valid relationship between the guardian and the student."
    );
}

//     if ($student_type === 'Transferee' && empty($_FILES['up_tor']['name'])) {
//     // throw new Exception("Transcript of Records / Form 137 is required for transferees.");
//     showError(
//         "Required Document Missing.",
//         "Transcript of Records (TOR) or Form 137 is required for transferee applicants."
//     );
// }

    $result = $conn->query("SELECT school_year_id FROM school_years WHERE is_active = 1 LIMIT 1");

    if ($result->num_rows == 0) {
    // throw new Exception("No active school year.");
    showError(
        "No Active School Year.",
        "Enrollment cannot proceed because there is currently no active school year."
    );
}

    $school_year = $result->fetch_assoc();
    $school_year_id = $school_year['school_year_id'];

    $stmt = $conn->prepare("SELECT student_id FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
    // throw new Exception("Email is already registered.");
    showError(
        "Email Already Registered.",
        "The email address you entered is already associated with another student account."
    );
}

    $stmt->close();

    $stmt = $conn->prepare("SELECT education_id FROM educational_background WHERE lrn = ?");
    $stmt->bind_param("s", $lrn);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
    // throw new Exception("LRN already exists.");
    showError(
        "LRN Already Exists.",
        "The Learner Reference Number (LRN) is already registered in the system."
    );
}

    $stmt->close();

    $conn->begin_transaction();

    $stmt = $conn->prepare("
        INSERT INTO students
        (student_type, last_name, first_name, middle_name, suffix, birth_date, gender, contact_number, email, address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
        (student_id, relationship, last_name, first_name, middle_name, suffix, contact_number, address)
        VALUES (?, 'Father', ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("issssss", $student_id, $fa_last, $fa_first, $fa_middle, $fa_suffix, $fa_contact, $fa_address);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("
        INSERT INTO student_family_members
        (student_id, relationship, last_name, first_name, middle_name, suffix, contact_number, address)
        VALUES (?, 'Mother', ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("issssss", $student_id, $mo_last, $mo_first, $mo_middle, $mo_suffix, $mo_contact, $mo_address);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("
        INSERT INTO student_family_members
        (student_id, relationship, last_name, first_name, middle_name, suffix, contact_number, address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("isssssss", $student_id, $gu_relation, $gu_last, $gu_first, $gu_middle, $gu_suffix, $gu_contact, $gu_address);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("
        INSERT INTO educational_background
        (student_id, elementary_school, elementary_year, junior_high_school, junior_high_year, senior_high_school, senior_high_year, lrn)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("isisisis", $student_id, $elementary, $elementary_year, $jhs, $jhs_year, $shs, $shs_year, $lrn);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("
        INSERT INTO enrollments
        (student_id, strand_id, grade_level, semester, school_year_id, status, stage)
        VALUES (?, ?, ?, ?, ?, 'Pending', 'Application Review')
    ");

    $stmt->bind_param("iissi", $student_id, $strand_id, $grade_level, $semester, $school_year_id);
    $stmt->execute();

    $enrollment_id = $conn->insert_id;

    $stmt->close();

    uploadDocument($conn, 'up_psa', 'PSA Birth Certificate', $enrollment_id, true);
    uploadDocument($conn, 'up_f138', 'Grade 10 Report Card (Form 138)', $enrollment_id, false);
    uploadDocument($conn, 'ip_goodMoral', 'Certificate of Good Moral', $enrollment_id, false);
    uploadDocument($conn, 'up_2x2', 'Recent 2x2 ID Picture', $enrollment_id, true); 
    uploadDocument($conn, 'up_tor', 'Transcript of Records / Form 137', $enrollment_id, false); // $student_type === 'Transferee'

    $conn->commit();

    header("Location: ../views/student/transferee-student-form?success=1");
    exit();

} catch (Exception $e) {

    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }

    fail($e->getMessage());
}
