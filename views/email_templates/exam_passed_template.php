<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrance Examination Result</title>
</head>
<body style="margin: 0; padding: 30px; background: #f1f5f9; font-family: Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 700px; margin: auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #dbe4f0;">

        <!-- HEADER -->
        <tr>
            <td style="background: linear-gradient(90deg, #0B1F4D, #2563EB); padding: 35px; text-align: center;">
                <h1 style="margin: 20px 0 5px; color: #ffffff; font-size: 28px;">
                    Masinag Senior High School
                </h1>
                <p style="margin: 0; color: #dbeafe; font-size: 15px;">
                    Senior High School Enrollment Management System
                </p>
            </td>
        </tr>

        <!-- BODY -->
        <tr>
            <td style="padding: 45px;">

                <h2 style="margin-top: 0; color: #0B1F4D; font-size: 30px; text-align: center;">
                    Congratulations!
                </h2>

                <p style="font-size: 17px; color: #374151; line-height: 1.8;">
                    Good day, <strong><?= htmlspecialchars($studentName) ?></strong>!
                </p>

                <p style="font-size: 16px; color: #374151; line-height: 1.8;">
                    We are pleased to inform you that you have successfully
                    <strong style="color: #16a34a;">PASSED</strong>
                    the Entrance Examination of
                    <strong>Masinag Senior High School.</strong>
                </p>

                <p style="font-size: 16px; color: #374151; line-height: 1.8;">
                    Your application has now advanced to the
                    <strong style="color: #2563EB;">Official Enrollment Review</strong> stage.
                    Our Admissions and Registrar Office will now review your application before final approval.
                </p>

                <!-- STATUS -->
                <div style="margin: 35px 0; padding: 20px; background: #ecfdf5; border-left: 6px solid #22c55e; border-radius: 10px;">
                    <h3 style="margin-top: 0; color: #166534;">
                        Application Status
                    </h3>
                    <p style="margin-bottom: 0; font-size: 16px;">
                        🟢 <strong>Passed Entrance Examination</strong>
                    </p>
                </div>

                <!-- APPLICANT DETAILS -->
                <table width="100%" cellpadding="12" cellspacing="0" style="border-collapse: collapse; margin-top: 20px;">
                    <tr style="background: #0B1F4D; color: white;">
                        <th colspan="2" style="padding: 14px; text-align: left; font-size: 17px;">
                            Applicant Information
                        </th>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #dbe4f0; width: 40%; font-weight: bold; color: #374151;">
                            Applicant Name
                        </td>
                        <td style="border: 1px solid #dbe4f0; color: #374151;">
                            <?= htmlspecialchars($studentName) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #dbe4f0; font-weight: bold; color: #374151;">
                            Application Type
                        </td>
                        <td style="border: 1px solid #dbe4f0; color: #374151;">
                            <?= htmlspecialchars($applicationType) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #dbe4f0; font-weight: bold; color: #374151;">
                            Grade Level
                        </td>
                        <td style="border: 1px solid #dbe4f0; color: #374151;">
                            <?= htmlspecialchars($gradeLevel) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #dbe4f0; font-weight: bold; color: #374151;">
                            Preferred Strand
                        </td>
                        <td style="border: 1px solid #dbe4f0; color: #374151;">
                            <?= htmlspecialchars($strand) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #dbe4f0; font-weight: bold; color: #374151;">
                            School Year
                        </td>
                        <td style="border: 1px solid #dbe4f0; color: #374151;">
                            <?= htmlspecialchars($schoolYear) ?>
                        </td>
                    </tr>
                </table>

                <!-- EXAM RESULT -->
                <div style="margin-top:35px;padding:20px;background:#ecfdf5;border-left:6px solid #22c55e;border-radius:10px;">
                    <h3 style="margin-top:0;color:#166534;">
                        Entrance Examination Result
                    </h3>

                    <table width="100%" cellpadding="5">
                        <tr>
                            <td style="font-weight:bold;color:#374151;">
                                Average Score
                            </td>

                            <td style="text-align:right;font-size:22px;font-weight:bold;color:#16a34a;">
                                <?= number_format($average,2) ?>%
                            </td>
                        </tr>

                        <tr>
                            <td style="font-weight:bold;color:#374151;">
                                Result
                            </td>

                            <td style="text-align:right;font-weight:bold;color:#16a34a;">
                                PASSED
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- NEXT STEP -->
                <div style="margin-top: 35px; padding: 25px; background: #eff6ff; border-radius: 10px;">
                    <h3 style="margin-top: 0; color: #0B1F4D;">
                        📌 What's Next?
                    </h3>
                    <ul style="padding-left: 20px; color: #374151; line-height: 2; margin-bottom: 0;">
                        <li>Your application is now under Official Enrollment Review.</li>
                        <li>Please wait for another email regarding your enrollment result.</li>
                        <li>Prepare your original admission requirements.</li>
                        <li>Keep this email for future reference.</li>
                    </ul>
                </div>

                <!-- REMINDER -->
                <div style="margin-top: 40px; padding: 20px; background: #fefce8; border-left: 5px solid #facc15; border-radius: 10px;">
                    <strong style="color: #92400e;">Reminder</strong>
                    <p style="margin-bottom: 0; color: #444; line-height: 1.8; margin-top: 5px;">
                        Your enrollment is <strong>NOT YET FINAL.</strong>
                        Passing the entrance examination means your application has successfully moved to the enrollment evaluation stage.
                        Please wait for another email confirming your official enrollment.
                    </p>
                </div>

            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
            <td style="background: #0B1F4D; padding: 30px; text-align: center;">
                <p style="color: #ffffff; font-size: 18px; font-weight: bold; margin: 0;">
                    Masinag Senior High School
                </p>
                <p style="color: #cbd5e1; margin-top: 8px; font-size: 14px; margin-bottom: 0;">
                    Senior High School Enrollment Management System
                </p>
                <p style="color: #cbd5e1; font-size: 14px; margin-top: 25px; line-height: 1.6;">
                    📧 admissions@masinagshs.edu.ph
                    <br><br>
                    This is an automated email. Please do not reply to this message.
                </p>
                <p style="color: #94a3b8; font-size: 12px; margin-top: 25px; margin-bottom: 0;">
                    © 2026 Masinag Senior High School. All Rights Reserved.
                </p>
            </td>
        </tr>

    </table>

</body>
</html>