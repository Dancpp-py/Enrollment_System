<?php

require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendExamNotice($studentEmail, $studentName, $enrollmentID) {

    $pdfPath = APP_ROOT . "/generated_pdfs/exam_notice_{$enrollmentID}.pdf";

    if (!file_exists($pdfPath)) {
        // throw new Exception("Generated exam notice PDF not found for enrollment {$enrollmentID}.");
        showError(
        "Error!",
        "Generated exam notice PDF not found for enrollment {$enrollmentID}."
    );
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'payrollmngmnt00@gmail.com';
        $mail->Password   = 'zngf jdza jgbt ihbf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('payrollmngmnt00@gmail.com', 'Masinag Senior High School');
        $mail->addAddress($studentEmail, $studentName);

        $mail->addAttachment($pdfPath, "Entrance_Exam_Notice.pdf");

        $mail->isHTML(true);
        $mail->Subject = "Notice of Entrance Examination";
        $mail->Body    = "Hi {$studentName},<br><br>Your application has been reviewed and you are scheduled to take the Senior High School Entrance Examination. Please see the attached notice for your exam ID, schedule, and reminders.<br><br>Good luck!";
        $mail->AltBody = "Hi {$studentName}, your application has been reviewed and you are scheduled to take the entrance examination. Please see the attached notice for details.";

        $mail->send();
        return true;

    } catch (Exception $e) {
        throw new Exception("Exam notice email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}