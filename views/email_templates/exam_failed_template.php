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

                <h2 style="margin-top: 0; color: #b91c1c; font-size: 30px; text-align: center;">
                    Entrance Examination Result
                </h2>

                <p style="font-size: 17px; color: #374151; line-height: 1.8;">
                    Good day, <strong><?= htmlspecialchars($studentName) ?></strong>,
                </p>

                <p style="font-size: 16px; color: #374151; line-height: 1.8;">
                    Thank you for taking the Entrance Examination for
                    <strong>Masinag Senior High School.</strong>
                </p>

                <p style="font-size: 16px; color: #374151; line-height: 1.8;">
                    After carefully evaluating your examination results, we regret to inform you that your application
                    <strong style="color: #dc2626;">did not meet the required passing score</strong>
                    for this admission period.
                </p>

                <p style="font-size: 16px; color: #374151; line-height: 1.8;">
                    Although your application was not successful during this admission period, we sincerely appreciate your interest in becoming part of Masinag Senior High School.
                </p>

                <!-- STATUS -->
                <div style="margin: 35px 0; padding: 20px; background: #ecfdf5; border-left: 6px solid #dc2626; border-radius: 10px;">
                    <h3 style="margin-top: 0; color: #991b1b;">
                        Application Status
                    </h3>
                    <p style="margin-bottom: 0; font-size: 16px;">
                        <strong>Failed Entrance Examination</strong>
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

                <div style="margin-top:35px;padding:20px;background:#fff7ed;border-left:6px solid #f97316;border-radius:10px;">
                    <h3 style="margin-top:0;color:#9a3412;">
                        Entrance Examination Result
                    </h3>
                    <table width="100%" cellpadding="5">
                        <tr>
                            <td style="font-weight:bold; color: #374151;">
                                Average Score
                            </td>

                            <td style="text-align: right; font-size: 22px; font-weight:bold; color: #ea580c;">
                                <?= number_format($average,2) ?>%
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- NEXT STEP -->
                <div style="margin-top: 35px; padding: 25px; background: #eff6ff; border-radius: 10px;">
                    <h3 style="margin-top: 0; color: #0B1F4D;">
                        What You Can Do Next
                    </h3>

                    <ul style="padding-left: 20px; color: #374151; line-height: 2; margin-bottom: 0;">
                        <li>You may contact the Admissions Office for further clarification regarding your application.</li>
                        <li>You are welcome to apply again during future admission periods if eligible.</li>
                        <li>Thank you for considering Masinag Senior High School.</li>
                    </ul>
                </div>

                <!-- REMINDER -->
                <div style="margin-top: 40px; padding: 20px; background: #eef6ff; border-left: 5px solid #2563eb; border-radius: 10px;">
                    <strong style="color: #1d4ed8;">
                        Encouragement
                    </strong>

                    <p style="margin-bottom: 0; color: #444; line-height: 1.8; margin-top: 8px;">
                        Although your application was not successful during this admission period,
                        we encourage you to continue pursuing your educational goals.
                        We sincerely appreciate your interest in becoming part of the
                        <strong>Masinag Senior High School</strong> community and wish you success in your future academic journey.
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