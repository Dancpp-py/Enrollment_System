<?php

require_once "../vendor/autoload.php";

use Dompdf\Dompdf;

define("GENERATE_PDF", true);

$enrollmentID = $enrollment_id;

include "generate_regform.php";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$pdfDir = APP_ROOT . "/generated_pdfs";

if (!is_dir($pdfDir)) {
    mkdir($pdfDir, 0755, true);
}

$pdfPath = $pdfDir . "/registration_{$enrollmentID}.pdf";

$written = file_put_contents($pdfPath, $dompdf->output());

if ($written === false) {
    // throw new Exception("Failed to write PDF to {$pdfPath}");
    showError(
        "Error!",
        "Failed to write PDF to {$pdfPath}."
    );
}

require_once "send_regform_email.php";
try {
    sendRegForm($student['email'], $student['first_name'] . ' ' . $student['last_name'], $enrollmentID);
} catch (Exception $e) {
    error_log("Email send failed for enrollment {$enrollmentID}: " . $e->getMessage());
}