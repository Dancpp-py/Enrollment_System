<?php

require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEnrollmentRejectedEmail($studentEmail, $studentName) {

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'payrollmngmnt00@gmail.com';
        $mail->Password   = 'zngf jdza jgbt ihbf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('payrollmngmnt00@gmail.com', 'Bawal Matalino Senior High School');
        $mail->addAddress($studentEmail, $studentName);

        $mail->isHTML(true);
        $mail->Subject = "Enrollment Application Update";
        $mail->Body    = "Hi {$studentName},<br><br>Thank you for completing the entrance examination and enrollment review process. After careful review, we regret to inform you that we are unable to proceed with your enrollment at this time.<br><br>We appreciate your interest in our school and wish you the best in your future academic endeavors.";
        $mail->AltBody = "Hi {$studentName}, after review we are unable to proceed with your enrollment at this time.";

        $mail->send();
        return true;

    } catch (Exception $e) {
        throw new Exception("Enrollment-rejected email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}