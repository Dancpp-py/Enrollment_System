-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2026 at 04:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `enrollment_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('Registrar','Admissions','Scheduler','Super Admin') NOT NULL DEFAULT 'Super Admin',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `email`, `role`, `is_active`, `password`) VALUES
(1, 'admin', NULL, 'Super Admin', 1, '$2y$10$Txso8SZgCVMnhWfAL92WH.zvKMDsmmuAlgWTtg.Rgs6NRQE81OIJe'),
(2, 'noah', 'pascuanoah@gmail.com', 'Registrar', 1, '$2y$10$J.als4DzvtoTwVTzwMqmnuodZeCGyFJ1DwIcIYEWpoWx9P3a7.CN2'),
(3, 'demi', 'centperaman4@gmail.com', 'Admissions', 1, '$2y$10$9r2Aa6aRLxmIhGZRwisAOOERt9zLiPu5MdQgHOXxH8.n9VQ3h8uIi'),
(4, 'dandi', 'kuyadani26@gmail.com', 'Scheduler', 1, '$2y$10$DnqYHUD3GauQ88RA.7IHZ.JQtLObCcuwYMvbBhi5RbrCFfW4QXBTS');

-- --------------------------------------------------------

--
-- Table structure for table `admin_password_resets`
--

CREATE TABLE `admin_password_resets` (
  `reset_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_schedules`
--

CREATE TABLE `class_schedules` (
  `schedule_id` int(11) NOT NULL,
  `curriculum_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `day_of_week` varchar(40) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `curriculum`
--

CREATE TABLE `curriculum` (
  `curriculum_id` int(11) NOT NULL,
  `curriculum_version_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `grade_level` enum('Grade 11','Grade 12') NOT NULL,
  `semester` enum('1st Semester','2nd Semester') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_versions`
--

CREATE TABLE `curriculum_versions` (
  `curriculum_version_id` int(11) NOT NULL,
  `version_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_versions`
--

INSERT INTO `curriculum_versions` (`curriculum_version_id`, `version_name`, `created_at`) VALUES
(1, '2026-2027 SHS Curriculum', '2026-09-15 16:05:50'),
(2, '2025-2026 SHS Curriculum', '2026-09-15 16:47:19');

-- --------------------------------------------------------

--
-- Table structure for table `educational_background`
--

CREATE TABLE `educational_background` (
  `education_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `elementary_school` varchar(150) NOT NULL,
  `elementary_year` year(4) NOT NULL,
  `junior_high_school` varchar(150) NOT NULL,
  `junior_high_year` year(4) NOT NULL,
  `senior_high_school` varchar(150) DEFAULT NULL,
  `senior_high_year` year(4) DEFAULT NULL,
  `lrn` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `educational_background`
--

INSERT INTO `educational_background` (`education_id`, `student_id`, `elementary_school`, `elementary_year`, `junior_high_school`, `junior_high_year`, `senior_high_school`, `senior_high_year`, `lrn`) VALUES
(1, 1, 'Animi quas iure qui', '2020', 'Ut impedit distinct', '2024', '', NULL, '111111111111'),
(2, 2, 'Fuga Dolor nemo ut', '2020', 'Laboriosam repellen', '2024', '', NULL, '222222222222'),
(3, 3, 'Qui distinctio Comm', '2020', 'Deleniti dolores in', '2024', '', NULL, '333333333333'),
(4, 4, 'Eaque pariatur Cons', '2020', 'Quas dolor aut saepe', '2024', '', NULL, '444444444443'),
(5, 5, 'Provident nihil sun', '2020', 'Consequatur veniam', '2024', '', NULL, '555555555555');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `grade_level` enum('Grade 11','Grade 12') NOT NULL,
  `semester` enum('1st Semester','2nd Semester') NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `status` enum('Pending','Confirmed','Rejected') NOT NULL DEFAULT 'Pending',
  `stage` enum('Application Review','Exam Scheduled','Exam Scored','Enrollment Review','Enrolled') NOT NULL DEFAULT 'Enrollment Review',
  `section_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `strand_id`, `grade_level`, `semester`, `school_year_id`, `status`, `stage`, `section_id`) VALUES
(1, 1, 4, 'Grade 11', '1st Semester', 2, 'Pending', 'Application Review', NULL),
(2, 2, 3, 'Grade 11', '1st Semester', 2, 'Pending', 'Application Review', NULL),
(3, 3, 5, 'Grade 11', '1st Semester', 2, 'Pending', 'Application Review', NULL),
(4, 4, 1, 'Grade 11', '1st Semester', 2, 'Pending', 'Application Review', NULL),
(5, 5, 2, 'Grade 11', '1st Semester', 2, 'Pending', 'Application Review', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_documents`
--

CREATE TABLE `enrollment_documents` (
  `document_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment_documents`
--

INSERT INTO `enrollment_documents` (`document_id`, `enrollment_id`, `document_type`, `file_path`, `uploaded_at`) VALUES
(1, 1, 'PSA Birth Certificate', 'uploads/documents/1_PSA_Birth_Certificate_1789827575.jpg', '2026-09-19 14:19:35'),
(2, 1, 'Grade 10 Report Card (Form 138)', 'uploads/documents/1_Grade_10_Report_Card__Form_138__1789827575.png', '2026-09-19 14:19:35'),
(3, 1, 'Certificate of Good Moral', 'uploads/documents/1_Certificate_of_Good_Moral_1789827575.jpg', '2026-09-19 14:19:35'),
(4, 1, 'Recent 2x2 ID Picture', 'uploads/documents/1_Recent_2x2_ID_Picture_1789827575.jpg', '2026-09-19 14:19:35'),
(5, 2, 'PSA Birth Certificate', 'uploads/documents/2_PSA_Birth_Certificate_1789827638.jpg', '2026-09-19 14:20:38'),
(6, 2, 'Grade 10 Report Card (Form 138)', 'uploads/documents/2_Grade_10_Report_Card__Form_138__1789827638.png', '2026-09-19 14:20:38'),
(7, 2, 'Certificate of Good Moral', 'uploads/documents/2_Certificate_of_Good_Moral_1789827638.jpg', '2026-09-19 14:20:38'),
(8, 2, 'Recent 2x2 ID Picture', 'uploads/documents/2_Recent_2x2_ID_Picture_1789827638.jpg', '2026-09-19 14:20:38'),
(9, 3, 'PSA Birth Certificate', 'uploads/documents/3_PSA_Birth_Certificate_1789827673.jpg', '2026-09-19 14:21:13'),
(10, 3, 'Grade 10 Report Card (Form 138)', 'uploads/documents/3_Grade_10_Report_Card__Form_138__1789827673.png', '2026-09-19 14:21:13'),
(11, 3, 'Certificate of Good Moral', 'uploads/documents/3_Certificate_of_Good_Moral_1789827673.jpg', '2026-09-19 14:21:13'),
(12, 3, 'Recent 2x2 ID Picture', 'uploads/documents/3_Recent_2x2_ID_Picture_1789827673.jpg', '2026-09-19 14:21:13'),
(13, 4, 'PSA Birth Certificate', 'uploads/documents/4_PSA_Birth_Certificate_1789827717.jpg', '2026-09-19 14:21:57'),
(14, 4, 'Grade 10 Report Card (Form 138)', 'uploads/documents/4_Grade_10_Report_Card__Form_138__1789827717.png', '2026-09-19 14:21:57'),
(15, 4, 'Certificate of Good Moral', 'uploads/documents/4_Certificate_of_Good_Moral_1789827717.jpg', '2026-09-19 14:21:57'),
(16, 4, 'Recent 2x2 ID Picture', 'uploads/documents/4_Recent_2x2_ID_Picture_1789827717.jpg', '2026-09-19 14:21:57'),
(17, 5, 'PSA Birth Certificate', 'uploads/documents/5_PSA_Birth_Certificate_1789827758.jpg', '2026-09-19 14:22:38'),
(18, 5, 'Grade 10 Report Card (Form 138)', 'uploads/documents/5_Grade_10_Report_Card__Form_138__1789827758.png', '2026-09-19 14:22:38'),
(19, 5, 'Certificate of Good Moral', 'uploads/documents/5_Certificate_of_Good_Moral_1789827758.jpg', '2026-09-19 14:22:38'),
(20, 5, 'Recent 2x2 ID Picture', 'uploads/documents/5_Recent_2x2_ID_Picture_1789827758.jpg', '2026-09-19 14:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_payments`
--

CREATE TABLE `enrollment_payments` (
  `payment_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `paymongo_link_id` varchar(50) NOT NULL,
  `checkout_url` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL COMMENT 'in centavos',
  `status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entrance_exam_results`
--

CREATE TABLE `entrance_exam_results` (
  `exam_result_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `math_score` int(11) DEFAULT NULL,
  `english_score` int(11) DEFAULT NULL,
  `filipino_score` int(11) DEFAULT NULL,
  `science_score` int(11) DEFAULT NULL,
  `average_score` decimal(5,2) DEFAULT NULL,
  `exam_status` enum('Pending','Passed','Failed') NOT NULL DEFAULT 'Pending',
  `scored_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `first_quarter` decimal(5,2) DEFAULT NULL,
  `second_quarter` decimal(5,2) DEFAULT NULL,
  `final_grade` decimal(5,2) DEFAULT NULL,
  `remarks` enum('Passed','Failed','Incomplete','Dropped') DEFAULT NULL,
  `encoded_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grading_periods`
--

CREATE TABLE `grading_periods` (
  `grading_period_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `semester` enum('1st Semester','2nd Semester') NOT NULL,
  `quarter` enum('First Quarter','Second Quarter') NOT NULL,
  `opens_at` datetime NOT NULL,
  `closes_at` datetime NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grading_periods`
--

INSERT INTO `grading_periods` (`grading_period_id`, `school_year_id`, `semester`, `quarter`, `opens_at`, `closes_at`, `is_open`, `created_at`) VALUES
(1, 2, '1st Semester', 'First Quarter', '2026-06-01 00:00:00', '2027-05-31 23:59:59', 1, '2026-09-16 14:47:50'),
(2, 2, '1st Semester', 'Second Quarter', '2026-06-01 00:00:00', '2027-05-31 23:59:59', 1, '2026-09-16 14:47:50'),
(3, 2, '2nd Semester', 'First Quarter', '2026-06-01 00:00:00', '2027-05-31 23:59:59', 1, '2026-09-16 14:47:50'),
(4, 2, '2nd Semester', 'Second Quarter', '2026-06-01 00:00:00', '2027-05-31 23:59:59', 1, '2026-09-16 14:47:50');

-- --------------------------------------------------------

--
-- Table structure for table `lms_classes`
--

CREATE TABLE `lms_classes` (
  `lms_class_id` int(10) UNSIGNED NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `status` enum('Active','Inactive','Archived') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_materials`
--

CREATE TABLE `lms_materials` (
  `material_id` int(10) UNSIGNED NOT NULL,
  `lms_class_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `original_file_name` varchar(255) NOT NULL,
  `stored_file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` bigint(20) UNSIGNED NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `professors`
--

CREATE TABLE `professors` (
  `professor_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`professor_id`, `username`, `last_name`, `first_name`, `middle_name`, `contact_number`, `email`, `password`, `is_active`) VALUES
(1, NULL, 'Santos', 'Juan', 'A', '09170000001', 'juan.santos@school.edu', NULL, 1),
(2, NULL, 'Reyes', 'Maria', 'B', '09170000002', 'maria.reyes@school.edu', NULL, 1),
(3, NULL, 'Cruz', 'Pedro', 'C', '09170000003', 'pedro.cruz@school.edu', NULL, 1),
(4, NULL, 'Garcia', 'Ana', 'D', '09170000004', 'ana.garcia@school.edu', NULL, 1),
(5, NULL, 'Lopez', 'Mark', 'E', '09170000005', 'mark.lopez@school.edu', NULL, 1),
(6, NULL, 'Torres', 'Grace', 'F', '09170000006', 'grace.torres@school.edu', NULL, 1),
(7, NULL, 'Ramos', 'Jose', 'G', '09170000007', 'jose.ramos@school.edu', NULL, 1),
(8, NULL, 'Flores', 'Liza', 'H', '09170000008', 'liza.flores@school.edu', NULL, 1),
(9, NULL, 'Rivera', 'Paolo', 'I', '09170000009', 'paolo.rivera@school.edu', NULL, 1),
(10, NULL, 'Aquino', 'Carla', 'J', '09170000010', 'carla.aquino@school.edu', NULL, 0),
(11, NULL, 'Fernandez', 'Leo', 'K', '09170000011', 'leo.fernandez@school.edu', NULL, 1),
(12, NULL, 'Mendoza', 'Kim', 'L', '09170000012', 'kim.mendoza@school.edu', NULL, 1),
(13, NULL, 'Navarro', 'Brian', 'M', '09170000013', 'brian.navarro@school.edu', NULL, 0),
(14, NULL, 'Morales', 'Janice', 'N', '09170000014', 'janice.morales@school.edu', NULL, 0),
(15, NULL, 'Domingo', 'Ralph', 'O', '09170000015', 'ralph.domingo@school.edu', NULL, 1),
(16, NULL, 'Castro', 'Ella', 'P', '09170000016', 'ella.castro@school.edu', NULL, 1),
(17, NULL, 'Bautista', 'Neil', 'Q', '09170000017', 'neil.bautista@school.edu', NULL, 1),
(18, NULL, 'Dela Cruz', 'Rose', 'R', '09170000018', 'rose.delacruz@school.edu', NULL, 1),
(19, NULL, 'Lim', 'Kevin', 'S', '09170000019', 'kevin.lim@school.edu', NULL, 1),
(20, NULL, 'Tan', 'Sophia', 'T', '09170000020', 'sophia.tan@school.edu', NULL, 1),
(21, 'sheriffwoody.pride', 'Pride', 'Sheriff Woody', '', '09278682456', 'sarino.charlesmichael@ncst.edu.ph', '$2y$10$u.F8fuS6kOJRxfBp7dYtEOXV.b4tTf33uZ3x9i3Kj9Ml/LNOqQvdO', 1);

-- --------------------------------------------------------

--
-- Table structure for table `professor_password_resets`
--

CREATE TABLE `professor_password_resets` (
  `reset_id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_name`) VALUES
(15, 'Accounting Room'),
(16, 'AVR'),
(11, 'Computer Lab 1'),
(12, 'Computer Lab 2'),
(19, 'Engineering Lab'),
(18, 'Gymnasium'),
(17, 'Library'),
(1, 'Room 101'),
(2, 'Room 102'),
(3, 'Room 103'),
(4, 'Room 104'),
(5, 'Room 105'),
(6, 'Room 106'),
(7, 'Room 107'),
(8, 'Room 108'),
(9, 'Room 109'),
(10, 'Room 110'),
(13, 'Science Lab 1'),
(14, 'Science Lab 2'),
(20, 'Workshop');

-- --------------------------------------------------------

--
-- Table structure for table `school_years`
--

CREATE TABLE `school_years` (
  `school_year_id` int(11) NOT NULL,
  `school_year` varchar(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `curriculum_version_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_years`
--

INSERT INTO `school_years` (`school_year_id`, `school_year`, `is_active`, `curriculum_version_id`) VALUES
(1, '2025-2026', 0, 2),
(2, '2026-2027', 1, 1),
(3, '2027-2028', 0, NULL),
(4, '2028-2029', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `grade_level` enum('Grade 11','Grade 12') NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 40,
  `room_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `strands`
--

CREATE TABLE `strands` (
  `strand_id` int(11) NOT NULL,
  `strand_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `strands`
--

INSERT INTO `strands` (`strand_id`, `strand_name`) VALUES
(2, 'ABM'),
(4, 'GAS'),
(3, 'HUMSS'),
(1, 'STEM'),
(5, 'TVL');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `student_number` varchar(20) DEFAULT NULL,
  `exam_number` varchar(20) DEFAULT NULL,
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
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_number`, `exam_number`, `student_type`, `last_name`, `first_name`, `middle_name`, `suffix`, `birth_date`, `gender`, `contact_number`, `email`, `address`, `password`) VALUES
(1, NULL, NULL, 'New', 'Martin', 'Katelyn Salas', '', 'Vel dolore dolor off', '1996-09-22', 'Male', '09278682456', 'charles.michael112@gmail.com', 'Quo non proident co', NULL),
(2, NULL, NULL, 'New', 'Spence', 'Oliver Franks', 'Zorita Ray', 'Aliquam et incidunt', '1995-05-02', 'Male', '09278682456', 'maddoto112@gmail.com', 'Consectetur ullamco', NULL),
(3, NULL, NULL, 'New', 'Doyle', 'Faith Mckee', 'Tamara Pittman', 'Ad nihil exercitatio', '1978-04-25', 'Male', '09278682456', 'chrlznft@gmail.com', 'Ullamco qui doloribu', NULL),
(4, NULL, NULL, 'New', 'James', 'Macon Wall', 'Rebecca Hartman', 'Ex optio sint ad i', '1987-07-31', 'Female', '09278682456', 'centperaman4@gmail.com', 'Consectetur et fugi', NULL),
(5, NULL, NULL, 'New', 'Chastity Meyer', 'Finn Delacruz', 'Hermione Roach', 'Consequat Dolor ali', '2007-03-17', 'Male', '09278682456', 'pascuanoah@gmail.com', 'Non et a voluptatibu', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_family_members`
--

CREATE TABLE `student_family_members` (
  `family_member_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `relationship` enum('Father','Mother','Legal Guardian','Grandfather','Grandmother','Brother','Sister','Uncle','Aunt','Relative','Other') NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `contact_number` varchar(20) NOT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_family_members`
--

INSERT INTO `student_family_members` (`family_member_id`, `student_id`, `relationship`, `last_name`, `first_name`, `middle_name`, `suffix`, `contact_number`, `address`) VALUES
(1, 1, 'Father', 'Wing Morin', 'Donna Blackwell', 'Sybill Torres', 'Veniam est nulla se', '09278682456', 'Qui corrupti necess'),
(2, 1, 'Mother', 'Joshua Cardenas', 'Plato Landry', 'Abdul Collins', 'Soluta sed cupidatat', '09278682456', 'Vero sunt occaecat'),
(3, 1, 'Father', 'Kaden Massey', 'Iliana Taylor', 'Roary Odom', '', '09278682456', 'Odit enim et sit qu'),
(4, 2, 'Father', 'Joshua Allison', 'Ingrid Erickson', 'Raya Bowers', 'Veritatis earum quae', '09278682456', 'Aliquid voluptatibus'),
(5, 2, 'Mother', 'Lillith Goff', 'Quin Butler', 'Vladimir Gill', 'Ipsam harum nulla as', '09278682456', 'Id assumenda non qui'),
(6, 2, 'Grandmother', 'Hakeem Davidson', 'Neville Cote', 'Sacha Rivers', '', '09278682456', 'Nulla dolores animi'),
(7, 3, 'Father', 'Blake Justice', 'Harlan Hammond', 'Marsden Woodward', 'Ullamco ad consectet', '09278682456', 'Et voluptatem Conse'),
(8, 3, 'Mother', 'Pearl Langley', 'Hamish Mcknight', 'Yoshio Alford', 'Unde ut dolor occaec', '09278682456', 'Excepteur nostrum qu'),
(9, 3, 'Sister', 'Halla Ewing', 'Arden Spence', 'Hillary Hodges', '', '09278682456', 'Sunt sint quaerat au'),
(10, 4, 'Father', 'Stephen Mullins', 'Hannah Graham', 'Ocean Bonner', 'Elit earum earum be', '09278682456', 'Qui delectus aute i'),
(11, 4, 'Mother', 'Michelle Peters', 'Omar Daugherty', 'Signe Snider', 'Magna ut dolorem qui', '09278682456', 'Doloribus exercitati'),
(12, 4, 'Father', 'Tatyana Richards', 'Shelley Ramsey', 'Kyra Melton', '', '09278682456', 'Tempor quam ea ut ad'),
(13, 5, 'Father', 'Herman Strickland', 'Ishmael Dominguez', 'Alea Montoya', 'Aliqua Recusandae', '09278682456', 'Vel excepteur qui ut'),
(14, 5, 'Mother', 'Celeste Walters', 'Avram Tucker', 'Declan Copeland', 'Unde illo lorem omni', '09278682456', 'Nulla inventore eius'),
(15, 5, 'Mother', 'Mari Holder', 'Amelia Lamb', 'Steven Terrell', '', '09278682456', 'Cupiditate unde beat');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `units` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `units`, `is_active`) VALUES
(1, 'OC', 'Oral Communication in Context', 3, 1),
(2, 'RW', 'Reading and Writing Skills', 3, 1),
(3, 'KPW', 'Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino', 3, 1),
(4, '21CL', '21st Century Literature from the Philippines and the World', 3, 1),
(5, 'CPAR', 'Contemporary Philippine Arts from the Regions', 3, 1),
(6, 'MIL', 'Media and Information Literacy', 2, 1),
(7, 'GM', 'General Mathematics', 3, 1),
(8, 'SP', 'Statistics and Probability', 3, 1),
(9, 'ELS', 'Earth and Life Science', 3, 1),
(10, 'PS', 'Physical Science', 3, 1),
(11, 'IPHP', 'Introduction to the Philosophy of the Human Person', 2, 1),
(12, 'PEH', 'Physical Education and Health', 2, 1),
(13, 'PD', 'Personal Development', 2, 1),
(14, 'UCSP', 'Understanding Culture, Society and Politics', 3, 1),
(15, 'PR1', 'Practical Research 1', 3, 1),
(16, 'PR2', 'Practical Research 2', 3, 1),
(17, 'ET', 'Empowerment Technologies', 3, 1),
(18, 'ENTREP', 'Entrepreneurship', 3, 1),
(19, 'PC', 'Pre-Calculus', 3, 1),
(20, 'BC', 'Basic Calculus', 3, 1),
(21, 'GB1', 'General Biology 1', 3, 1),
(22, 'GB2', 'General Biology 2', 3, 1),
(23, 'GP1', 'General Physics 1', 3, 1),
(24, 'GP2', 'General Physics 2', 3, 1),
(25, 'GC1', 'General Chemistry 1', 3, 1),
(26, 'GC2', 'General Chemistry 2', 3, 1),
(27, 'AE', 'Applied Economics', 3, 1),
(28, 'BESR', 'Business Ethics and Social Responsibility', 3, 1),
(29, 'FABM1', 'Fundamentals of Accountancy, Business and Management 1', 3, 1),
(30, 'FABM2', 'Fundamentals of Accountancy, Business and Management 2', 3, 1),
(31, 'BM', 'Business Mathematics', 3, 1),
(32, 'BF', 'Business Finance', 3, 1),
(33, 'OM', 'Organization and Management', 3, 1),
(34, 'PM', 'Principles of Marketing', 3, 1),
(35, 'HUM1', 'Humanities 1', 3, 1),
(36, 'HUM2', 'Humanities 2', 3, 1),
(37, 'SS1', 'Social Science 1', 3, 1),
(38, 'DRRR', 'Disaster Readiness and Risk Reduction', 3, 1),
(39, 'GAS-E1', 'GAS Elective 1', 3, 1),
(40, 'GAS-E2', 'GAS Elective 2', 3, 1),
(41, 'CSS1', 'Computer Systems Servicing 1', 4, 1),
(42, 'CSS2', 'Computer Systems Servicing 2', 4, 1),
(43, 'PROG1', 'Programming 1', 4, 1),
(44, 'PROG2', 'Programming 2', 4, 1),
(45, 'WI', 'Work Immersion', 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `curriculum_id` (`curriculum_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `professor_id` (`professor_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `curriculum`
--
ALTER TABLE `curriculum`
  ADD PRIMARY KEY (`curriculum_id`),
  ADD UNIQUE KEY `subject_strand_grade_sem_version` (`subject_id`,`strand_id`,`grade_level`,`semester`,`curriculum_version_id`),
  ADD KEY `strand_id` (`strand_id`),
  ADD KEY `curriculum_ibfk_3` (`curriculum_version_id`);

--
-- Indexes for table `curriculum_versions`
--
ALTER TABLE `curriculum_versions`
  ADD PRIMARY KEY (`curriculum_version_id`),
  ADD UNIQUE KEY `version_name` (`version_name`);

--
-- Indexes for table `educational_background`
--
ALTER TABLE `educational_background`
  ADD PRIMARY KEY (`education_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `strand_id` (`strand_id`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `enrollment_documents`
--
ALTER TABLE `enrollment_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `enrollment_payments`
--
ALTER TABLE `enrollment_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `entrance_exam_results`
--
ALTER TABLE `entrance_exam_results`
  ADD PRIMARY KEY (`exam_result_id`),
  ADD UNIQUE KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD UNIQUE KEY `enrollment_schedule` (`enrollment_id`,`schedule_id`),
  ADD KEY `schedule_id` (`schedule_id`),
  ADD KEY `encoded_by` (`encoded_by`);

--
-- Indexes for table `grading_periods`
--
ALTER TABLE `grading_periods`
  ADD PRIMARY KEY (`grading_period_id`),
  ADD UNIQUE KEY `year_sem_quarter` (`school_year_id`,`semester`,`quarter`),
  ADD KEY `school_year_id` (`school_year_id`);

--
-- Indexes for table `lms_classes`
--
ALTER TABLE `lms_classes`
  ADD PRIMARY KEY (`lms_class_id`),
  ADD UNIQUE KEY `uq_lms_schedule` (`schedule_id`);

--
-- Indexes for table `lms_materials`
--
ALTER TABLE `lms_materials`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `idx_lms_materials_class` (`lms_class_id`),
  ADD KEY `idx_lms_materials_professor` (`uploaded_by`);

--
-- Indexes for table `professors`
--
ALTER TABLE `professors`
  ADD PRIMARY KEY (`professor_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `professor_password_resets`
--
ALTER TABLE `professor_password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `professor_id` (`professor_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_name` (`room_name`);

--
-- Indexes for table `school_years`
--
ALTER TABLE `school_years`
  ADD PRIMARY KEY (`school_year_id`),
  ADD UNIQUE KEY `school_year` (`school_year`),
  ADD KEY `school_years_ibfk_1` (`curriculum_version_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`section_id`),
  ADD UNIQUE KEY `strand_grade_year_name` (`strand_id`,`grade_level`,`school_year_id`,`section_name`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `sections_ibfk_3` (`room_id`);

--
-- Indexes for table `strands`
--
ALTER TABLE `strands`
  ADD PRIMARY KEY (`strand_id`),
  ADD UNIQUE KEY `strand_name` (`strand_name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_number` (`student_number`);

--
-- Indexes for table `student_family_members`
--
ALTER TABLE `student_family_members`
  ADD PRIMARY KEY (`family_member_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `curriculum`
--
ALTER TABLE `curriculum`
  MODIFY `curriculum_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `curriculum_versions`
--
ALTER TABLE `curriculum_versions`
  MODIFY `curriculum_version_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `educational_background`
--
ALTER TABLE `educational_background`
  MODIFY `education_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enrollment_documents`
--
ALTER TABLE `enrollment_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `enrollment_payments`
--
ALTER TABLE `enrollment_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entrance_exam_results`
--
ALTER TABLE `entrance_exam_results`
  MODIFY `exam_result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grading_periods`
--
ALTER TABLE `grading_periods`
  MODIFY `grading_period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lms_classes`
--
ALTER TABLE `lms_classes`
  MODIFY `lms_class_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_materials`
--
ALTER TABLE `lms_materials`
  MODIFY `material_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `professors`
--
ALTER TABLE `professors`
  MODIFY `professor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `professor_password_resets`
--
ALTER TABLE `professor_password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `school_years`
--
ALTER TABLE `school_years`
  MODIFY `school_year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `strands`
--
ALTER TABLE `strands`
  MODIFY `strand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_family_members`
--
ALTER TABLE `student_family_members`
  MODIFY `family_member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  ADD CONSTRAINT `admin_password_resets_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE;

--
-- Constraints for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD CONSTRAINT `class_schedules_ibfk_1` FOREIGN KEY (`curriculum_id`) REFERENCES `curriculum` (`curriculum_id`),
  ADD CONSTRAINT `class_schedules_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`),
  ADD CONSTRAINT `class_schedules_ibfk_3` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`),
  ADD CONSTRAINT `class_schedules_ibfk_4` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

--
-- Constraints for table `curriculum`
--
ALTER TABLE `curriculum`
  ADD CONSTRAINT `curriculum_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`),
  ADD CONSTRAINT `curriculum_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`),
  ADD CONSTRAINT `curriculum_ibfk_3` FOREIGN KEY (`curriculum_version_id`) REFERENCES `curriculum_versions` (`curriculum_version_id`);

--
-- Constraints for table `educational_background`
--
ALTER TABLE `educational_background`
  ADD CONSTRAINT `educational_background_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`),
  ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`school_year_id`),
  ADD CONSTRAINT `enrollments_ibfk_4` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`);

--
-- Constraints for table `enrollment_documents`
--
ALTER TABLE `enrollment_documents`
  ADD CONSTRAINT `enrollment_documents_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollment_payments`
--
ALTER TABLE `enrollment_payments`
  ADD CONSTRAINT `enrollment_payments_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE;

--
-- Constraints for table `entrance_exam_results`
--
ALTER TABLE `entrance_exam_results`
  ADD CONSTRAINT `entrance_exam_results_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`schedule_id`),
  ADD CONSTRAINT `grades_ibfk_3` FOREIGN KEY (`encoded_by`) REFERENCES `professors` (`professor_id`);

--
-- Constraints for table `grading_periods`
--
ALTER TABLE `grading_periods`
  ADD CONSTRAINT `grading_periods_ibfk_1` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`school_year_id`);

--
-- Constraints for table `lms_classes`
--
ALTER TABLE `lms_classes`
  ADD CONSTRAINT `fk_lms_class_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`schedule_id`) ON UPDATE CASCADE;

--
-- Constraints for table `lms_materials`
--
ALTER TABLE `lms_materials`
  ADD CONSTRAINT `fk_lms_materials_class` FOREIGN KEY (`lms_class_id`) REFERENCES `lms_classes` (`lms_class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lms_materials_professor` FOREIGN KEY (`uploaded_by`) REFERENCES `professors` (`professor_id`) ON UPDATE CASCADE;

--
-- Constraints for table `professor_password_resets`
--
ALTER TABLE `professor_password_resets`
  ADD CONSTRAINT `professor_password_resets_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`) ON DELETE CASCADE;

--
-- Constraints for table `school_years`
--
ALTER TABLE `school_years`
  ADD CONSTRAINT `school_years_ibfk_1` FOREIGN KEY (`curriculum_version_id`) REFERENCES `curriculum_versions` (`curriculum_version_id`);

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`),
  ADD CONSTRAINT `sections_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`school_year_id`),
  ADD CONSTRAINT `sections_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

--
-- Constraints for table `student_family_members`
--
ALTER TABLE `student_family_members`
  ADD CONSTRAINT `student_family_members_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
