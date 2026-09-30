<?php
require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMissingRequirementsEmail($studentEmail, $studentName, array $missingDocs) {
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

        $listItemsHtml = '';
        $listItemsText = '';
        foreach ($missingDocs as $doc) {
            $listItemsHtml .= "<li>" . htmlspecialchars($doc) . "</li>";
            $listItemsText .= "- {$doc}\n";
        }

        $mail->isHTML(true);
        $mail->Subject = "Action Required: Missing Enrollment Requirements";
        $mail->Body    = "Hi {$studentName},<br><br>"
            . "Our records show that your enrollment application is still missing the following requirement(s):<br>"
            . "<ul>{$listItemsHtml}</ul>"
            . "Please submit the missing document(s) as soon as possible so we can proceed with processing your application.<br><br>"
            . "Thank you!";
        $mail->AltBody = "Hi {$studentName}, your enrollment application is missing the following requirement(s):\n\n"
            . "{$listItemsText}\nPlease submit the missing document(s) as soon as possible.";

        $mail->send();
        return true;

    } catch (Exception $e) {
        throw new Exception("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}