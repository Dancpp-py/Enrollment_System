<?php include '../views/includes/header.php'; ?>


<div class="container">
        <div class="card">
            <div class="header">
                <div class="flex-header">
                    <div class="center">
                        <div class="small">Republic of the Philippines</div>
                        <h1>Masinag Senior High School</h1>
                        <div>Senior High School Enrollment Office</div>
                        <div style="font-size:13px;color:#666">School Address • Contact Number • Email Address</div>
                    </div>
                </div>
            </div>

            <div class="content">

                <div class="title">
                    <h2>NOTICE OF ENTRANCE EXAMINATION</h2>
                    <div class="line"></div>
                </div>

                <p>Dear <strong><?= htmlspecialchars($studentName) ?></strong>,</p>
                <p>Greetings! We are pleased to inform you that your application has been successfully reviewed and verified. You are hereby scheduled to take the Senior High School Entrance Examination.</p>

                <div class="box">
                    <div class="boxhead">Entrance Examination Details</div>

                    <div class="boxbody">
                        <table>
                            <tr>
                                <td class=".td-exam">Exam ID</td>
                                <td class=".td-exam"><?= htmlspecialchars($examNumber)?></td>
                            </tr>
                            <tr>
                                <td class=".td-exam">Examination Date</td>
                                <td class=".td-exam">June 26, 2026</td>
                            </tr>
                            <tr>
                                <td class=".td-exam">Time</td>
                                <td class=".td-exam">08:00pm</td>
                            </tr>
                            <tr>
                                <td class=".td-exam">Venue</td>
                                <td class=".td-exam">Gymnasium 1</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="rem">
                    <strong>Examination Reminders</strong>
                    <ul>
                        <li>Bring two sharpened pencils with eraser.</li>
                        <li>Present one valid school or government-issued ID.</li>
                        <li>Arrive at least 30 minutes before the examination.</li>
                        <li>Failure to attend may result in cancellation of your application.</li>
                    </ul>
                </div>

                <p>We wish you the best of luck and look forward to welcoming you to our institution.</p>

                <div class="footer">
                    <div>
                        <strong>Admissions Office</strong><br>
                        <span style="font-size:14px;color:#666">Senior High School Enrollment Committee</span>
                    </div>
                    <div class="sign">
                        <strong>Admissions Officer</strong>
                    </div>
                </div>
            </div>

            <div class="bottom">
                This is a system-generated notice from the Senior High School Enrollment System. No signature is required.
            </div>
        </div>
    </div>