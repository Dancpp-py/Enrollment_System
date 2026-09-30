<?php

require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendExamFailedEmail(
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

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'payrollmngmnt00@gmail.com';
        $mail->Password   = 'zngf jdza jgbt ihbf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('payrollmngmnt00@gmail.com', 'Masinag Senior High School');
        $mail->addAddress($studentEmail, $studentName);

        ob_start();

        include "../views/email_templates/exam_failed_template.php";

        $emailBody = ob_get_clean();

        $mail->isHTML(true);
        $mail->Subject = "Entrance Examination Result";
        $mail->Body = $emailBody;

        $mail->AltBody =
        "Dear {$studentName},

        Thank you for taking the Entrance Examination.

        Unfortunately, your application did not meet the required passing score for this admission period.

        Application Type : {$applicationType}
        Grade Level      : {$gradeLevel}
        Preferred Strand : {$strand}
        School Year      : {$schoolYear}
        Average Score    : {$average}

        Thank you for considering Masinag Senior High School.";

        $mail->send();

        return true;

    } catch (Exception $e) {

        throw new Exception(
            "Exam-failed email could not be sent. Mailer Error: {$mail->ErrorInfo}"
        );

    }

}