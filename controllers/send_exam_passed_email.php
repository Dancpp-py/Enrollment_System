<?php

require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendExamPassedEmail(
    $studentEmail,
    $studentName,
    $applicationType,
    $gradeLevel,
    $strand,
    $schoolYear,
    $average
) {

    $mail = new PHPMailer(true);

    try {

        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'payrollmngmnt00@gmail.com';
        $mail->Password   = 'zngf jdza jgbt ihbf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender & Recipient
        $mail->setFrom('payrollmngmnt00@gmail.com', 'Masinag Senior High School');
        $mail->addAddress($studentEmail, $studentName);

        // HTML Email
        $mail->isHTML(true);
        $mail->Subject = "Application Update - Entrance Examination Passed";

        // Load the HTML Email Template
        ob_start();

        include "../views/email_templates/exam_passed_template.php";

        $mail->Body = ob_get_clean();

        // Plain text version
        $mail->AltBody =
            "Congratulations {$studentName}!\n\n" .
            "You have successfully passed the Entrance Examination of Masinag Senior High School.\n\n" .
            "Your application is now under Official Enrollment Review.\n\n" .
            "Please wait for another email regarding your final enrollment status.";

        $mail->send();

        return true;

    } catch (Exception $e) {

        throw new Exception(
            "Exam-passed email could not be sent. Mailer Error: {$mail->ErrorInfo}"
        );

    }

}