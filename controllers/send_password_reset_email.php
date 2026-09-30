<?php

require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendPasswordResetEmail($adminEmail, $username, $resetLink) {

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
        $mail->addAddress($adminEmail, $username);

        $mail->isHTML(true);
        $mail->Subject = "Password Reset Request";
        $mail->Body    = "Hi {$username},<br><br>We received a request to reset your admin account password. Click the link below to set a new password. This link expires in 30 minutes and can only be used once.<br><br><a href=\"{$resetLink}\">{$resetLink}</a><br><br>If you did not request this, you can safely ignore this email — your password will remain unchanged.";
        $mail->AltBody = "Hi {$username}, reset your password using this link (expires in 30 minutes): {$resetLink}. If you did not request this, ignore this email.";

        $mail->send();
        return true;

    } catch (Exception $e) {
        throw new Exception("Password reset email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}