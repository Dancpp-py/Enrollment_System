<?php
require_once "../config/db.php";
require_once "../vendor/autoload.php";


use Dompdf\Dompdf;

$stmt = $conn->prepare("
    SELECT
        s.first_name,
        s.last_name,
        s.exam_number,
        s.email
    FROM enrollments e
    INNER JOIN students s
        ON e.student_id = s.student_id
    WHERE e.enrollment_id = ?
");

$stmt->bind_param("i", $enrollmentID);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

if (empty($student['exam_number'])) {
    // throw new Exception("Exam number not yet generated for enrollment {$enrollmentID}.");
    showError(
        "Error!",
        "Exam number not yet generated for enrollment {$enrollmentID}."
    );
}

$stmt->close();

$studentName = $student['first_name'] . " " . $student['last_name'];
$examNumber  = $student['exam_number'];

ob_start();

include "../views/student/entrance-exam-notice.php";

$html = ob_get_clean();

$dompdf = new Dompdf();

$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$pdfDir = APP_ROOT . "/generated_pdfs";

if (!is_dir($pdfDir)) {
    mkdir($pdfDir, 0755, true);
}

$pdfPath = $pdfDir . "/exam_notice_{$enrollmentID}.pdf";

file_put_contents($pdfPath, $dompdf->output());

require_once "send_exam_notice.php";

sendExamNotice(
    $student['email'],
    $studentName,
    $enrollmentID
);