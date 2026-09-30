<?php

require_once "../config/db.php";
require_once '../views/includes/auth.php';

requireRole('Scheduler', 'Super Admin');
include '../views/includes/helpers.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$action = $_POST['action'] ?? '';

$VALID_GRADES = ['Grade 11', 'Grade 12'];
$VALID_SEMESTERS = ['1st Semester', '2nd Semester'];

if ($action === 'create_version') {
    $version_name = trim($_POST['version_name'] ?? '');
    $source_version_id = (int) ($_POST['source_version_id'] ?? 0);

    if (empty($version_name)) {
        showError("Invalid Version Name!", "Please enter a curriculum version name.");
        exit;
    }

    if (mb_strlen($version_name) > 100) {
        showError("Name Too Long!", "The version name must not exceed 100 characters.");
        exit;
    }

    $stmt = $conn->prepare("
        SELECT curriculum_version_id
        FROM curriculum_versions
        WHERE version_name = ?
    ");
    $stmt->bind_param("s", $version_name);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        showError("Version Already Exists!", "A curriculum version with this name already exists.");
        exit;
    }

    $stmt->close();

    if ($source_version_id > 0) {
        $stmt = $conn->prepare("
            SELECT curriculum_version_id
            FROM curriculum_versions
            WHERE curriculum_version_id = ?
        ");
        $stmt->bind_param("i", $source_version_id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows === 0) {
            $stmt->close();
            showError("Invalid Source Version!", "The selected template curriculum version could not be found.");
            exit;
        }

        $stmt->close();
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            INSERT INTO curriculum_versions (version_name)
            VALUES (?)
        ");
        $stmt->bind_param("s", $version_name);
        $stmt->execute();

        $new_version_id = $stmt->insert_id;
        $stmt->close();

        if ($source_version_id > 0) {
            $stmt = $conn->prepare("
                INSERT INTO curriculum (
                    subject_id,
                    strand_id,
                    grade_level,
                    semester,
                    curriculum_version_id
                )
                SELECT
                    subject_id,
                    strand_id,
                    grade_level,
                    semester,
                    ?
                FROM curriculum
                WHERE curriculum_version_id = ?
            ");
            $stmt->bind_param("ii", $new_version_id, $source_version_id);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();

        $_SESSION['curriculum_version_id'] = $new_version_id;
        $_SESSION['curriculum_builder_grade'] = 'Grade 11';
        $_SESSION['curriculum_builder_semester'] = '1st Semester';

        showSuccess(
            "Version Created!",
            "The curriculum version has been created successfully." .
            ($source_version_id > 0 ? " Subjects copied from the previous version." : ""),
            "window.location.href = '../views/scheduler/curriculum-version-builder.php';"
        );
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        showError("Error!", "Failed to create curriculum version.");
        exit;
    }
}

if ($action === 'rename_version') {
    $version_id = (int) ($_SESSION['curriculum_version_id'] ?? 0);
    $version_name = trim($_POST['version_name'] ?? '');

    if ($version_id <= 0) {
        showError("Invalid Request!", "No curriculum version specified.");
        exit;
    }

    if (empty($version_name)) {
        showError("Invalid Version Name!", "Please enter a curriculum version name.");
        exit;
    }

    if (mb_strlen($version_name) > 100) {
        showError("Name Too Long!", "The version name must not exceed 100 characters.");
        exit;
    }

    $stmt = $conn->prepare("
        SELECT curriculum_version_id
        FROM curriculum_versions
        WHERE curriculum_version_id = ?
    ");
    $stmt->bind_param("i", $version_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();
        showError("Version Not Found!", "The curriculum version could not be found.");
        exit;
    }

    $stmt->close();

    $stmt = $conn->prepare("
        SELECT curriculum_version_id
        FROM curriculum_versions
        WHERE version_name = ?
        AND curriculum_version_id != ?
    ");
    $stmt->bind_param("si", $version_name, $version_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        showError(
            "Version Already Exists!",
            "Another curriculum version already uses this name. Please choose a different name."
        );
        exit;
    }

    $stmt->close();

    $stmt = $conn->prepare("
        UPDATE curriculum_versions
        SET version_name = ?
        WHERE curriculum_version_id = ?
    ");
    $stmt->bind_param("si", $version_name, $version_id);
    $stmt->execute();
    $stmt->close();

    showSuccess(
        "Name Updated!",
        "The curriculum version has been renamed successfully.",
        "window.location.href = '../views/scheduler/curriculum-version-builder.php';"
    );
    exit;
}

if ($action === 'create_school_year') {
    $school_year = trim($_POST['school_year'] ?? '');

    if (empty($school_year)) {
        showError("Invalid School Year!", "Please enter a school year.");
        exit;
    }

    if (!preg_match('/^(\d{4})-(\d{4})$/', $school_year, $matches)) {
        showError(
            "Invalid Format!",
            "School year must be in the format YYYY-YYYY, e.g. 2026-2027."
        );
        exit;
    }

    $startYear = (int) $matches[1];
    $endYear = (int) $matches[2];

    if ($endYear !== $startYear + 1) {
        showError(
            "Invalid School Year Range!",
            "The second year must be exactly one year after the first, e.g. 2026-2027."
        );
        exit;
    }

    $stmt = $conn->prepare("
        SELECT school_year_id
        FROM school_years
        WHERE school_year = ?
    ");
    $stmt->bind_param("s", $school_year);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        showError("School Year Already Exists!", "This school year has already been added.");
        exit;
    }

    $stmt->close();

    $stmt = $conn->prepare("
        INSERT INTO school_years (school_year, is_active)
        VALUES (?, 0)
    ");
    $stmt->bind_param("s", $school_year);
    $stmt->execute();
    $stmt->close();

    showSuccess(
        "School Year Added!",
        "{$school_year} has been added. Assign a curriculum version to it below when you are ready.",
        "window.location.href = '../views/scheduler/curriculum-version-builder.php';"
    );
    exit;
}

if ($action === 'activate_school_year') {
    $school_year_id = (int) ($_POST['school_year_id'] ?? 0);

    if ($school_year_id <= 0) {
        showError("Invalid Request!", "No school year was specified.");
        exit;
    }

    $stmt = $conn->prepare("
        SELECT school_year_id, school_year
        FROM school_years
        WHERE school_year_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $school_year_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $school_year_data = $result->fetch_assoc();
    $stmt->close();

    if (!$school_year_data) {
        showError("School Year Not Found!", "The selected school year could not be found.");
        exit;
    }

    $school_year_name = $school_year_data['school_year'];

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            UPDATE school_years
            SET is_active = 0
        ");
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("
            UPDATE school_years
            SET is_active = 1
            WHERE school_year_id = ?
        ");
        $stmt->bind_param("i", $school_year_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        showSuccess(
            "School Year Activated!",
            "{$school_year_name} is now the active school year.",
            "window.location.href = '../views/scheduler/curriculum-builder';"
        );
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        showError("Activation Failed!", "The school year could not be activated.");
        exit;
    }
}

if ($action === 'assign_school_year') {
    $school_year_id = (int) ($_POST['school_year_id'] ?? 0);
    $curriculum_version_id = (int) ($_POST['curriculum_version_id'] ?? 0);

    if ($school_year_id <= 0) {
        showError("Invalid Request!", "No school year specified.");
        exit;
    }

    $stmt = $conn->prepare("
        SELECT school_year_id
        FROM school_years
        WHERE school_year_id = ?
    ");
    $stmt->bind_param("i", $school_year_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();
        showError("Invalid School Year!", "The selected school year could not be found.");
        exit;
    }

    $stmt->close();

    if ($curriculum_version_id > 0) {
        $stmt = $conn->prepare("
            SELECT curriculum_version_id
            FROM curriculum_versions
            WHERE curriculum_version_id = ?
        ");
        $stmt->bind_param("i", $curriculum_version_id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows === 0) {
            $stmt->close();
            showError("Invalid Version!", "The selected curriculum version could not be found.");
            exit;
        }

        $stmt->close();
    }

    $stmt = $conn->prepare("
        UPDATE school_years
        SET curriculum_version_id = ?
        WHERE school_year_id = ?
    ");
    $stmt->bind_param("ii", $curriculum_version_id, $school_year_id);
    $stmt->execute();
    $stmt->close();

    showSuccess(
        "Assignment Updated!",
        "The school year has been assigned to the curriculum version."
    );
    exit;
}

if ($action === 'save_curriculum') {
    $version_id = (int) ($_SESSION['curriculum_version_id'] ?? 0);
    $grade_level = $_POST['grade_level'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $curriculum_data = $_POST['curriculum'] ?? [];

    if ($version_id <= 0) {
        showError("Invalid Version!", "No curriculum version specified.");
        exit;
    }

    if (!in_array($grade_level, $VALID_GRADES, true)) {
        showError("Invalid Grade Level!", "The grade level being saved is not valid.");
        exit;
    }

    if (!in_array($semester, $VALID_SEMESTERS, true)) {
        showError("Invalid Semester!", "The semester being saved is not valid.");
        exit;
    }

    $stmt = $conn->prepare("
        SELECT curriculum_version_id
        FROM curriculum_versions
        WHERE curriculum_version_id = ?
    ");
    $stmt->bind_param("i", $version_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        $stmt->close();
        showError("Invalid Version!", "The curriculum version could not be found.");
        exit;
    }

    $stmt->close();

    $valid_subjects = [];
    $subject_result = $conn->query("
        SELECT subject_id
        FROM subjects
        WHERE is_active = 1
    ");

    while ($row = $subject_result->fetch_assoc()) {
        $valid_subjects[(int) $row['subject_id']] = true;
    }

    $valid_strands = [];
    $strand_result = $conn->query("
        SELECT strand_id
        FROM strands
    ");

    while ($row = $strand_result->fetch_assoc()) {
        $valid_strands[(int) $row['strand_id']] = true;
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            DELETE FROM curriculum
            WHERE curriculum_version_id = ?
            AND grade_level = ?
            AND semester = ?
        ");
        $stmt->bind_param("iss", $version_id, $grade_level, $semester);
        $stmt->execute();
        $stmt->close();

        $insertStmt = $conn->prepare("
            INSERT INTO curriculum (
                subject_id,
                strand_id,
                grade_level,
                semester,
                curriculum_version_id
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        if (is_array($curriculum_data)) {
            foreach ($curriculum_data as $subject_id => $strands_data) {
                $subject_id = (int) $subject_id;

                if (!isset($valid_subjects[$subject_id])) {
                    continue;
                }

                if (!is_array($strands_data)) {
                    continue;
                }

                foreach ($strands_data as $strand_id => $grades_data) {
                    $strand_id = (int) $strand_id;

                    if (!isset($valid_strands[$strand_id])) {
                        continue;
                    }

                    if (!is_array($grades_data)) {
                        continue;
                    }

                    foreach ($grades_data as $glevel => $semesters_data) {
                        if ($glevel !== $grade_level) {
                            continue;
                        }

                        if (!is_array($semesters_data)) {
                            continue;
                        }

                        foreach ($semesters_data as $sem => $value) {
                            if ($sem !== $semester) {
                                continue;
                            }

                            if ($value == 1) {
                                $insertStmt->bind_param(
                                    "iissi",
                                    $subject_id,
                                    $strand_id,
                                    $glevel,
                                    $sem,
                                    $version_id
                                );
                                $insertStmt->execute();
                            }
                        }
                    }
                }
            }
        }

        $insertStmt->close();
        $conn->commit();

        $_SESSION['curriculum_builder_grade'] = $grade_level;
        $_SESSION['curriculum_builder_semester'] = $semester;

        showSuccess(
            "Curriculum Saved!",
            "Changes to {$grade_level}, {$semester} have been saved successfully.",
            "window.location.href = '../views/scheduler/curriculum-version-builder';"
        );
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        showError("Error!", "Failed to save curriculum.");
        exit;
    }
}

header("Location: ../views/scheduler/curriculum-builder");
exit;