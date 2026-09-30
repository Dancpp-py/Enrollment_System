-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2026 at 06:47 AM
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

--
-- Dumping data for table `class_schedules`
--

INSERT INTO `class_schedules` (`schedule_id`, `curriculum_id`, `section_id`, `professor_id`, `room_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(1, 2, 1, 22, 1, 'Monday,Tuesday', '07:00:00', '09:00:00'),
(2, 7, 1, NULL, 1, NULL, NULL, NULL),
(3, 12, 1, NULL, 1, NULL, NULL, NULL),
(4, 17, 1, NULL, 1, NULL, NULL, NULL),
(5, 22, 1, NULL, 1, NULL, NULL, NULL),
(6, 27, 1, NULL, 1, NULL, NULL, NULL),
(7, 32, 1, NULL, 1, NULL, NULL, NULL),
(8, 37, 1, NULL, 1, NULL, NULL, NULL),
(9, 42, 1, NULL, 1, NULL, NULL, NULL),
(10, 47, 1, NULL, 1, NULL, NULL, NULL),
(11, 52, 1, NULL, 1, NULL, NULL, NULL),
(12, 57, 1, NULL, 1, NULL, NULL, NULL),
(13, 62, 1, NULL, 1, NULL, NULL, NULL),
(14, 67, 1, NULL, 1, NULL, NULL, NULL),
(15, 72, 1, NULL, 1, NULL, NULL, NULL),
(16, 77, 1, NULL, 1, NULL, NULL, NULL),
(17, 82, 1, NULL, 1, NULL, NULL, NULL);

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
(104, 1, 1, 1, 'Grade 12', '2nd Semester'),
(101, 1, 1, 2, 'Grade 12', '2nd Semester'),
(103, 1, 1, 3, 'Grade 12', '2nd Semester'),
(102, 1, 1, 4, 'Grade 12', '2nd Semester'),
(105, 1, 1, 5, 'Grade 12', '2nd Semester'),
(94, 1, 3, 1, 'Grade 12', '2nd Semester'),
(91, 1, 3, 2, 'Grade 12', '2nd Semester'),
(93, 1, 3, 3, 'Grade 12', '2nd Semester'),
(92, 1, 3, 4, 'Grade 12', '2nd Semester'),
(95, 1, 3, 5, 'Grade 12', '2nd Semester'),
(4, 1, 4, 1, 'Grade 11', '1st Semester'),
(1, 1, 4, 2, 'Grade 11', '1st Semester'),
(3, 1, 4, 3, 'Grade 11', '1st Semester'),
(2, 1, 4, 4, 'Grade 11', '1st Semester'),
(5, 1, 4, 5, 'Grade 11', '1st Semester'),
(44, 1, 5, 1, 'Grade 11', '1st Semester'),
(41, 1, 5, 2, 'Grade 11', '1st Semester'),
(43, 1, 5, 3, 'Grade 11', '1st Semester'),
(42, 1, 5, 4, 'Grade 11', '1st Semester'),
(45, 1, 5, 5, 'Grade 11', '1st Semester'),
(99, 1, 6, 1, 'Grade 12', '2nd Semester'),
(96, 1, 6, 2, 'Grade 12', '2nd Semester'),
(98, 1, 6, 3, 'Grade 12', '2nd Semester'),
(97, 1, 6, 4, 'Grade 12', '2nd Semester'),
(100, 1, 6, 5, 'Grade 12', '2nd Semester'),
(54, 1, 9, 1, 'Grade 11', '2nd Semester'),
(51, 1, 9, 2, 'Grade 11', '2nd Semester'),
(53, 1, 9, 3, 'Grade 11', '2nd Semester'),
(52, 1, 9, 4, 'Grade 11', '2nd Semester'),
(55, 1, 9, 5, 'Grade 11', '2nd Semester'),
(124, 1, 10, 1, 'Grade 12', '2nd Semester'),
(121, 1, 10, 2, 'Grade 12', '2nd Semester'),
(123, 1, 10, 3, 'Grade 12', '2nd Semester'),
(122, 1, 10, 4, 'Grade 12', '2nd Semester'),
(125, 1, 10, 5, 'Grade 12', '2nd Semester'),
(89, 1, 11, 1, 'Grade 12', '2nd Semester'),
(86, 1, 11, 2, 'Grade 12', '2nd Semester'),
(88, 1, 11, 3, 'Grade 12', '2nd Semester'),
(87, 1, 11, 4, 'Grade 12', '2nd Semester'),
(90, 1, 11, 5, 'Grade 12', '2nd Semester'),
(119, 1, 12, 1, 'Grade 12', '2nd Semester'),
(116, 1, 12, 2, 'Grade 12', '2nd Semester'),
(118, 1, 12, 3, 'Grade 12', '2nd Semester'),
(117, 1, 12, 4, 'Grade 12', '2nd Semester'),
(120, 1, 12, 5, 'Grade 12', '2nd Semester'),
(114, 1, 13, 1, 'Grade 12', '2nd Semester'),
(111, 1, 13, 2, 'Grade 12', '2nd Semester'),
(113, 1, 13, 3, 'Grade 12', '2nd Semester'),
(112, 1, 13, 4, 'Grade 12', '2nd Semester'),
(115, 1, 13, 5, 'Grade 12', '2nd Semester'),
(59, 1, 17, 1, 'Grade 11', '2nd Semester'),
(56, 1, 17, 2, 'Grade 11', '2nd Semester'),
(58, 1, 17, 3, 'Grade 11', '2nd Semester'),
(57, 1, 17, 4, 'Grade 11', '2nd Semester'),
(60, 1, 17, 5, 'Grade 11', '2nd Semester'),
(64, 1, 18, 1, 'Grade 11', '2nd Semester'),
(61, 1, 18, 2, 'Grade 11', '2nd Semester'),
(63, 1, 18, 3, 'Grade 11', '2nd Semester'),
(62, 1, 18, 4, 'Grade 11', '2nd Semester'),
(65, 1, 18, 5, 'Grade 11', '2nd Semester'),
(14, 1, 20, 1, 'Grade 11', '1st Semester'),
(11, 1, 20, 2, 'Grade 11', '1st Semester'),
(13, 1, 20, 3, 'Grade 11', '1st Semester'),
(12, 1, 20, 4, 'Grade 11', '1st Semester'),
(15, 1, 20, 5, 'Grade 11', '1st Semester'),
(9, 1, 27, 1, 'Grade 11', '1st Semester'),
(6, 1, 27, 2, 'Grade 11', '1st Semester'),
(8, 1, 27, 3, 'Grade 11', '1st Semester'),
(7, 1, 27, 4, 'Grade 11', '1st Semester'),
(10, 1, 27, 5, 'Grade 11', '1st Semester'),
(19, 1, 28, 1, 'Grade 11', '1st Semester'),
(16, 1, 28, 2, 'Grade 11', '1st Semester'),
(18, 1, 28, 3, 'Grade 11', '1st Semester'),
(17, 1, 28, 4, 'Grade 11', '1st Semester'),
(20, 1, 28, 5, 'Grade 11', '1st Semester'),
(69, 1, 29, 1, 'Grade 11', '2nd Semester'),
(66, 1, 29, 2, 'Grade 11', '2nd Semester'),
(68, 1, 29, 3, 'Grade 11', '2nd Semester'),
(67, 1, 29, 4, 'Grade 11', '2nd Semester'),
(70, 1, 29, 5, 'Grade 11', '2nd Semester'),
(74, 1, 30, 1, 'Grade 11', '2nd Semester'),
(71, 1, 30, 2, 'Grade 11', '2nd Semester'),
(73, 1, 30, 3, 'Grade 11', '2nd Semester'),
(72, 1, 30, 4, 'Grade 11', '2nd Semester'),
(75, 1, 30, 5, 'Grade 11', '2nd Semester'),
(29, 1, 31, 1, 'Grade 11', '1st Semester'),
(26, 1, 31, 2, 'Grade 11', '1st Semester'),
(28, 1, 31, 3, 'Grade 11', '1st Semester'),
(27, 1, 31, 4, 'Grade 11', '1st Semester'),
(30, 1, 31, 5, 'Grade 11', '1st Semester'),
(24, 1, 32, 1, 'Grade 11', '1st Semester'),
(21, 1, 32, 2, 'Grade 11', '1st Semester'),
(23, 1, 32, 3, 'Grade 11', '1st Semester'),
(22, 1, 32, 4, 'Grade 11', '1st Semester'),
(25, 1, 32, 5, 'Grade 11', '1st Semester'),
(109, 1, 33, 1, 'Grade 12', '2nd Semester'),
(106, 1, 33, 2, 'Grade 12', '2nd Semester'),
(108, 1, 33, 3, 'Grade 12', '2nd Semester'),
(107, 1, 33, 4, 'Grade 12', '2nd Semester'),
(110, 1, 33, 5, 'Grade 12', '2nd Semester'),
(49, 1, 38, 1, 'Grade 11', '1st Semester'),
(46, 1, 38, 2, 'Grade 11', '1st Semester'),
(48, 1, 38, 3, 'Grade 11', '1st Semester'),
(47, 1, 38, 4, 'Grade 11', '1st Semester'),
(50, 1, 38, 5, 'Grade 11', '1st Semester'),
(79, 1, 39, 1, 'Grade 11', '2nd Semester'),
(76, 1, 39, 2, 'Grade 11', '2nd Semester'),
(78, 1, 39, 3, 'Grade 11', '2nd Semester'),
(77, 1, 39, 4, 'Grade 11', '2nd Semester'),
(80, 1, 39, 5, 'Grade 11', '2nd Semester'),
(84, 1, 40, 1, 'Grade 11', '2nd Semester'),
(81, 1, 40, 2, 'Grade 11', '2nd Semester'),
(83, 1, 40, 3, 'Grade 11', '2nd Semester'),
(82, 1, 40, 4, 'Grade 11', '2nd Semester'),
(85, 1, 40, 5, 'Grade 11', '2nd Semester'),
(34, 1, 41, 1, 'Grade 11', '1st Semester'),
(31, 1, 41, 2, 'Grade 11', '1st Semester'),
(33, 1, 41, 3, 'Grade 11', '1st Semester'),
(32, 1, 41, 4, 'Grade 11', '1st Semester'),
(35, 1, 41, 5, 'Grade 11', '1st Semester'),
(39, 1, 42, 1, 'Grade 11', '1st Semester'),
(36, 1, 42, 2, 'Grade 11', '1st Semester'),
(38, 1, 42, 3, 'Grade 11', '1st Semester'),
(37, 1, 42, 4, 'Grade 11', '1st Semester'),
(40, 1, 42, 5, 'Grade 11', '1st Semester');

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
(1, 1, 'Perferendis sit cupi', '2004', 'Quia dignissimos mol', '2010', '', NULL, '094919463161');

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
(1, 1, 4, 'Grade 11', '1st Semester', 1, 'Confirmed', 'Enrolled', 1);

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
(1, 1, 'PSA Birth Certificate', 'uploads/documents/1_PSA_Birth_Certificate_1790082342.jpg', '2026-09-22 13:05:42'),
(2, 1, 'Grade 10 Report Card (Form 138)', 'uploads/documents/1_Grade_10_Report_Card__Form_138__1790082342.jpg', '2026-09-22 13:05:42'),
(3, 1, 'Certificate of Good Moral', 'uploads/documents/1_Certificate_of_Good_Moral_1790082342.jpg', '2026-09-22 13:05:42'),
(4, 1, 'Recent 2x2 ID Picture', 'uploads/documents/1_Recent_2x2_ID_Picture_1790082342.jpg', '2026-09-22 13:05:42');

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

--
-- Dumping data for table `enrollment_payments`
--

INSERT INTO `enrollment_payments` (`payment_id`, `enrollment_id`, `paymongo_link_id`, `checkout_url`, `amount`, `status`, `created_at`) VALUES
(1, 1, 'link_5238a3822f5f96cd7b39c20f', 'https://pm.link/org-mMY44u5kWE4vJdCxVcBRWn3k/test/YSxfYbK', 100, 'unpaid', '2026-09-22 13:07:22');

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
(1, 1, 96, 96, 96, 96, 96.00, 'Passed', '2026-09-22 13:06:13');

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
(1, 1, 'Active', '2026-09-22 13:08:27', '2026-09-22 13:08:27');

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

--
-- Dumping data for table `lms_materials`
--

INSERT INTO `lms_materials` (`material_id`, `lms_class_id`, `title`, `description`, `original_file_name`, `stored_file_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'asd', 'asd', 'prof-db-subs.pdf', '968927c561b8a44d2ee58256442c3392.pdf', 'uploads/968927c561b8a44d2ee58256442c3392.pdf', 'application/pdf', 68241, 22, '2026-09-22 13:08:36', '2026-09-22 13:08:36'),
(2, 1, 'asd', 'asd', 'professor_student_grade_entry_flowchart.pdf', 'f74571a8cb9c53f0e50a8e8e231ee18c.pdf', 'uploads/f74571a8cb9c53f0e50a8e8e231ee18c.pdf', 'application/pdf', 40615, 22, '2026-09-22 13:55:57', '2026-09-22 13:55:57'),
(3, 1, 'asd', 'asd', 'professor_student_grade_entry_flowchart (1).pdf', 'ae0fb96c2ebbcf6cf5255c8168e1ed7e.pdf', 'uploads/ae0fb96c2ebbcf6cf5255c8168e1ed7e.pdf', 'application/pdf', 40615, 22, '2026-09-22 14:06:01', '2026-09-22 14:06:01');

-- --------------------------------------------------------

--
-- Table structure for table `lms_notifications`
--

CREATE TABLE `lms_notifications` (
  `notification_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` int(11) NOT NULL,
  `lms_class_id` int(10) UNSIGNED DEFAULT NULL,
  `notification_type` enum('Material','Quiz') NOT NULL,
  `reference_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` varchar(500) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lms_notifications`
--

INSERT INTO `lms_notifications` (`notification_id`, `student_id`, `lms_class_id`, `notification_type`, `reference_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 1, 'Material', 1, 'New Learning Material', 'Your professor uploaded a new material: asd', 1, '2026-09-22 13:08:36'),
(2, 1, 1, 'Quiz', 1, 'New Quiz Available', 'Your professor published a new quiz: asd', 1, '2026-09-22 13:08:58'),
(3, 1, 1, 'Material', 2, 'New Learning Material', 'Your professor uploaded a new material: asd', 1, '2026-09-22 13:55:57'),
(4, 1, 1, 'Quiz', 2, 'New Quiz Available', 'Your professor published a new quiz: QUIZ 1', 1, '2026-09-22 13:56:51'),
(5, 1, 1, 'Material', 3, 'New Learning Material', 'Your professor uploaded a new material: asd', 1, '2026-09-22 14:06:01'),
(6, 1, 1, 'Quiz', 3, 'New Quiz Available', 'Your professor published a new quiz: QUIZ 2 NA MGA TANGA', 1, '2026-09-22 14:06:32');

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
(16, NULL, 'Castro', 'Ella', 'P', '09170000016', 'ella.castro@school.edu', NULL, 0),
(17, NULL, 'Bautista', 'Neil', 'Q', '09170000017', 'neil.bautista@school.edu', NULL, 0),
(18, NULL, 'Dela Cruz', 'Rose', 'R', '09170000018', 'rose.delacruz@school.edu', NULL, 1),
(19, NULL, 'Lim', 'Kevin', 'S', '09170000019', 'kevin.lim@school.edu', NULL, 1),
(20, NULL, 'Tan', 'Sophia', 'T', '09170000020', 'sophia.tan@school.edu', NULL, 1),
(21, 'sheriffwoody.pride', 'Pride', 'Sheriff Woody', '', '09278682456', 'sarino.charlesmichael@ncst.edu.ph', '$2y$10$u.F8fuS6kOJRxfBp7dYtEOXV.b4tTf33uZ3x9i3Kj9Ml/LNOqQvdO', 1),
(22, 'noah.pascua', 'Pascua', 'Noah', '', '09499463161', 'pascua.noah@ncst.edu.ph', '$2y$10$75TplRaNuRlNSSlHIs.Kz.6Kb8O9rTW6EHT2jBLmI95qWM3.ZJ9Q.', 1);

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
(1, 22, '082056ce69c063b31ff0bd2a49c9d90147892be24aad9c4a0aedf3c503252f1c', '2026-09-23 15:06:48', 1, '2026-09-22 13:06:48');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `time_limit` int(11) DEFAULT NULL,
  `opens_at` datetime DEFAULT NULL,
  `closes_at` datetime DEFAULT NULL,
  `status` enum('Draft','Published') NOT NULL DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `professor_id`, `schedule_id`, `title`, `time_limit`, `opens_at`, `closes_at`, `status`, `created_at`) VALUES
(1, 22, 1, 'asd', 5, NULL, NULL, 'Published', '2026-09-22 13:08:55'),
(2, 22, 1, 'QUIZ 1', 10, NULL, NULL, 'Published', '2026-09-22 13:56:45'),
(3, 22, 1, 'QUIZ 2 NA MGA TANGA', 90, NULL, NULL, 'Published', '2026-09-22 14:06:27');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `attempt_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` enum('In Progress','Submitted') NOT NULL DEFAULT 'In Progress',
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `submitted_at` timestamp NULL DEFAULT NULL,
  `total_items` int(11) DEFAULT NULL,
  `correct_count` int(11) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempt_answers`
--

CREATE TABLE `quiz_attempt_answers` (
  `attempt_answer_id` int(11) NOT NULL,
  `attempt_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer` enum('A','B','C','D') DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_order` int(11) NOT NULL DEFAULT 1,
  `question_text` text NOT NULL,
  `choice_a` varchar(255) NOT NULL,
  `choice_b` varchar(255) NOT NULL,
  `choice_c` varchar(255) NOT NULL,
  `choice_d` varchar(255) NOT NULL,
  `correct_answer` enum('A','B','C','D') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`question_id`, `quiz_id`, `question_order`, `question_text`, `choice_a`, `choice_b`, `choice_c`, `choice_d`, `correct_answer`) VALUES
(1, 1, 1, 'asd', 'asd', 'asd', 'asd', 'asd', 'A'),
(2, 2, 1, 'What is oblong?', 'logbi', 'medyo bilog', 'taligid', 'talongok', 'C'),
(3, 3, 1, 'ASD', 'ASD', 'ASD', 'ASD', 'ASD', 'A');

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
-- Table structure for table `school_calendar`
--

CREATE TABLE `school_calendar` (
  `calendar_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `event_type` enum('Exam','School Event','Deadline','Holiday','Other') NOT NULL DEFAULT 'Other',
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_calendar`
--

INSERT INTO `school_calendar` (`calendar_id`, `title`, `event_type`, `event_date`, `start_time`, `end_time`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 'TJ', 'School Event', '2026-09-27', '05:35:00', '17:35:00', 'ASD', NULL, '2026-09-26 09:35:43', NULL),
(4, 'UPDATED PSA', 'Deadline', '2026-09-30', '06:42:00', '18:42:00', 'ASAP', NULL, '2026-09-26 09:43:00', NULL),
(5, 'LIBRENG TULI', 'School Event', '2026-09-29', '13:00:00', '17:00:00', '', NULL, '2026-09-29 04:41:04', NULL);

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
(1, '2026-2027', 1, 1),
(2, '2028-2029', 0, 1);

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
(1, 4, 'Grade 11', 1, '1101', 40, 1);

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
(1, '202600001', 'EX2026-0001', 'Existing', 'George Merritt', 'Keith Booker', 'Samantha Espinoza', 'Quae tempore omnis', '1975-01-23', 'Female', '09499463161', 'pascuanoah@gmail.com', 'Deleniti commodo dol', '$2y$10$np70jQHHFlS43XeyuJN6juPia5Y7u/VnxB2wg8ZqiKsPbkY5LGLYq');

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
(1, 1, 'Father', 'Lillian Holmes', 'Ronan Deleon', 'Quemby Craig', 'Atque deleniti est l', '09499463161', 'Voluptate hic quia c'),
(2, 1, 'Mother', 'Lacey Collins', 'Joy Coffey', 'Palmer Morrow', 'Qui eligendi aliqua', '09499463161', 'Est aliquip cupidit'),
(3, 1, 'Legal Guardian', 'Eric Gilliam', 'Alea Brooks', 'Petra Faulkner', '', '09499463161', 'Aliquip in irure und');

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
-- Indexes for table `lms_notifications`
--
ALTER TABLE `lms_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD UNIQUE KEY `uq_student_notification` (`student_id`,`notification_type`,`reference_id`),
  ADD KEY `idx_notification_student_read` (`student_id`,`is_read`),
  ADD KEY `idx_notification_created` (`created_at`),
  ADD KEY `fk_notification_class` (`lms_class_id`);

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
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`),
  ADD KEY `professor_id` (`professor_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD UNIQUE KEY `quiz_student` (`quiz_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `quiz_attempt_answers`
--
ALTER TABLE `quiz_attempt_answers`
  ADD PRIMARY KEY (`attempt_answer_id`),
  ADD UNIQUE KEY `attempt_question` (`attempt_id`,`question_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_name` (`room_name`);

--
-- Indexes for table `school_calendar`
--
ALTER TABLE `school_calendar`
  ADD PRIMARY KEY (`calendar_id`),
  ADD KEY `idx_calendar_date` (`event_date`),
  ADD KEY `idx_calendar_type` (`event_type`);

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
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `curriculum`
--
ALTER TABLE `curriculum`
  MODIFY `curriculum_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `curriculum_versions`
--
ALTER TABLE `curriculum_versions`
  MODIFY `curriculum_version_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `educational_background`
--
ALTER TABLE `educational_background`
  MODIFY `education_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enrollment_documents`
--
ALTER TABLE `enrollment_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enrollment_payments`
--
ALTER TABLE `enrollment_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `entrance_exam_results`
--
ALTER TABLE `entrance_exam_results`
  MODIFY `exam_result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `lms_notifications`
--
ALTER TABLE `lms_notifications`
  MODIFY `notification_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `professors`
--
ALTER TABLE `professors`
  MODIFY `professor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `professor_password_resets`
--
ALTER TABLE `professor_password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quiz_attempt_answers`
--
ALTER TABLE `quiz_attempt_answers`
  MODIFY `attempt_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `school_calendar`
--
ALTER TABLE `school_calendar`
  MODIFY `calendar_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_family_members`
--
ALTER TABLE `student_family_members`
  MODIFY `family_member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- Constraints for table `lms_classes`
--
ALTER TABLE `lms_classes`
  ADD CONSTRAINT `fk_lms_class_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`schedule_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
