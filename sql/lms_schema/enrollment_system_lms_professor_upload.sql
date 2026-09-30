-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2026 at 12:43 PM
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
(4, 'noah.pascua', NULL, 'Registrar', 0, '$2y$10$hhL9nqcXlRNi4RouYE9CT.Dnt.DbMlGONuQDDe4mS5IYOuLUlSIBS'),
(5, 'qwerty', NULL, 'Admissions', 1, '$2y$10$rn1q4WAK08XvldZGjNGbneKoL4b//eAJDH2pC.oBHUEVj8w44I3De'),
(6, 'noahzxc', NULL, 'Registrar', 1, '$2y$10$V6EA347mGdMr94lOTGxTqeSYEBJ.jovuqtD434DjsKIMDNg0Go2qm'),
(8, 'demi', NULL, 'Scheduler', 1, '$2y$10$/AcR3b3kMa1ikCEcuE7XReg5CfL3w575OaPIF4gDtTSRTz2ffnsBC'),
(9, 'chrlz', 'maddoto112@gmail.com', 'Registrar', 0, '$2y$10$LqGdkrkTd/wiHAaXSsHywum/oCJli83kG.fhqSRM.fdbT2nCyM4ba'),
(10, 'chrlzzz', 'charles.michael112@gmail.com', 'Admissions', 1, '$2y$10$Tmh5LvEK/vYx.W4YR7gDyeRubyNcZhZkN/kz7laroBNtuMr7jF3Ni');

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

--
-- Dumping data for table `class_schedules`
--

INSERT INTO `class_schedules` (`schedule_id`, `curriculum_id`, `section_id`, `professor_id`, `room_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(1, 25, 1, NULL, 1, NULL, NULL, NULL),
(2, 26, 1, NULL, 1, NULL, NULL, NULL),
(3, 27, 1, NULL, 1, NULL, NULL, NULL),
(4, 28, 1, NULL, 1, NULL, NULL, NULL),
(5, 29, 1, NULL, 1, NULL, NULL, NULL),
(6, 30, 1, NULL, 1, NULL, NULL, NULL),
(7, 31, 1, NULL, 1, NULL, NULL, NULL),
(8, 32, 1, NULL, 1, NULL, NULL, NULL),
(9, 33, 1, NULL, 1, NULL, NULL, NULL),
(10, 34, 1, NULL, 1, NULL, NULL, NULL),
(11, 35, 1, NULL, 1, NULL, NULL, NULL),
(12, 36, 1, NULL, 1, NULL, NULL, NULL),
(13, 37, 1, NULL, 1, NULL, NULL, NULL),
(14, 38, 1, NULL, 1, NULL, NULL, NULL),
(15, 39, 1, NULL, 1, NULL, NULL, NULL),
(16, 99, 1, 21, 1, 'Monday,Wednesday,Friday', '09:00:00', '10:00:00'),
(17, 104, 1, NULL, 1, NULL, NULL, NULL),
(18, 109, 1, NULL, 1, NULL, NULL, NULL);

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

--
-- Dumping data for table `curriculum`
--

INSERT INTO `curriculum` (`curriculum_id`, `curriculum_version_id`, `subject_id`, `strand_id`, `grade_level`, `semester`) VALUES
(1, 1, 1, 1, 'Grade 11', '1st Semester'),
(25, 1, 1, 2, 'Grade 11', '1st Semester'),
(49, 1, 1, 4, 'Grade 11', '1st Semester'),
(72, 1, 1, 5, 'Grade 11', '1st Semester'),
(2, 1, 2, 1, 'Grade 11', '1st Semester'),
(26, 1, 2, 2, 'Grade 11', '1st Semester'),
(50, 1, 2, 4, 'Grade 11', '1st Semester'),
(73, 1, 2, 5, 'Grade 11', '1st Semester'),
(3, 1, 3, 1, 'Grade 11', '1st Semester'),
(33, 1, 3, 2, 'Grade 11', '2nd Semester'),
(56, 1, 3, 4, 'Grade 11', '2nd Semester'),
(78, 1, 3, 5, 'Grade 11', '2nd Semester'),
(102, 2, 4, 1, 'Grade 11', '1st Semester'),
(99, 2, 4, 2, 'Grade 11', '1st Semester'),
(34, 1, 4, 2, 'Grade 11', '2nd Semester'),
(101, 2, 4, 3, 'Grade 11', '1st Semester'),
(100, 2, 4, 4, 'Grade 11', '1st Semester'),
(57, 1, 4, 4, 'Grade 11', '2nd Semester'),
(103, 2, 4, 5, 'Grade 11', '1st Semester'),
(114, 3, 4, 5, 'Grade 11', '1st Semester'),
(79, 1, 4, 5, 'Grade 11', '2nd Semester'),
(35, 1, 5, 2, 'Grade 11', '2nd Semester'),
(58, 1, 5, 4, 'Grade 11', '2nd Semester'),
(80, 1, 5, 5, 'Grade 11', '2nd Semester'),
(20, 1, 6, 1, 'Grade 12', '2nd Semester'),
(47, 1, 6, 2, 'Grade 12', '2nd Semester'),
(118, 3, 6, 5, 'Grade 11', '1st Semester'),
(89, 1, 6, 5, 'Grade 12', '2nd Semester'),
(4, 1, 7, 1, 'Grade 11', '1st Semester'),
(27, 1, 7, 2, 'Grade 11', '1st Semester'),
(51, 1, 7, 4, 'Grade 11', '1st Semester'),
(74, 1, 7, 5, 'Grade 11', '1st Semester'),
(28, 1, 8, 2, 'Grade 11', '1st Semester'),
(52, 1, 8, 4, 'Grade 11', '1st Semester'),
(5, 1, 9, 1, 'Grade 11', '1st Semester'),
(53, 1, 9, 4, 'Grade 11', '1st Semester'),
(21, 1, 10, 1, 'Grade 12', '2nd Semester'),
(48, 1, 10, 2, 'Grade 12', '2nd Semester'),
(22, 1, 11, 1, 'Grade 12', '2nd Semester'),
(7, 1, 12, 1, 'Grade 11', '1st Semester'),
(46, 1, 12, 2, 'Grade 12', '2nd Semester'),
(88, 1, 12, 5, 'Grade 12', '2nd Semester'),
(6, 1, 13, 1, 'Grade 11', '1st Semester'),
(29, 1, 13, 2, 'Grade 11', '1st Semester'),
(54, 1, 13, 4, 'Grade 11', '1st Semester'),
(75, 1, 13, 5, 'Grade 11', '1st Semester'),
(23, 1, 14, 1, 'Grade 12', '2nd Semester'),
(36, 1, 14, 2, 'Grade 11', '2nd Semester'),
(59, 1, 14, 4, 'Grade 11', '2nd Semester'),
(13, 1, 15, 1, 'Grade 11', '2nd Semester'),
(43, 1, 15, 2, 'Grade 12', '1st Semester'),
(60, 1, 15, 4, 'Grade 11', '2nd Semester'),
(84, 1, 15, 5, 'Grade 12', '1st Semester'),
(14, 1, 16, 1, 'Grade 11', '2nd Semester'),
(44, 1, 16, 2, 'Grade 12', '1st Semester'),
(61, 1, 16, 4, 'Grade 11', '2nd Semester'),
(85, 1, 16, 5, 'Grade 12', '1st Semester'),
(8, 1, 17, 1, 'Grade 11', '1st Semester'),
(24, 1, 17, 1, 'Grade 12', '2nd Semester'),
(30, 1, 17, 2, 'Grade 11', '1st Semester'),
(55, 1, 17, 4, 'Grade 11', '1st Semester'),
(76, 1, 17, 5, 'Grade 11', '1st Semester'),
(19, 1, 18, 1, 'Grade 12', '1st Semester'),
(45, 1, 18, 2, 'Grade 12', '2nd Semester'),
(70, 1, 18, 4, 'Grade 12', '2nd Semester'),
(117, 3, 18, 5, 'Grade 11', '1st Semester'),
(86, 1, 18, 5, 'Grade 12', '1st Semester'),
(9, 1, 19, 1, 'Grade 11', '2nd Semester'),
(112, 2, 20, 1, 'Grade 11', '1st Semester'),
(15, 1, 20, 1, 'Grade 12', '1st Semester'),
(109, 2, 20, 2, 'Grade 11', '1st Semester'),
(111, 2, 20, 3, 'Grade 11', '1st Semester'),
(110, 2, 20, 4, 'Grade 11', '1st Semester'),
(113, 2, 20, 5, 'Grade 11', '1st Semester'),
(10, 1, 21, 1, 'Grade 11', '2nd Semester'),
(16, 1, 22, 1, 'Grade 12', '1st Semester'),
(11, 1, 23, 1, 'Grade 11', '2nd Semester'),
(17, 1, 24, 1, 'Grade 12', '1st Semester'),
(12, 1, 25, 1, 'Grade 11', '2nd Semester'),
(18, 1, 26, 1, 'Grade 12', '1st Semester'),
(107, 2, 27, 1, 'Grade 11', '1st Semester'),
(104, 2, 27, 2, 'Grade 11', '1st Semester'),
(37, 1, 27, 2, 'Grade 11', '2nd Semester'),
(106, 2, 27, 3, 'Grade 11', '1st Semester'),
(105, 2, 27, 4, 'Grade 11', '1st Semester'),
(65, 1, 27, 4, 'Grade 12', '1st Semester'),
(108, 2, 27, 5, 'Grade 11', '1st Semester'),
(38, 1, 28, 2, 'Grade 11', '2nd Semester'),
(31, 1, 29, 2, 'Grade 11', '1st Semester'),
(40, 1, 30, 2, 'Grade 12', '1st Semester'),
(32, 1, 31, 2, 'Grade 11', '1st Semester'),
(41, 1, 32, 2, 'Grade 12', '1st Semester'),
(39, 1, 33, 2, 'Grade 11', '2nd Semester'),
(66, 1, 33, 4, 'Grade 12', '1st Semester'),
(42, 1, 34, 2, 'Grade 12', '1st Semester'),
(62, 1, 35, 4, 'Grade 12', '1st Semester'),
(63, 1, 36, 4, 'Grade 12', '1st Semester'),
(64, 1, 37, 4, 'Grade 12', '1st Semester'),
(67, 1, 38, 4, 'Grade 12', '2nd Semester'),
(68, 1, 39, 4, 'Grade 12', '2nd Semester'),
(69, 1, 40, 4, 'Grade 12', '2nd Semester'),
(77, 1, 41, 5, 'Grade 11', '1st Semester'),
(115, 3, 41, 5, 'Grade 11', '1st Semester'),
(116, 3, 42, 5, 'Grade 11', '1st Semester'),
(81, 1, 42, 5, 'Grade 11', '2nd Semester'),
(119, 3, 43, 5, 'Grade 11', '1st Semester'),
(82, 1, 43, 5, 'Grade 11', '2nd Semester'),
(120, 3, 44, 5, 'Grade 11', '1st Semester'),
(83, 1, 44, 5, 'Grade 12', '1st Semester'),
(71, 1, 45, 4, 'Grade 12', '2nd Semester'),
(87, 1, 45, 5, 'Grade 12', '2nd Semester');

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
(2, 'SY 2026-2027 V2', '2026-09-15 16:47:19'),
(3, 'SY 2026-2027 V3', '2026-09-16 16:36:29');

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
(1, 1, 'Facilis voluptate do', '2020', 'Reprehenderit natus', '2024', '', NULL, '111111111111'),
(2, 2, 'Ut officiis eveniet', '2020', 'Delectus aliquam qu', '2024', '', NULL, '222222222222');

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
(1, 1, 3, 'Grade 11', '1st Semester', 2, 'Confirmed', 'Enrollment Review', NULL),
(2, 2, 2, 'Grade 11', '1st Semester', 2, 'Confirmed', 'Enrolled', 1);

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
(1, 1, 'PSA Birth Certificate', 'uploads/documents/1_PSA_Birth_Certificate_1789813908.jpg', '2026-09-19 10:31:48'),
(2, 1, 'Grade 10 Report Card (Form 138)', 'uploads/documents/1_Grade_10_Report_Card__Form_138__1789813908.jpg', '2026-09-19 10:31:48'),
(3, 1, 'Certificate of Good Moral', 'uploads/documents/1_Certificate_of_Good_Moral_1789813908.jpg', '2026-09-19 10:31:48'),
(4, 1, 'Recent 2x2 ID Picture', 'uploads/documents/1_Recent_2x2_ID_Picture_1789813908.jpg', '2026-09-19 10:31:48'),
(5, 2, 'PSA Birth Certificate', 'uploads/documents/2_PSA_Birth_Certificate_1789813946.jpg', '2026-09-19 10:32:26'),
(6, 2, 'Grade 10 Report Card (Form 138)', 'uploads/documents/2_Grade_10_Report_Card__Form_138__1789813946.jpg', '2026-09-19 10:32:26'),
(7, 2, 'Certificate of Good Moral', 'uploads/documents/2_Certificate_of_Good_Moral_1789813946.jpg', '2026-09-19 10:32:26'),
(8, 2, 'Recent 2x2 ID Picture', 'uploads/documents/2_Recent_2x2_ID_Picture_1789813946.jpg', '2026-09-19 10:32:26'),
(9, 2, 'Transcript of Records / Form 137', 'uploads/documents/2_Transcript_of_Records___Form_137_1789813946.jpg', '2026-09-19 10:32:26');

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

--
-- Dumping data for table `entrance_exam_results`
--

INSERT INTO `entrance_exam_results` (`exam_result_id`, `enrollment_id`, `math_score`, `english_score`, `filipino_score`, `science_score`, `average_score`, `exam_status`, `scored_at`) VALUES
(1, 1, 89, 93, 88, 92, 90.50, 'Passed', '2026-09-19 10:33:16'),
(2, 2, 92, 93, 94, 95, 93.50, 'Passed', '2026-09-19 10:33:43');

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

--
-- Dumping data for table `lms_classes`
--

INSERT INTO `lms_classes` (`lms_class_id`, `schedule_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 16, 'Active', '2026-09-19 10:41:07', '2026-09-19 10:41:07');

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
(10, NULL, 'Aquino', 'Carla', 'J', '09170000010', 'carla.aquino@school.edu', NULL, 1),
(11, NULL, 'Fernandez', 'Leo', 'K', '09170000011', 'leo.fernandez@school.edu', NULL, 1),
(12, NULL, 'Mendoza', 'Kim', 'L', '09170000012', 'kim.mendoza@school.edu', NULL, 1),
(13, NULL, 'Navarro', 'Brian', 'M', '09170000013', 'brian.navarro@school.edu', NULL, 1),
(14, NULL, 'Morales', 'Janice', 'N', '09170000014', 'janice.morales@school.edu', NULL, 1),
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

--
-- Dumping data for table `professor_password_resets`
--

INSERT INTO `professor_password_resets` (`reset_id`, `professor_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
(1, 21, '4da97dcfadcff9e9dae14563756228b22d24fd2d1a530808334e97d3323b0809', '2026-09-20 12:38:31', 1, '2026-09-19 10:38:31');

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
(2, '2026-2027', 1, 2);

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

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`section_id`, `strand_id`, `grade_level`, `school_year_id`, `section_name`, `capacity`, `room_id`) VALUES
(1, 2, 'Grade 11', 2, 'MAGSAYSAY', 40, 1);

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
(1, NULL, 'EX2026-0002', 'New', 'Nathan Rivas', 'Chadwick Byrd', 'Rajah Fletcher', 'Eos qui in officia m', '1982-11-15', 'Male', '09278682456', 'charles.michael112@gmail.com', 'Sed quibusdam fugiat', NULL),
(2, '202600001', 'EX2026-0001', 'Existing', 'Armand Aguirre', 'Cheryl Barr', 'Vincent Pugh', 'Et duis numquam repr', '1991-06-05', 'Female', '09278682456', 'maddoto112@gmail.com', 'Laudantium ullam co', '$2y$10$WvlPF/y.7FUSQLsDlPeMcO13gQ3tOq1GkRNLgKFi/tOmD7l1dLPq6');

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
(1, 1, 'Father', 'Richard Cherry', 'Marcia Andrews', 'Duncan Fowler', 'Omnis totam voluptat', '09278682456', 'Adipisicing consequa'),
(2, 1, 'Mother', 'Stella Wiggins', 'Brenden Meyers', 'Molly Valentine', 'Mollit ut dolorum fa', '09278682456', 'Minus nobis laboris'),
(3, 1, 'Brother', 'Abra Contreras', 'Xaviera Henson', 'Chester Thornton', '', '09278682456', 'Occaecat excepturi p'),
(4, 2, 'Father', 'Jameson Battle', 'Rafael Owens', 'Zachary Hickman', 'Consequatur enim en', '09278682456', 'Omnis in perspiciati'),
(5, 2, 'Mother', 'Lila Mcclure', 'Mohammad Holman', 'Bevis Lott', 'Odit nesciunt venia', '09278682456', 'Corrupti non non do'),
(6, 2, 'Grandfather', 'Dawn Guerra', 'Emmanuel Shaffer', 'Lavinia Schroeder', '', '09278682456', 'Laudantium libero s');

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
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `curriculum`
--
ALTER TABLE `curriculum`
  MODIFY `curriculum_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `curriculum_versions`
--
ALTER TABLE `curriculum_versions`
  MODIFY `curriculum_version_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `educational_background`
--
ALTER TABLE `educational_background`
  MODIFY `education_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enrollment_documents`
--
ALTER TABLE `enrollment_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `entrance_exam_results`
--
ALTER TABLE `entrance_exam_results`
  MODIFY `exam_result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `lms_class_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lms_materials`
--
ALTER TABLE `lms_materials`
  MODIFY `material_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `professors`
--
ALTER TABLE `professors`
  MODIFY `professor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `professor_password_resets`
--
ALTER TABLE `professor_password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `school_years`
--
ALTER TABLE `school_years`
  MODIFY `school_year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_family_members`
--
ALTER TABLE `student_family_members`
  MODIFY `family_member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

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
