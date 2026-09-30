<?php

require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOfficiallyEnrolledEmail($studentEmail, $studentName, $sectionName, $studentNumber, $plainPassword = null, $paymentLink = null) {

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

        $credentialsBlockHtml = '';
        $credentialsBlockText = '';

        if (!empty($plainPassword)) {
            $credentialsBlockHtml = "
                <p>You can also log in to the student portal using the credentials below:</p>
                <ul>
                    <li><strong>Username (Student Number):</strong> {$studentNumber}</li>
                    <li><strong>Password:</strong> {$plainPassword}</li>
                </ul>
                <p>Please keep this information private and change your password once that feature is available.</p>
            ";
            $credentialsBlockText = "\n\nStudent Portal Login:\nUsername: {$studentNumber}\nPassword: {$plainPassword}\n";
        }

        $paymentBlockHtml = '';
        $paymentBlockText = '';

        if (!empty($paymentLink)) {
            $paymentBlockHtml = "
                <p>To complete your enrollment, please settle the ₱1.00 formality fee using the link below:</p>
                <p><a href=\"{$paymentLink}\">{$paymentLink}</a></p>
            ";
            $paymentBlockText = "\n\nComplete your enrollment by settling the P1.00 formality fee: {$paymentLink}\n";
        }

        $mail->isHTML(true);
        $mail->Subject = "You Are Officially Enrolled";
        $mail->Body    = "Hi {$studentName},<br><br>Congratulations, you are now officially enrolled at Masinag Senior High School."
            . "<br><br>Your student number is <strong>{$studentNumber}</strong> and you have been assigned to section <strong>{$sectionName}</strong>."
            . "<br><br>Your full class schedule and registration form are still being finalized and will be sent to you separately once ready."
            . "<br><br>{$credentialsBlockHtml}{$paymentBlockHtml}<br>Thank you for your patience.";
        $mail->AltBody = "Hi {$studentName}, you are officially enrolled. Student number: {$studentNumber}. Section: {$sectionName}. "
            . "Your class schedule and registration form will be sent separately once ready."
            . $credentialsBlockText
            . $paymentBlockText;

        $mail->send();
        return true;

    } catch (Exception $e) {
        throw new Exception("Officially-enrolled email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}