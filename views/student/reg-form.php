<?php include '../views/includes/header.php'; ?>

<main id="registration-form" class="regform-body">
    <div class="regform-wrapper">

        <!-- HEADER -->
        <div class="regform-header">
            <div class="regform-brand center">
                <div>
                    <div>Republic of the Philippines</div>
                    <h1>Masinag Senior High School</h1>
                    <div>Senior High School Enrollment System</div>
                    <h2 style="margin:5px 0 0;border:none;color:#fff;padding:0;"> OFFICIAL REGISTRATION FORM</h2>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="regform-content">

        <div class="regField">
            <!-- STUDENT INFORMATION -->
            <div class="regform-field">
                <label>Student Name</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars("{$student['last_name']}, {$student['first_name']} {$student['middle_name']} {$student['suffix']}") ?>"
                    readonly>
            </div>

            <div class="regform-field">
                <label>Student Number</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($student['student_number']) ?>"
                    readonly>
            </div>

            <div class="regform-field">
                <label>Grade Level</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($student['grade_level']) ?>"
                    readonly>
            </div>

            <div class="regform-field">
                <label>Strand</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($student['strand_name']) ?>"
                    readonly>
            </div>

            <div class="regform-field">
                <label>Section</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($student['section_name']) ?>"
                    readonly>
            </div>

            <div class="regform-field">
                <label>School Year</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($student['school_year']) ?>"
                    readonly>
            </div>
        </div>

            <!-- SUBJECTS -->
            <h2>Registered Subjects</h2>

            <table class="regform-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Subject</th>
                        <th>Units</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($subjects as $subj): ?>
                        <tr>
                            <td><?= htmlspecialchars($subj['subject_code']) ?></td>
                            <td><?= htmlspecialchars($subj['subject_name']) ?></td>
                            <td><?= htmlspecialchars($subj['units']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- CLASS SCHEDULE -->
            <h2>Class Schedule</h2>

            <table class="regform-table">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Subject</th>
                        <th>Room</th>
                        <th>Teacher</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($schedule)): ?>

                        <!-- Only happens if there are literally no curriculum
                             subjects for this strand/grade/semester at all —
                             not the "unscheduled" case anymore, that's handled
                             per-row below with TBA. -->
                        <tr>
                            <td colspan="5" style="text-align:center;">
                                No subjects found for this curriculum.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($schedule as $class): ?>

                            <tr>
                                <td>
                                    <?= $class['day_of_week']
                                        ? htmlspecialchars(str_replace(',', ', ', $class['day_of_week']))
                                        : 'TBA' ?>
                                </td>

                                <td>
                                    <?php if ($class['start_time'] && $class['end_time']): ?>
                                        <?= date("g:i A", strtotime($class['start_time'])) ?>
                                        -
                                        <?= date("g:i A", strtotime($class['end_time'])) ?>
                                    <?php else: ?>
                                        TBA
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($class['subject_name']) ?></td>

                                <td><?= $class['room_name'] ? htmlspecialchars($class['room_name']) : 'TBA' ?></td>

                                <td>
                                    <?= $class['prof_last_name']
                                        ? htmlspecialchars($class['prof_last_name'] . ', ' . $class['prof_first_name'])
                                        : 'TBA' ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>
            </table>

            <!-- ASSESSMENT -->
            <h2>Assessment Fee Breakdown</h2>

            <table class="regform-table">

                <thead>
                    <tr>
                        <th>Fee</th>
                        <th width="180">Amount</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>Tuition Fee</td>
                        <td class="amount">01.00</td>
                    </tr>

                    <tr>
                        <td>Laboratory Fee</td>
                        <td class="amount">00.00</td>
                    </tr>

                    <tr>
                        <td>Miscellaneous Fee</td>
                        <td class="amount">00.00</td>
                    </tr>

                    <tr>
                        <td>LMS</td>
                        <td class="amount">00.00</td>
                    </tr>

                    <tr>
                        <td>NSTP/ROTC</td>
                        <td class="amount">00.00</td>
                    </tr>

                </tbody>

                <tfoot>

                    <tr class="scf-total">
                        <td>TOTAL CONTRIBUTIONS</td>
                        <td class="amount">01.00</td>
                    </tr>

                </tfoot>

            </table>

            <!-- REMINDER -->
            <div class="regform-note">

                <strong>Important Reminder</strong>

                <ul>
                    <li>This serves as your temporary Registration Form.</li>
                    <li>Bring a printed copy during school opening.</li>
                    <li>Contact the Admissions Office for corrections.</li>
                </ul>

            </div>

            <!-- SIGNATURE -->
            <div class="regform-footer">

                <div class="regform-sign">
                    Admissions Officer
                </div>

                <div class="regform-sign">
                    Registrar
                </div>

            </div>

        </div>
        <!-- END regform-content -->

        <div class="regform-bottom">
            Generated by the Senior High School Enrollment System
        </div>

    </div>
</main>