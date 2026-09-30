<?php

require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendProfessorInviteEmail($professorEmail, $professorName, $username, $rawToken) {

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
   $setupLink = "{$protocol}://{$_SERVER['HTTP_HOST']}/enrollment_system/views/professor/professor-set-password.php?token={$rawToken}";

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
        $mail->addAddress($professorEmail, $professorName);

        $mail->isHTML(true);
        $mail->Subject = "Set Up Your Teacher Account";
        $mail->Body    = "Hi {$professorName},<br><br>"
            . "An account has been created for you at Masinag Senior High School.<br><br>"
            . "Your username is: <strong>{$username}</strong><br><br>"
            . "Please click the link below to set your password and activate your account. This link expires in 24 hours:<br><br>"
            . "<a href=\"{$setupLink}\">{$setupLink}</a><br><br>"
            . "If you did not expect this email, you can safely ignore it.<br><br>"
            . "Thank you!";
        $mail->AltBody = "Hi {$professorName}, an account has been created for you. "
            . "Username: {$username}. Set your password here (expires in 24 hours): {$setupLink}";

        $mail->send();
        return true;

    } catch (Exception $e) {
        throw new Exception("Invite email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}