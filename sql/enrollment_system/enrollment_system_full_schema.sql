-- =========================================================
-- enrollment_system — FULL SCHEMA (finalized)
-- Original tables preserved as-is.
-- New columns/tables clearly marked with comments.
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ---------------------------------------------------------
-- EXISTING TABLES (unchanged structure, unless noted)
-- ---------------------------------------------------------

-- admins (MODIFIED: added `role` column)
CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `role` enum('Registrar','Admissions','Super Admin') NOT NULL DEFAULT 'Super Admin', -- NEW
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- curriculum (unchanged)
CREATE TABLE `curriculum` (
  `curriculum_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `grade_level` enum('Grade 11','Grade 12') NOT NULL,
  `semester` enum('1st Semester','2nd Semester') NOT NULL,
  PRIMARY KEY (`curriculum_id`),
  UNIQUE KEY `subject_id` (`subject_id`,`strand_id`,`grade_level`,`semester`),
  KEY `strand_id` (`strand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- educational_background (unchanged)
CREATE TABLE `educational_background` (
  `education_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `elementary_school` varchar(150) NOT NULL,
  `elementary_year` year(4) NOT NULL,
  `junior_high_school` varchar(150) NOT NULL,
  `junior_high_year` year(4) NOT NULL,
  `senior_high_school` varchar(150) DEFAULT NULL,
  `senior_high_year` year(4) DEFAULT NULL,
  `lrn` varchar(20) NOT NULL,
  PRIMARY KEY (`education_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- enrollments (MODIFIED: added `stage` and `section_id`)
CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `grade_level` enum('Grade 11','Grade 12') NOT NULL,
  `semester` enum('1st Semester','2nd Semester') NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `status` enum('Pending','Confirmed','Rejected') NOT NULL DEFAULT 'Pending',
  `stage` enum('Application Review','Exam Scheduled','Exam Scored','Enrollment Review','Enrolled') NOT NULL DEFAULT 'Enrollment Review', -- NEW
  `section_id` int(11) DEFAULT NULL, -- NEW
  PRIMARY KEY (`enrollment_id`),
  KEY `student_id` (`student_id`),
  KEY `strand_id` (`strand_id`),
  KEY `school_year_id` (`school_year_id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- school_years (unchanged)
CREATE TABLE `school_years` (
  `school_year_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_year` varchar(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`school_year_id`),
  UNIQUE KEY `school_year` (`school_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- strands (unchanged)
CREATE TABLE `strands` (
  `strand_id` int(11) NOT NULL AUTO_INCREMENT,
  `strand_name` varchar(50) NOT NULL,
  PRIMARY KEY (`strand_id`),
  UNIQUE KEY `strand_name` (`strand_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- students (MODIFIED: added `exam_number`)
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_number` varchar(20) DEFAULT NULL,
  `exam_number` varchar(20) DEFAULT NULL, -- NEW
  `student_type` enum('New','Transferee','Existing') NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `student_number` (`student_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- student_family_members (unchanged)
CREATE TABLE `student_family_members` (
  `family_member_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `relationship` enum('Father','Mother','Legal Guardian','Grandfather','Grandmother','Brother','Sister','Uncle','Aunt','Relative','Other') NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `contact_number` varchar(20) NOT NULL,
  `address` text NOT NULL,
  PRIMARY KEY (`family_member_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- subjects (unchanged)
CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `units` int(11) NOT NULL,
  PRIMARY KEY (`subject_id`),
  UNIQUE KEY `subject_code` (`subject_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- NEW TABLES
-- ---------------------------------------------------------

-- entrance_exam_results
CREATE TABLE `entrance_exam_results` (
  `exam_result_id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL,
  `math_score` int(11) DEFAULT NULL,
  `english_score` int(11) DEFAULT NULL,
  `filipino_score` int(11) DEFAULT NULL,
  `science_score` int(11) DEFAULT NULL,
  `exam_status` enum('Pending','Passed','Failed') NOT NULL DEFAULT 'Pending',
  `scored_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`exam_result_id`),
  UNIQUE KEY `enrollment_id` (`enrollment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- sections
CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `strand_id` int(11) NOT NULL,
  `grade_level` enum('Grade 11','Grade 12') NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 40,
  PRIMARY KEY (`section_id`),
  UNIQUE KEY `strand_grade_year_name` (`strand_id`,`grade_level`,`school_year_id`,`section_name`),
  KEY `school_year_id` (`school_year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- professors
CREATE TABLE `professors` (
  `professor_id` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`professor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- rooms
CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_name` varchar(50) NOT NULL,
  PRIMARY KEY (`room_id`),
  UNIQUE KEY `room_name` (`room_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- class_schedules
CREATE TABLE `class_schedules` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `curriculum_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`schedule_id`),
  KEY `curriculum_id` (`curriculum_id`),
  KEY `section_id` (`section_id`),
  KEY `professor_id` (`professor_id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- enrollment_documents
-- document_type expected values (enforced in PHP, not DB):
--   'PSA Birth Certificate'              (required, all students)
--   'Grade 10 Report Card (Form 138)'    (required, all students)
--   'Certificate of Good Moral'          (required, all students)
--   'Recent 2x2 ID Picture'              (required, all students)
--   'Transcript of Records / Form 137'   (required ONLY for Transferee student_type)
CREATE TABLE `enrollment_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`),
  KEY `enrollment_id` (`enrollment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- SEED DATA (matches your original dump; passwords/roles
-- should be updated per earlier discussion re: hashing)
-- ---------------------------------------------------------

INSERT INTO `admins` (`admin_id`, `username`, `role`, `password`) VALUES
(1, 'admin', 'Super Admin', 'admin123'); -- replace with password_hash() output before going live

INSERT INTO `school_years` (`school_year_id`, `school_year`, `is_active`) VALUES
(1, '2025-2026', 0),
(2, '2026-2027', 1);

INSERT INTO `strands` (`strand_id`, `strand_name`) VALUES
(2, 'ABM'),
(4, 'GAS'),
(3, 'HUMSS'),
(1, 'STEM'),
(5, 'TVL');

-- ---------------------------------------------------------
-- FOREIGN KEY CONSTRAINTS
-- (added after all tables exist, to avoid ordering issues)
-- ---------------------------------------------------------

ALTER TABLE `curriculum`
  ADD CONSTRAINT `curriculum_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`),
  ADD CONSTRAINT `curriculum_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`);

ALTER TABLE `educational_background`
  ADD CONSTRAINT `educational_background_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`),
  ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`school_year_id`),
  ADD CONSTRAINT `enrollments_ibfk_4` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`); -- NEW

ALTER TABLE `student_family_members`
  ADD CONSTRAINT `student_family_members_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

ALTER TABLE `entrance_exam_results` -- NEW TABLE
  ADD CONSTRAINT `entrance_exam_results_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE;

ALTER TABLE `sections` -- NEW TABLE
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`),
  ADD CONSTRAINT `sections_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`school_year_id`);

ALTER TABLE `class_schedules` -- NEW TABLE
  ADD CONSTRAINT `class_schedules_ibfk_1` FOREIGN KEY (`curriculum_id`) REFERENCES `curriculum` (`curriculum_id`),
  ADD CONSTRAINT `class_schedules_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`),
  ADD CONSTRAINT `class_schedules_ibfk_3` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`),
  ADD CONSTRAINT `class_schedules_ibfk_4` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

ALTER TABLE `enrollment_documents` -- NEW TABLE
  ADD CONSTRAINT `enrollment_documents_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE;

COMMIT;
