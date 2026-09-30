<?php
require_once "../config/db.php";
require_once "../vendor/autoload.php";
require_once "../views/includes/helpers.php";

use Dompdf\Dompdf;

function generateRegistrationForm($enrollmentID, $officialStudentNumber, $plainPassword = null, $paymentLink = null) {
    global $conn;

    $stmt = $conn->prepare("
        SELECT
            e.enrollment_id,
            e.grade_level,
            e.semester,
            e.strand_id,
            e.section_id,
            s.student_number,
            s.first_name,
            s.last_name,
            s.middle_name,
            s.suffix,
            s.gender,
            s.contact_number,
            s.email,
            s.address,
            st.strand_name,
            sy.school_year,
            sec.section_name
        FROM enrollments e
        INNER JOIN students s ON e.student_id = s.student_id
        INNER JOIN strands st ON e.strand_id = st.strand_id
        INNER JOIN school_years sy ON e.school_year_id = sy.school_year_id
        INNER JOIN sections sec ON sec.section_id = e.section_id
        WHERE e.enrollment_id = ?
    ");

    $stmt->bind_param("i", $enrollmentID);
    $stmt->execute();

    $student = $stmt->get_result()->fetch_assoc();

    if (empty($student)) {
        showError(
            "Error!",
            "Enrollment not found for enrollment {$enrollmentID}."
        );
        exit;
    }

    $stmt->close();

    if (!empty($officialStudentNumber)) {
        $student['student_number'] = $officialStudentNumber;
    }

    $studentName = $student['first_name'] . " " . $student['last_name'];

    // Required subjects for this strand/grade/semester (unchanged — this
    // part was already correct, since it isn't gated by whether a
    // schedule exists).
    $stmt = $conn->prepare("
        SELECT sub.subject_code, sub.subject_name, sub.units
        FROM curriculum c
        INNER JOIN subjects sub ON sub.subject_id = c.subject_id
        WHERE c.strand_id = ?
          AND c.grade_level = ?
          AND c.semester = ?
    ");
    $stmt->bind_param("iss", $student['strand_id'], $student['grade_level'], $student['semester']);
    $stmt->execute();
    $subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // -----------------------------------------------------------------
    // Class schedule — FIXED.
    //
    // Driven from `curriculum` (every subject required for this strand/
    // grade/semester) with LEFT JOINs onto class_schedules/rooms/
    // professors, instead of starting from class_schedules with INNER
    // JOINs. That old version silently dropped any subject missing a
    // room or teacher (INNER JOIN needs a match on both sides — NULL
    // room_id/professor_id means no match, means the whole row vanishes),
    // and it also never filtered by semester, so it could pull in the
    // OTHER semester's schedule rows too (both semesters' class_schedules
    // rows get created together when a section is made).
    //
    // Now every required subject always gets a row; day/time/room/
    // teacher are NULL when not yet set, and the template below renders
    // "TBA" for those instead of hiding the subject.
    // -----------------------------------------------------------------
    $stmt = $conn->prepare("
        SELECT
            sub.subject_name,
            cs.day_of_week,
            cs.start_time,
            cs.end_time,
            r.room_name,
            p.last_name AS prof_last_name,
            p.first_name AS prof_first_name
        FROM curriculum c
        INNER JOIN subjects sub ON sub.subject_id = c.subject_id
        LEFT JOIN class_schedules cs ON cs.curriculum_id = c.curriculum_id AND cs.section_id = ?
        LEFT JOIN rooms r ON r.room_id = cs.room_id
        LEFT JOIN professors p ON p.professor_id = cs.professor_id
        WHERE c.strand_id = ?
          AND c.grade_level = ?
          AND c.semester = ?
        ORDER BY
            (cs.day_of_week IS NULL),
            FIELD(cs.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),
            cs.start_time,
            sub.subject_name
    ");
    $stmt->bind_param(
        "iiss",
        $student['section_id'],
        $student['strand_id'],
        $student['grade_level'],
        $student['semester']
    );
    $stmt->execute();
    $schedule = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $fees = [
        'Tuition Fee'       => 1.00,
        'Laboratory Fee'    => 0.00,
        'Miscellaneous Fee' => 0.00,
        'LMS'               => 0.00,
        'NSTP/ROTC'         => 0.00,
    ];
    $totalFees    = array_sum($fees);
    $dateEnrolled = date("F j, Y");

    ob_start();

    include "../views/student/reg-form.php";

    $html = ob_get_clean();

    $dompdf = new Dompdf();

    $dompdf->loadHtml($html);
    $dompdf->setPaper("A4", "portrait");
    $dompdf->render();

    $pdfDir = APP_ROOT . "/generated_pdfs";

    if (!is_dir($pdfDir)) {
        mkdir($pdfDir, 0755, true);
    }

    $pdfPath = $pdfDir . "/registration_{$enrollmentID}.pdf";

    file_put_contents($pdfPath, $dompdf->output());

    require_once "send_regform_email.php";

    sendRegForm(
        $student['email'],
        $studentName,
        $enrollmentID,
        $student['student_number'],
        $plainPassword,
        $paymentLink
    );
}