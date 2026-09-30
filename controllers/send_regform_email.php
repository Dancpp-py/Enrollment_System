<?php

require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendRegForm($studentEmail, $studentName, $enrollmentID, $studentNumber, $plainPassword = null, $paymentLink = null) {
    $pdfPath = APP_ROOT . "/generated_pdfs/registration_{$enrollmentID}.pdf";

    if (!file_exists($pdfPath)) {
        throw new Exception("Generated PDF not found for enrollment {$enrollmentID}.");
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

        $mail->addAttachment($pdfPath, "Registration_Form.pdf");

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
        $mail->Subject = "Your Certificate of Registration";
        $mail->Body    = "Hi {$studentName},<br><br>"
            . "Attached is your official Certificate of Registration. Please keep this for your records."
            . "<br><br>{$credentialsBlockHtml}{$paymentBlockHtml}<br>Thank you!";
        $mail->AltBody = "Hi {$studentName}, attached is your official Certificate of Registration."
            . $credentialsBlockText
            . $paymentBlockText;

        $mail->send();
        return true;

    } catch (Exception $e) {
        throw new Exception("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}