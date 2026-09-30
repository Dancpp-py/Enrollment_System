-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2026 at 07:37 PM
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
(1, 28, 2, NULL, 1, NULL, NULL, NULL),
(2, 29, 2, NULL, 1, NULL, NULL, NULL),
(3, 30, 2, NULL, 1, NULL, NULL, NULL),
(4, 31, 2, NULL, 1, NULL, NULL, NULL),
(5, 32, 2, NULL, 1, NULL, NULL, NULL),
(6, 33, 2, NULL, 1, NULL, NULL, NULL),
(7, 34, 2, NULL, 1, NULL, NULL, NULL),
(8, 35, 2, NULL, 1, NULL, NULL, NULL),
(9, 36, 2, NULL, 1, NULL, NULL, NULL),
(10, 37, 2, NULL, 1, NULL, NULL, NULL),
(11, 38, 2, NULL, 1, NULL, NULL, NULL),
(12, 39, 2, NULL, 1, NULL, NULL, NULL),
(13, 40, 2, NULL, 1, NULL, NULL, NULL),
(14, 41, 2, NULL, 1, NULL, NULL, NULL),
(15, 42, 2, NULL, 1, NULL, NULL, NULL),
(16, 43, 2, NULL, 1, NULL, NULL, NULL),
(17, 44, 2, NULL, 1, NULL, NULL, NULL),
(18, 45, 2, NULL, 1, NULL, NULL, NULL),
(19, 46, 2, NULL, 1, NULL, NULL, NULL),
(20, 81, 3, NULL, 2, NULL, NULL, NULL),
(21, 82, 3, NULL, 2, NULL, NULL, NULL),
(22, 83, 3, NULL, 2, NULL, NULL, NULL),
(23, 84, 3, NULL, 2, NULL, NULL, NULL),
(24, 85, 3, NULL, 2, NULL, NULL, NULL),
(25, 86, 3, NULL, 2, NULL, NULL, NULL),
(26, 87, 3, NULL, 2, NULL, NULL, NULL),
(27, 88, 3, NULL, 2, NULL, NULL, NULL),
(28, 89, 3, NULL, 2, NULL, NULL, NULL),
(29, 90, 3, NULL, 2, NULL, NULL, NULL),
(30, 91, 3, NULL, 2, NULL, NULL, NULL),
(31, 92, 3, NULL, 2, NULL, NULL, NULL),
(32, 93, 3, NULL, 2, NULL, NULL, NULL),
(33, 94, 3, NULL, 2, NULL, NULL, NULL),
(34, 95, 3, NULL, 2, NULL, NULL, NULL),
(35, 96, 3, NULL, 2, NULL, NULL, NULL),
(36, 97, 3, NULL, 2, NULL, NULL, NULL),
(37, 98, 3, NULL, 2, NULL, NULL, NULL),
(38, 56, 4, NULL, 3, NULL, NULL, NULL),
(39, 57, 4, NULL, 3, NULL, NULL, NULL),
(40, 58, 4, NULL, 3, NULL, NULL, NULL),
(41, 59, 4, NULL, 3, NULL, NULL, NULL),
(42, 60, 4, NULL, 3, NULL, NULL, NULL),
(43, 61, 4, NULL, 3, NULL, NULL, NULL),
(44, 62, 4, NULL, 3, NULL, NULL, NULL),
(45, 63, 4, NULL, 3, NULL, NULL, NULL),
(46, 64, 4, NULL, 3, NULL, NULL, NULL),
(47, 65, 4, NULL, 3, NULL, NULL, NULL),
(48, 66, 4, NULL, 3, NULL, NULL, NULL),
(49, 67, 4, NULL, 3, NULL, NULL, NULL),
(50, 68, 4, NULL, 3, NULL, NULL, NULL),
(51, 69, 4, NULL, 3, NULL, NULL, NULL),
(52, 70, 4, NULL, 3, NULL, NULL, NULL),
(53, 71, 4, NULL, 3, NULL, NULL, NULL),
(54, 72, 4, NULL, 3, NULL, NULL, NULL),
(55, 73, 4, NULL, 3, NULL, NULL, NULL),
(56, 74, 4, NULL, 3, NULL, NULL, NULL),
(57, 1, 5, NULL, 4, NULL, NULL, NULL),
(58, 2, 5, NULL, 4, NULL, NULL, NULL),
(59, 3, 5, NULL, 4, NULL, NULL, NULL),
(60, 4, 5, NULL, 4, NULL, NULL, NULL),
(61, 5, 5, NULL, 4, NULL, NULL, NULL),
(62, 6, 5, NULL, 4, NULL, NULL, NULL),
(63, 7, 5, NULL, 4, NULL, NULL, NULL),
(64, 8, 5, NULL, 4, NULL, NULL, NULL),
(65, 9, 5, NULL, 4, NULL, NULL, NULL),
(66, 10, 5, NULL, 4, NULL, NULL, NULL),
(67, 11, 5, NULL, 4, NULL, NULL, NULL),
(68, 12, 5, NULL, 4, NULL, NULL, NULL),
(69, 13, 5, NULL, 4, NULL, NULL, NULL),
(70, 14, 5, NULL, 4, NULL, NULL, NULL),
(71, 15, 5, NULL, 4, NULL, NULL, NULL),
(72, 16, 5, NULL, 4, NULL, NULL, NULL),
(73, 17, 5, NULL, 4, NULL, NULL, NULL),
(74, 18, 5, NULL, 4, NULL, NULL, NULL),
(75, 19, 5, NULL, 4, NULL, NULL, NULL),
(76, 104, 6, NULL, 5, NULL, NULL, NULL),
(77, 105, 6, NULL, 5, NULL, NULL, NULL),
(78, 106, 6, NULL, 5, NULL, NULL, NULL),
(79, 107, 6, NULL, 5, NULL, NULL, NULL),
(80, 108, 6, NULL, 5, NULL, NULL, NULL),
(81, 109, 6, NULL, 5, NULL, NULL, NULL),
(82, 110, 6, NULL, 5, NULL, NULL, NULL),
(83, 111, 6, NULL, 5, NULL, NULL, NULL),
(84, 112, 6, NULL, 5, NULL, NULL, NULL),
(85, 113, 6, NULL, 5, NULL, NULL, NULL),
(86, 114, 6, 21, 5, 'Monday,Wednesday,Friday', '10:00:00', '12:00:00'),
(87, 115, 6, NULL, 5, NULL, NULL, NULL),
(88, 116, 6, NULL, 5, NULL, NULL, NULL),
(89, 117, 6, NULL, 5, NULL, NULL, NULL),
(90, 118, 6, NULL, 5, NULL, NULL, NULL),
(91, 119, 6, NULL, 5, NULL, NULL, NULL),
(92, 120, 6, NULL, 5, NULL, NULL, NULL),
(93, 121, 6, NULL, 5, NULL, NULL, NULL),
(94, 122, 6, NULL, 5, NULL, NULL, NULL),
(95, 123, 6, NULL, 5, NULL, NULL, NULL);

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
(28, 1, 1, 2, 'Grade 11', '1st Semester'),
(56, 1, 1, 3, 'Grade 11', '1st Semester'),
(81, 1, 1, 4, 'Grade 11', '1st Semester'),
(104, 1, 1, 5, 'Grade 11', '1st Semester'),
(2, 1, 2, 1, 'Grade 11', '1st Semester'),
(29, 1, 2, 2, 'Grade 11', '1st Semester'),
(57, 1, 2, 3, 'Grade 11', '1st Semester'),
(82, 1, 2, 4, 'Grade 11', '1st Semester'),
(105, 1, 2, 5, 'Grade 11', '1st Semester'),
(3, 1, 3, 1, 'Grade 11', '1st Semester'),
(30, 1, 3, 2, 'Grade 11', '1st Semester'),
(58, 1, 3, 3, 'Grade 11', '1st Semester'),
(83, 1, 3, 4, 'Grade 11', '1st Semester'),
(106, 1, 3, 5, 'Grade 11', '1st Semester'),
(11, 1, 4, 1, 'Grade 11', '2nd Semester'),
(38, 1, 4, 2, 'Grade 11', '2nd Semester'),
(66, 1, 4, 3, 'Grade 11', '2nd Semester'),
(91, 1, 4, 4, 'Grade 11', '2nd Semester'),
(115, 1, 4, 5, 'Grade 11', '2nd Semester'),
(12, 1, 5, 1, 'Grade 11', '2nd Semester'),
(39, 1, 5, 2, 'Grade 11', '2nd Semester'),
(67, 1, 5, 3, 'Grade 11', '2nd Semester'),
(92, 1, 5, 4, 'Grade 11', '2nd Semester'),
(116, 1, 5, 5, 'Grade 11', '2nd Semester'),
(13, 1, 6, 1, 'Grade 11', '2nd Semester'),
(40, 1, 6, 2, 'Grade 11', '2nd Semester'),
(68, 1, 6, 3, 'Grade 11', '2nd Semester'),
(93, 1, 6, 4, 'Grade 11', '2nd Semester'),
(117, 1, 6, 5, 'Grade 11', '2nd Semester'),
(4, 1, 7, 1, 'Grade 11', '1st Semester'),
(31, 1, 7, 2, 'Grade 11', '1st Semester'),
(59, 1, 7, 3, 'Grade 11', '1st Semester'),
(84, 1, 7, 4, 'Grade 11', '1st Semester'),
(107, 1, 7, 5, 'Grade 11', '1st Semester'),
(14, 1, 8, 1, 'Grade 11', '2nd Semester'),
(41, 1, 8, 2, 'Grade 11', '2nd Semester'),
(69, 1, 8, 3, 'Grade 11', '2nd Semester'),
(94, 1, 8, 4, 'Grade 11', '2nd Semester'),
(118, 1, 8, 5, 'Grade 11', '2nd Semester'),
(5, 1, 9, 1, 'Grade 11', '1st Semester'),
(32, 1, 9, 2, 'Grade 11', '1st Semester'),
(60, 1, 9, 3, 'Grade 11', '1st Semester'),
(85, 1, 9, 4, 'Grade 11', '1st Semester'),
(108, 1, 9, 5, 'Grade 11', '1st Semester'),
(15, 1, 10, 1, 'Grade 11', '2nd Semester'),
(42, 1, 10, 2, 'Grade 11', '2nd Semester'),
(70, 1, 10, 3, 'Grade 11', '2nd Semester'),
(95, 1, 10, 4, 'Grade 11', '2nd Semester'),
(119, 1, 10, 5, 'Grade 11', '2nd Semester'),
(6, 1, 11, 1, 'Grade 11', '1st Semester'),
(33, 1, 11, 2, 'Grade 11', '1st Semester'),
(61, 1, 11, 3, 'Grade 11', '1st Semester'),
(86, 1, 11, 4, 'Grade 11', '1st Semester'),
(109, 1, 11, 5, 'Grade 11', '1st Semester'),
(7, 1, 12, 1, 'Grade 11', '1st Semester'),
(24, 1, 12, 1, 'Grade 12', '1st Semester'),
(34, 1, 12, 2, 'Grade 11', '1st Semester'),
(62, 1, 12, 3, 'Grade 11', '1st Semester'),
(87, 1, 12, 4, 'Grade 11', '1st Semester'),
(110, 1, 12, 5, 'Grade 11', '1st Semester'),
(8, 1, 13, 1, 'Grade 11', '1st Semester'),
(35, 1, 13, 2, 'Grade 11', '1st Semester'),
(63, 1, 13, 3, 'Grade 11', '1st Semester'),
(88, 1, 13, 4, 'Grade 11', '1st Semester'),
(111, 1, 13, 5, 'Grade 11', '1st Semester'),
(16, 1, 14, 1, 'Grade 11', '2nd Semester'),
(43, 1, 14, 2, 'Grade 11', '2nd Semester'),
(71, 1, 14, 3, 'Grade 11', '2nd Semester'),
(96, 1, 14, 4, 'Grade 11', '2nd Semester'),
(120, 1, 14, 5, 'Grade 11', '2nd Semester'),
(17, 1, 15, 1, 'Grade 11', '2nd Semester'),
(44, 1, 15, 2, 'Grade 11', '2nd Semester'),
(72, 1, 15, 3, 'Grade 11', '2nd Semester'),
(97, 1, 15, 4, 'Grade 11', '2nd Semester'),
(121, 1, 15, 5, 'Grade 11', '2nd Semester'),
(20, 1, 16, 1, 'Grade 12', '1st Semester'),
(47, 1, 16, 2, 'Grade 12', '1st Semester'),
(75, 1, 16, 3, 'Grade 12', '1st Semester'),
(99, 1, 16, 4, 'Grade 12', '1st Semester'),
(124, 1, 16, 5, 'Grade 12', '1st Semester'),
(9, 1, 17, 1, 'Grade 11', '1st Semester'),
(36, 1, 17, 2, 'Grade 11', '1st Semester'),
(64, 1, 17, 3, 'Grade 11', '1st Semester'),
(89, 1, 17, 4, 'Grade 11', '1st Semester'),
(112, 1, 17, 5, 'Grade 11', '1st Semester'),
(45, 1, 18, 2, 'Grade 11', '2nd Semester'),
(10, 1, 19, 1, 'Grade 11', '1st Semester'),
(18, 1, 20, 1, 'Grade 11', '2nd Semester'),
(19, 1, 21, 1, 'Grade 11', '2nd Semester'),
(21, 1, 22, 1, 'Grade 12', '1st Semester'),
(22, 1, 23, 1, 'Grade 12', '1st Semester'),
(25, 1, 24, 1, 'Grade 12', '2nd Semester'),
(23, 1, 25, 1, 'Grade 12', '1st Semester'),
(26, 1, 26, 1, 'Grade 12', '2nd Semester'),
(48, 1, 27, 2, 'Grade 12', '1st Semester'),
(53, 1, 28, 2, 'Grade 12', '2nd Semester'),
(37, 1, 29, 2, 'Grade 11', '1st Semester'),
(49, 1, 29, 2, 'Grade 12', '1st Semester'),
(50, 1, 30, 2, 'Grade 12', '1st Semester'),
(46, 1, 31, 2, 'Grade 11', '2nd Semester'),
(51, 1, 32, 2, 'Grade 12', '1st Semester'),
(52, 1, 33, 2, 'Grade 12', '1st Semester'),
(54, 1, 34, 2, 'Grade 12', '2nd Semester'),
(65, 1, 35, 3, 'Grade 11', '1st Semester'),
(76, 1, 35, 3, 'Grade 12', '1st Semester'),
(73, 1, 36, 3, 'Grade 11', '2nd Semester'),
(77, 1, 36, 3, 'Grade 12', '1st Semester'),
(74, 1, 37, 3, 'Grade 11', '2nd Semester'),
(78, 1, 37, 3, 'Grade 12', '1st Semester'),
(79, 1, 38, 3, 'Grade 12', '1st Semester'),
(100, 1, 38, 4, 'Grade 12', '1st Semester'),
(90, 1, 39, 4, 'Grade 11', '1st Semester'),
(101, 1, 39, 4, 'Grade 12', '1st Semester'),
(98, 1, 40, 4, 'Grade 11', '2nd Semester'),
(102, 1, 40, 4, 'Grade 12', '1st Semester'),
(113, 1, 41, 5, 'Grade 11', '1st Semester'),
(125, 1, 41, 5, 'Grade 12', '1st Semester'),
(122, 1, 42, 5, 'Grade 11', '2nd Semester'),
(126, 1, 42, 5, 'Grade 12', '1st Semester'),
(114, 1, 43, 5, 'Grade 11', '1st Semester'),
(127, 1, 43, 5, 'Grade 12', '1st Semester'),
(123, 1, 44, 5, 'Grade 11', '2nd Semester'),
(128, 1, 44, 5, 'Grade 12', '1st Semester'),
(27, 1, 45, 1, 'Grade 12', '2nd Semester'),
(55, 1, 45, 2, 'Grade 12', '2nd Semester'),
(80, 1, 45, 3, 'Grade 12', '2nd Semester'),
(103, 1, 45, 4, 'Grade 12', '2nd Semester'),
(129, 1, 45, 5, 'Grade 12', '2nd Semester');

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
(5, 5, 'Provident nihil sun', '2020', 'Consequatur veniam', '2024', '', NULL, '555555555555'),
(6, 6, 'Consequuntur tenetur', '2020', 'Magnam est nostrum l', '2024', '', NULL, '666666666666'),
(7, 7, 'Impedit reprehender', '2020', 'Cupidatat delectus', '2024', '', NULL, '777777777777'),
(10, 10, 'Beatae voluptas faci', '2020', 'Harum voluptate proi', '2024', 'Laborum quos sapient', '2021', '888888888888'),
(11, 11, 'Voluptas optio at s', '2020', 'Consequat Dolore ut', '2024', 'Obcaecati culpa beat', '2021', '999999999999'),
(12, 12, 'Sunt dolore iure deb', '2020', 'Vero ut praesentium', '2024', 'Non ea ratione magni', '2021', '123123123123'),
(13, 13, 'Id suscipit nesciun', '2020', 'Hic ipsa et est ill', '2024', 'Dolorem odit anim es', '2021', '234234234234'),
(14, 14, 'Autem excepteur volu', '2020', 'Autem quis perspicia', '2024', '', NULL, '456456456546'),
(15, 15, 'Voluptates quidem do', '2020', 'Doloribus aut earum', '2024', '', NULL, '678678678678'),
(16, 16, 'Et quia tempora inci', '2020', 'Sed impedit sed deb', '2024', '', NULL, '890890890890');

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
(3, 3, 5, 'Grade 11', '1st Semester', 2, 'Confirmed', 'Enrolled', 6),
(4, 4, 1, 'Grade 11', '1st Semester', 2, 'Pending', 'Application Review', NULL),
(5, 5, 2, 'Grade 11', '1st Semester', 2, 'Confirmed', 'Enrolled', 2),
(6, 6, 3, 'Grade 11', '1st Semester', 2, 'Pending', 'Application Review', NULL),
(7, 7, 1, 'Grade 11', '1st Semester', 2, 'Confirmed', 'Enrolled', 5),
(10, 10, 4, 'Grade 12', '1st Semester', 2, 'Pending', 'Application Review', NULL),
(11, 11, 2, 'Grade 12', '1st Semester', 2, 'Pending', 'Application Review', NULL),
(12, 12, 1, 'Grade 12', '1st Semester', 2, 'Pending', 'Application Review', NULL),
(13, 13, 2, 'Grade 12', '1st Semester', 2, 'Pending', 'Application Review', NULL),
(14, 14, 5, 'Grade 11', '1st Semester', 2, 'Confirmed', 'Enrolled', 6),
(15, 15, 3, 'Grade 11', '1st Semester', 2, 'Pending', 'Application Review', NULL),
(16, 16, 5, 'Grade 11', '1st Semester', 2, 'Confirmed', 'Enrolled', 6);

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
(20, 5, 'Recent 2x2 ID Picture', 'uploads/documents/5_Recent_2x2_ID_Picture_1789827758.jpg', '2026-09-19 14:22:38'),
(21, 6, 'PSA Birth Certificate', 'uploads/documents/6_PSA_Birth_Certificate_1789830784.jpg', '2026-09-19 15:13:04'),
(22, 6, 'Grade 10 Report Card (Form 138)', 'uploads/documents/6_Grade_10_Report_Card__Form_138__1789830784.png', '2026-09-19 15:13:04'),
(23, 6, 'Certificate of Good Moral', 'uploads/documents/6_Certificate_of_Good_Moral_1789830784.jpg', '2026-09-19 15:13:04'),
(24, 6, 'Recent 2x2 ID Picture', 'uploads/documents/6_Recent_2x2_ID_Picture_1789830784.jpg', '2026-09-19 15:13:04'),
(25, 7, 'PSA Birth Certificate', 'uploads/documents/7_PSA_Birth_Certificate_1789831041.jpg', '2026-09-19 15:17:21'),
(26, 7, 'Recent 2x2 ID Picture', 'uploads/documents/7_Recent_2x2_ID_Picture_1789831041.jpg', '2026-09-19 15:17:21'),
(31, 10, 'PSA Birth Certificate', 'uploads/documents/10_PSA_Birth_Certificate_1789832416.jpg', '2026-09-19 15:40:16'),
(32, 10, 'Recent 2x2 ID Picture', 'uploads/documents/10_Recent_2x2_ID_Picture_1789832416.jpg', '2026-09-19 15:40:16'),
(33, 11, 'PSA Birth Certificate', 'uploads/documents/11_PSA_Birth_Certificate_1789832500.jpg', '2026-09-19 15:41:40'),
(34, 11, 'Recent 2x2 ID Picture', 'uploads/documents/11_Recent_2x2_ID_Picture_1789832500.jpg', '2026-09-19 15:41:40'),
(35, 12, 'PSA Birth Certificate', 'uploads/documents/12_PSA_Birth_Certificate_1789832582.jpg', '2026-09-19 15:43:02'),
(36, 12, 'Recent 2x2 ID Picture', 'uploads/documents/12_Recent_2x2_ID_Picture_1789832582.jpg', '2026-09-19 15:43:02'),
(37, 13, 'PSA Birth Certificate', 'uploads/documents/13_PSA_Birth_Certificate_1789832710.jpg', '2026-09-19 15:45:10'),
(38, 13, 'Recent 2x2 ID Picture', 'uploads/documents/13_Recent_2x2_ID_Picture_1789832710.jpg', '2026-09-19 15:45:10'),
(39, 14, 'PSA Birth Certificate', 'uploads/documents/14_PSA_Birth_Certificate_1789833200.jpg', '2026-09-19 15:53:20'),
(40, 14, 'Certificate of Good Moral', 'uploads/documents/14_Certificate_of_Good_Moral_1789833200.jpg', '2026-09-19 15:53:20'),
(41, 14, 'Recent 2x2 ID Picture', 'uploads/documents/14_Recent_2x2_ID_Picture_1789833200.jpg', '2026-09-19 15:53:20'),
(42, 15, 'PSA Birth Certificate', 'uploads/documents/15_PSA_Birth_Certificate_1789833333.jpg', '2026-09-19 15:55:33'),
(43, 15, 'Recent 2x2 ID Picture', 'uploads/documents/15_Recent_2x2_ID_Picture_1789833333.jpg', '2026-09-19 15:55:33'),
(44, 16, 'PSA Birth Certificate', 'uploads/documents/16_PSA_Birth_Certificate_1789833392.jpg', '2026-09-19 15:56:32'),
(45, 16, 'Recent 2x2 ID Picture', 'uploads/documents/16_Recent_2x2_ID_Picture_1789833392.jpg', '2026-09-19 15:56:32');

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
(1, 5, 'link_f1edbe8748e8e3983af72a67', 'https://pm.link/org-mMY44u5kWE4vJdCxVcBRWn3k/test/AB6kvqZ', 100, 'unpaid', '2026-09-19 14:28:28'),
(2, 7, 'link_dc1b3702e44903ccb38a78b7', 'https://pm.link/org-mMY44u5kWE4vJdCxVcBRWn3k/test/HoIBsRN', 100, 'unpaid', '2026-09-19 15:18:09'),
(3, 3, 'link_52cebcea8cd3d6336d7f45ba', 'https://pm.link/org-mMY44u5kWE4vJdCxVcBRWn3k/test/WLQpMDB', 100, 'unpaid', '2026-09-19 15:47:58'),
(4, 14, 'link_61c4c6206d3bdb1f0206d306', 'https://pm.link/org-mMY44u5kWE4vJdCxVcBRWn3k/test/EDwEGAo', 100, 'unpaid', '2026-09-19 15:53:50'),
(5, 16, 'link_8d73e8dd1524543a467bc5e0', 'https://pm.link/org-mMY44u5kWE4vJdCxVcBRWn3k/test/2U6CPmK', 100, 'unpaid', '2026-09-19 15:57:34');

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
(1, 5, 92, 95, 89, 80, 89.00, 'Passed', '2026-09-19 14:28:18'),
(2, 7, 90, 87, 93, 88, 89.50, 'Passed', '2026-09-19 15:17:59'),
(3, 3, 91, 90, 90, 92, 90.75, 'Passed', '2026-09-19 15:47:47'),
(4, 14, 88, 86, 90, 89, 88.25, 'Passed', '2026-09-19 15:53:41'),
(5, 16, 93, 94, 90, 90, 91.75, 'Passed', '2026-09-19 15:56:50');

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

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `enrollment_id`, `schedule_id`, `first_quarter`, `second_quarter`, `final_grade`, `remarks`, `encoded_by`, `updated_at`) VALUES
(1, 3, 86, 91.20, 93.50, 92.35, 'Passed', 21, '2026-09-19 15:48:35'),
(2, 14, 86, 89.20, 85.30, 87.25, 'Passed', 21, '2026-09-19 15:54:22'),
(3, 16, 86, 92.30, 96.90, 94.60, 'Passed', 21, '2026-09-19 15:57:56');

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
(1, 86, 'Active', '2026-09-19 15:06:20', '2026-09-19 15:06:20');

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
(13, 1, 'PROGRAMMING-TOPIC1', '', 'IPT-PRELIM-TOPIC-1.pptx', 'bf18fd3d75a824412dcbbafd6f80efad.pptx', 'uploads/bf18fd3d75a824412dcbbafd6f80efad.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 4435128, 21, '2026-09-19 16:54:53', '2026-09-19 16:54:53'),
(14, 1, 'PROGRAMMING-TOPIC2', '', 'IPT-PRELIM-TOPIC-2.pptx', '1cd065d4430644074ce7ba2de96e4a35.pptx', 'uploads/1cd065d4430644074ce7ba2de96e4a35.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 2885340, 21, '2026-09-19 16:55:21', '2026-09-19 16:55:21'),
(15, 1, 'PROGRAMMING-TOPIC3', '', 'IPT-PRELIM-TOPIC-3.pptx', 'b5ff235214f07dbebf877fc48032f457.pptx', 'uploads/b5ff235214f07dbebf877fc48032f457.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 2956812, 21, '2026-09-19 16:55:35', '2026-09-19 16:55:35'),
(16, 1, 'PROGRAMMING-TOPIC4', '', 'IPT-PRELIM-TOPIC-4.pptx', 'f13367c18988c2eda25ddb82424b4b84.pptx', 'uploads/f13367c18988c2eda25ddb82424b4b84.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 3253007, 21, '2026-09-19 16:55:42', '2026-09-19 16:55:42');

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
(1, 21, 86, 'Quiz#1', 10, NULL, NULL, 'Published', '2026-09-19 15:05:45'),
(2, 21, 86, 'Programming', 10, NULL, NULL, 'Published', '2026-09-19 16:52:56'),
(3, 21, 86, 'Quiz#2', 5, NULL, NULL, 'Published', '2026-09-19 16:59:24'),
(4, 21, 86, 'Quiz#3', 10, NULL, NULL, 'Published', '2026-09-19 17:13:58'),
(5, 21, 86, 'Quiz Sample', 10, NULL, NULL, 'Published', '2026-09-19 17:35:08');

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

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`attempt_id`, `quiz_id`, `student_id`, `status`, `started_at`, `submitted_at`, `total_items`, `correct_count`, `score`) VALUES
(1, 2, 3, 'Submitted', '2026-09-19 16:53:15', '2026-09-19 16:53:20', 2, 2, 100.00),
(2, 1, 3, 'Submitted', '2026-09-19 16:57:12', '2026-09-19 16:57:57', 2, 2, 100.00),
(3, 3, 3, 'Submitted', '2026-09-19 16:59:34', '2026-09-19 17:14:10', 1, 0, 0.00),
(4, 4, 3, 'Submitted', '2026-09-19 17:14:16', '2026-09-19 17:34:31', 3, 0, 0.00),
(5, 5, 3, 'Submitted', '2026-09-19 17:35:18', '2026-09-19 17:35:27', 1, 1, 100.00);

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

--
-- Dumping data for table `quiz_attempt_answers`
--

INSERT INTO `quiz_attempt_answers` (`attempt_answer_id`, `attempt_id`, `question_id`, `selected_answer`, `is_correct`) VALUES
(1, 1, 3, 'A', 1),
(2, 1, 4, 'A', 1),
(3, 2, 1, 'A', 1),
(4, 2, 2, 'D', 1),
(5, 3, 5, NULL, 0),
(6, 4, 6, NULL, 0),
(7, 4, 7, NULL, 0),
(8, 4, 8, NULL, 0),
(9, 5, 9, 'B', 1);

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
(1, 1, 1, 'What is HTML?', 'Lumpiang Turon', 'Lumpiang Shanghai', 'Dynamite', 'Kikiam ni Kit', 'A'),
(2, 1, 2, 'What is CSS?', 'Palad ni Sir Calaycay', 'Bulsa ni Sir Calaycay', 'Medyas ni Sir Calaycay', 'T-Shirt na Blue ni Sir Calaycay', 'D'),
(3, 2, 1, 'HTML', 'hypertext', 'wrong', 'wrong', 'wrong', 'A'),
(4, 2, 2, 'css', 'cascading', 'wrong', 'wrong', 'wrong', 'A'),
(5, 3, 1, 'JS', 'wrong', 'javascript', 'wrong', 'wrong', 'B'),
(6, 4, 1, 'html', 'wrong', 'hypertext', 'wrong', 'wrong', 'B'),
(7, 4, 2, 'css', 'cascading', 'wrong', 'wrong', 'wrong', 'D'),
(8, 4, 3, 'js', 'wrong', 'wrong', 'wrong', 'javaa', 'C'),
(9, 5, 1, 'quiz', 'wrong', 'correct', 'wrong', 'wrong', 'B');

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

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`section_id`, `strand_id`, `grade_level`, `school_year_id`, `section_name`, `capacity`, `room_id`) VALUES
(2, 2, 'Grade 11', 2, 'AMBER', 40, 1),
(3, 4, 'Grade 11', 2, 'TOPAZ', 40, 2),
(4, 3, 'Grade 11', 2, 'BERYL', 40, 3),
(5, 1, 'Grade 11', 2, 'AMETHYST', 40, 4),
(6, 5, 'Grade 11', 2, 'JACINTH', 40, 5);

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
(3, '202600003', 'EX2026-0003', 'Existing', 'Doyle', 'Faith Mckee', 'Tamara Pittman', 'Ad nihil exercitatio', '1978-04-25', 'Male', '09278682456', 'chrlznft@gmail.com', 'Ullamco qui doloribu', '$2y$10$053N2UezHyxO9aKRpiCwouIkhlTWkYZaphikxD4qrF7od8nMRmvZK'),
(4, NULL, NULL, 'New', 'James', 'Macon Wall', 'Rebecca Hartman', 'Ex optio sint ad i', '1987-07-31', 'Female', '09278682456', 'centperaman4@gmail.com', 'Consectetur et fugi', NULL),
(5, '202600001', 'EX2026-0001', 'Existing', 'Chastity Meyer', 'Finn Delacruz', 'Hermione Roach', 'Consequat Dolor ali', '2007-03-17', 'Male', '09278682456', 'pascuanoah@gmail.com', 'Non et a voluptatibu', '$2y$10$PacrTmt6whwNhCak1LzNb.o1cZaUTLGq2pEgDg8JwCq0MwxqUeyye'),
(6, NULL, NULL, 'New', 'Sage Sullivan', 'Ciara Boyle', 'Quemby Henderson', 'Rerum atque quisquam', '2002-04-24', 'Male', '09278682456', 'angeloaga000@gmail.com', 'Alias qui voluptas s', NULL),
(7, '202600002', 'EX2026-0002', 'Existing', 'Vance Horton', 'Ignacia Carson', 'Rhiannon Jarvis', 'Tempora eos dignissi', '1986-02-09', 'Male', '09278682456', 'batosaimalupet@gmail.com', 'Aut soluta iste qui', '$2y$10$dTyY/DIf0UNreOlKDIzrY.xrX3ESGhyPAtmSvePgd8SbimSE6MfB6'),
(10, NULL, NULL, 'Transferee', 'Winter Kane', 'Sigourney Taylor', 'Stuart Conley', 'Dicta doloribus aut', '2003-09-18', 'Female', '09278682456', 'madumekaba@gmail.com', 'Odit porro harum vel', NULL),
(11, NULL, NULL, 'Transferee', 'Julie Puckett', 'Phyllis Ferguson', 'Quamar Bonner', 'Voluptatem quidem si', '2023-11-21', 'Female', '09278682456', 'boboanghacker@gmail.com', 'Porro libero at odit', NULL),
(12, NULL, NULL, 'Transferee', 'Rafael Bowman', 'Shelley Irwin', 'Dana Davis', 'Ipsum non ad placeat', '2003-01-10', 'Male', '09278682456', 'risenowlin00@gmail.com', 'Est sint eos omnis', NULL),
(13, NULL, NULL, 'Transferee', 'Hilel Pollard', 'Noble Higgins', 'Shad Horne', 'Molestiae ab commodo', '2026-07-02', 'Female', '09278682456', 'moimoisalazar@gmail.com', 'Omnis irure vel mini', NULL),
(14, '202600004', 'EX2026-0004', 'Existing', 'Wynter Beasley', 'Jade Massey', 'Paul Boyd', 'Exercitationem irure', '2009-04-30', 'Male', '09278682456', 'pascuanoah123@gmail.com', 'Labore doloremque mo', '$2y$10$BBuRrQeK56fsTKnaeBb4HuRYWQfBT.fpqYYEz/iBPa603vOqTSyUu'),
(15, NULL, NULL, 'New', 'Demetrius Wilkinson', 'Margaret English', 'Tamara Hendrix', 'Dolore optio pariat', '1985-02-11', 'Male', '09278682456', 'pascua.noah@ncst.edu.ph', 'Do culpa ad et veri', NULL),
(16, '202600005', 'EX2026-0005', 'Existing', 'Madaline Baker', 'Damian Todd', 'Galena Odom', 'Quod ratione ut saep', '1980-04-02', 'Female', '09278682456', 'madalinebaker@gmail.com', 'Quis neque eligendi', '$2y$10$vNf/dlhLdUEnPYaVzWEK..uQpw10lflVknrCV0aA08vtiWJQnYhwK');

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
(15, 5, 'Mother', 'Mari Holder', 'Amelia Lamb', 'Steven Terrell', '', '09278682456', 'Cupiditate unde beat'),
(16, 6, 'Father', 'Orla Clay', 'Dennis Finley', 'Devin Mccoy', 'Aliquip est doloribu', '09278682456', 'Sequi exercitationem'),
(17, 6, 'Mother', 'Christopher Nielsen', 'Jennifer Mason', 'Debra Vang', 'Est corporis quis i', '09278682456', 'Et nesciunt id cons'),
(18, 6, 'Grandmother', 'Linus Preston', 'Anne Merrill', 'Martena Faulkner', '', '09278682456', 'Iusto sit eaque exe'),
(19, 7, 'Father', 'Ronan Sargent', 'Katelyn Wright', 'Cruz Howell', 'Vero aut ullam elige', '09278682456', 'Similique libero qui'),
(20, 7, 'Mother', 'Lenore Burgess', 'Gregory Martin', 'Amity Shields', 'Ab illo velit aut u', '09278682456', 'Deserunt qui fugiat'),
(21, 7, 'Father', 'Whilemina Hays', 'Richard Blackburn', 'William Ball', '', '09278682456', 'Duis aliquid ut plac'),
(28, 10, 'Father', 'Candace Good', 'Ella Talley', 'Ila Camacho', 'Praesentium minim ve', '09278682456', 'Quis aut omnis moles'),
(29, 10, 'Mother', 'Justina Haney', 'Jocelyn Chaney', 'Jonah Vinson', 'Architecto ipsum lib', '09278682456', 'Magna laborum ipsam'),
(30, 10, 'Legal Guardian', 'Brody Howard', 'Chandler Swanson', 'Edan Ward', '', '09278682456', 'Corrupti dolorem vo'),
(31, 11, 'Father', 'Kylan Ballard', 'Rachel Waters', 'Brenna Matthews', 'Inventore non repudi', '09278682456', 'Dolore et sed autem'),
(32, 11, 'Mother', 'Joelle Landry', 'Griffin Kirkland', 'Chaim Moore', 'Consectetur ut offi', '09278682456', 'Blanditiis ut qui ex'),
(33, 11, 'Legal Guardian', 'Sean Mcdaniel', 'Whilemina Cotton', 'Dana Brady', '', '09278682456', 'Lorem veniam quam a'),
(34, 12, 'Father', 'Julie Moore', 'Hilda Herrera', 'Pearl Roth', 'Sit temporibus quibu', '09278682456', 'Culpa dolores et rep'),
(35, 12, 'Mother', 'Alec Skinner', 'Cyrus Rivers', 'Martena Johns', 'Soluta exercitatione', '09278682456', 'Adipisicing autem et'),
(36, 12, 'Brother', 'Zahir Bowen', 'Selma Lancaster', 'Ocean Gonzalez', '', '09278682456', 'Tempor excepteur non'),
(37, 13, 'Father', 'Craig Espinoza', 'Erasmus Wise', 'Honorato Mcgee', 'Explicabo Dicta id', '09278682456', 'Beatae autem cupidat'),
(38, 13, 'Mother', 'Henry Roach', 'Chester Cote', 'Erasmus Bowman', 'Repudiandae velit la', '09278682456', 'Voluptatem non archi'),
(39, 13, 'Grandmother', 'Randall Flynn', 'Megan Alston', 'Shannon Griffin', '', '09278682456', 'In harum aut deserun'),
(40, 14, 'Father', 'Gray Wilkins', 'Daquan Ellis', 'Melissa Riley', 'Voluptatem est iust', '09278682456', 'Duis ipsa est et a'),
(41, 14, 'Mother', 'Paki Morrow', 'Stacy Blanchard', 'Sigourney Holland', 'Commodi perferendis', '09278682456', 'Aute provident offi'),
(42, 14, 'Grandmother', 'Taylor Stout', 'Tamekah Bird', 'Maxine Manning', '', '09278682456', 'Nesciunt voluptatem'),
(43, 15, 'Father', 'Yuri Cross', 'Quincy Thomas', 'Hillary Macdonald', 'At praesentium volup', '09278682456', 'Maxime amet enim vo'),
(44, 15, 'Mother', 'Lacota Rasmussen', 'Regina Weiss', 'August Turner', 'Nihil repellendus F', '09278682456', 'Enim ut sed laborum'),
(45, 15, 'Legal Guardian', 'Leila Holt', 'Malcolm Dunn', 'Octavia English', '', '09278682456', 'Magnam natus sapient'),
(46, 16, 'Father', 'Zorita Savage', 'Hyatt Watts', 'Maggie Harris', 'Nam sunt consequuntu', '09278682456', 'Aut veniam cillum a'),
(47, 16, 'Mother', 'Reece Holder', 'Celeste Hopkins', 'Ruth House', 'Reprehenderit velit', '09278682456', 'Odit nemo magna plac'),
(48, 16, 'Sister', 'Burke Frye', 'Kirsten Bowers', 'Armando Daniel', '', '09278682456', 'Numquam quo necessit');

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
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `curriculum`
--
ALTER TABLE `curriculum`
  MODIFY `curriculum_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `curriculum_versions`
--
ALTER TABLE `curriculum_versions`
  MODIFY `curriculum_version_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `educational_background`
--
ALTER TABLE `educational_background`
  MODIFY `education_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `enrollment_documents`
--
ALTER TABLE `enrollment_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `enrollment_payments`
--
ALTER TABLE `enrollment_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `entrance_exam_results`
--
ALTER TABLE `entrance_exam_results`
  MODIFY `exam_result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `material_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `lms_notifications`
--
ALTER TABLE `lms_notifications`
  MODIFY `notification_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quiz_attempt_answers`
--
ALTER TABLE `quiz_attempt_answers`
  MODIFY `attempt_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `strands`
--
ALTER TABLE `strands`
  MODIFY `strand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `student_family_members`
--
ALTER TABLE `student_family_members`
  MODIFY `family_member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

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
-- Constraints for table `lms_notifications`
--
ALTER TABLE `lms_notifications`
  ADD CONSTRAINT `fk_notification_class` FOREIGN KEY (`lms_class_id`) REFERENCES `lms_classes` (`lms_class_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notification_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `professor_password_resets`
--
ALTER TABLE `professor_password_resets`
  ADD CONSTRAINT `professor_password_resets_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`professor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `class_schedules` (`schedule_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempt_answers`
--
ALTER TABLE `quiz_attempt_answers`
  ADD CONSTRAINT `quiz_attempt_answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`attempt_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempt_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE;

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
