-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2026 at 05:08 PM
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
  `role` enum('Registrar','Admissions','Super Admin') NOT NULL DEFAULT 'Super Admin',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `role`, `password`) VALUES
(1, 'admin', 'Super Admin', '$2y$10$Txso8SZgCVMnhWfAL92WH.zvKMDsmmuAlgWTtg.Rgs6NRQE81OIJe'),
(4, 'noah.pascua', 'Registrar', '$2y$10$hhL9nqcXlRNi4RouYE9CT.Dnt.DbMlGONuQDDe4mS5IYOuLUlSIBS'),
(5, 'qwerty', 'Admissions', '$2y$10$rn1q4WAK08XvldZGjNGbneKoL4b//eAJDH2pC.oBHUEVj8w44I3De'),
(6, 'noahzxc', 'Registrar', '$2y$10$V6EA347mGdMr94lOTGxTqeSYEBJ.jovuqtD434DjsKIMDNg0Go2qm');

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
  `subject_id` int(11) NOT NULL,
  `strand_id` int(11) NOT NULL,
  `grade_level` enum('Grade 11','Grade 12') NOT NULL,
  `semester` enum('1st Semester','2nd Semester') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum`
--

INSERT INTO `curriculum` (`curriculum_id`, `subject_id`, `strand_id`, `grade_level`, `semester`) VALUES
(5, 1, 1, 'Grade 11', '1st Semester'),
(35, 1, 2, 'Grade 11', '1st Semester'),
(58, 1, 3, 'Grade 11', '1st Semester'),
(82, 1, 4, 'Grade 11', '1st Semester'),
(106, 1, 5, 'Grade 11', '1st Semester'),
(9, 2, 1, 'Grade 11', '1st Semester'),
(38, 2, 2, 'Grade 11', '1st Semester'),
(61, 2, 3, 'Grade 11', '1st Semester'),
(85, 2, 4, 'Grade 11', '1st Semester'),
(109, 2, 5, 'Grade 11', '1st Semester'),
(4, 3, 1, 'Grade 11', '1st Semester'),
(1, 4, 1, 'Grade 11', '1st Semester'),
(22, 5, 1, 'Grade 11', '2nd Semester'),
(44, 5, 2, 'Grade 11', '2nd Semester'),
(68, 5, 3, 'Grade 11', '2nd Semester'),
(91, 5, 4, 'Grade 11', '2nd Semester'),
(115, 5, 5, 'Grade 11', '2nd Semester'),
(27, 6, 1, 'Grade 12', '1st Semester'),
(7, 7, 1, 'Grade 11', '1st Semester'),
(37, 7, 2, 'Grade 11', '1st Semester'),
(60, 7, 3, 'Grade 11', '1st Semester'),
(84, 7, 4, 'Grade 11', '1st Semester'),
(108, 7, 5, 'Grade 11', '1st Semester'),
(6, 8, 1, 'Grade 11', '1st Semester'),
(36, 8, 2, 'Grade 11', '1st Semester'),
(59, 8, 3, 'Grade 11', '1st Semester'),
(83, 8, 4, 'Grade 11', '1st Semester'),
(107, 8, 5, 'Grade 11', '1st Semester'),
(21, 9, 1, 'Grade 11', '2nd Semester'),
(43, 9, 2, 'Grade 11', '2nd Semester'),
(66, 9, 3, 'Grade 11', '2nd Semester'),
(90, 9, 4, 'Grade 11', '2nd Semester'),
(114, 9, 5, 'Grade 11', '2nd Semester'),
(26, 10, 1, 'Grade 12', '1st Semester'),
(50, 10, 2, 'Grade 12', '1st Semester'),
(72, 10, 3, 'Grade 12', '1st Semester'),
(98, 10, 4, 'Grade 12', '1st Semester'),
(121, 10, 5, 'Grade 12', '1st Semester'),
(30, 11, 1, 'Grade 12', '2nd Semester'),
(54, 11, 2, 'Grade 12', '2nd Semester'),
(78, 11, 3, 'Grade 12', '2nd Semester'),
(102, 11, 4, 'Grade 12', '2nd Semester'),
(123, 11, 5, 'Grade 12', '2nd Semester'),
(20, 12, 1, 'Grade 11', '2nd Semester'),
(42, 12, 2, 'Grade 11', '2nd Semester'),
(65, 12, 3, 'Grade 11', '2nd Semester'),
(89, 12, 4, 'Grade 11', '2nd Semester'),
(113, 12, 5, 'Grade 11', '2nd Semester'),
(10, 13, 1, 'Grade 11', '1st Semester'),
(24, 14, 1, 'Grade 12', '1st Semester'),
(28, 15, 1, 'Grade 12', '1st Semester'),
(23, 16, 1, 'Grade 12', '1st Semester'),
(48, 16, 2, 'Grade 12', '1st Semester'),
(71, 16, 3, 'Grade 12', '1st Semester'),
(95, 16, 4, 'Grade 12', '1st Semester'),
(119, 16, 5, 'Grade 12', '1st Semester'),
(31, 17, 1, 'Grade 12', '2nd Semester'),
(55, 17, 2, 'Grade 12', '2nd Semester'),
(79, 17, 3, 'Grade 12', '2nd Semester'),
(103, 17, 4, 'Grade 12', '2nd Semester'),
(124, 17, 5, 'Grade 12', '2nd Semester'),
(8, 18, 1, 'Grade 11', '1st Semester'),
(16, 19, 1, 'Grade 11', '2nd Semester'),
(2, 20, 1, 'Grade 11', '1st Semester'),
(17, 21, 1, 'Grade 11', '2nd Semester'),
(3, 22, 1, 'Grade 11', '1st Semester'),
(18, 23, 1, 'Grade 11', '2nd Semester'),
(19, 24, 1, 'Grade 11', '2nd Semester'),
(25, 25, 1, 'Grade 12', '1st Semester'),
(34, 26, 2, 'Grade 11', '1st Semester'),
(41, 27, 2, 'Grade 11', '2nd Semester'),
(40, 28, 2, 'Grade 11', '2nd Semester'),
(49, 29, 2, 'Grade 12', '1st Semester'),
(33, 30, 2, 'Grade 11', '1st Semester'),
(47, 31, 2, 'Grade 12', '1st Semester'),
(57, 32, 3, 'Grade 11', '1st Semester'),
(64, 33, 3, 'Grade 11', '2nd Semester'),
(67, 34, 3, 'Grade 11', '2nd Semester'),
(73, 36, 3, 'Grade 12', '1st Semester'),
(74, 37, 3, 'Grade 12', '1st Semester'),
(81, 38, 4, 'Grade 11', '1st Semester'),
(88, 39, 4, 'Grade 11', '2nd Semester'),
(97, 40, 4, 'Grade 12', '1st Semester'),
(96, 41, 4, 'Grade 12', '1st Semester'),
(105, 42, 5, 'Grade 11', '1st Semester'),
(112, 43, 5, 'Grade 11', '2nd Semester'),
(120, 44, 5, 'Grade 12', '1st Semester'),
(122, 45, 5, 'Grade 12', '2nd Semester');

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
-- Table structure for table `professors`
--

CREATE TABLE `professors` (
  `professor_id` int(11) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`professor_id`, `last_name`, `first_name`, `middle_name`, `contact_number`, `email`) VALUES
(1, 'Santos', 'Juan', 'A', '09170000001', 'juan.santos@school.edu'),
(2, 'Reyes', 'Maria', 'B', '09170000002', 'maria.reyes@school.edu'),
(3, 'Cruz', 'Pedro', 'C', '09170000003', 'pedro.cruz@school.edu'),
(4, 'Garcia', 'Ana', 'D', '09170000004', 'ana.garcia@school.edu'),
(5, 'Lopez', 'Mark', 'E', '09170000005', 'mark.lopez@school.edu'),
(6, 'Torres', 'Grace', 'F', '09170000006', 'grace.torres@school.edu'),
(7, 'Ramos', 'Jose', 'G', '09170000007', 'jose.ramos@school.edu'),
(8, 'Flores', 'Liza', 'H', '09170000008', 'liza.flores@school.edu'),
(9, 'Rivera', 'Paolo', 'I', '09170000009', 'paolo.rivera@school.edu'),
(10, 'Aquino', 'Carla', 'J', '09170000010', 'carla.aquino@school.edu'),
(11, 'Fernandez', 'Leo', 'K', '09170000011', 'leo.fernandez@school.edu'),
(12, 'Mendoza', 'Kim', 'L', '09170000012', 'kim.mendoza@school.edu'),
(13, 'Navarro', 'Brian', 'M', '09170000013', 'brian.navarro@school.edu'),
(14, 'Morales', 'Janice', 'N', '09170000014', 'janice.morales@school.edu'),
(15, 'Domingo', 'Ralph', 'O', '09170000015', 'ralph.domingo@school.edu'),
(16, 'Castro', 'Ella', 'P', '09170000016', 'ella.castro@school.edu'),
(17, 'Bautista', 'Neil', 'Q', '09170000017', 'neil.bautista@school.edu'),
(18, 'Dela Cruz', 'Rose', 'R', '09170000018', 'rose.delacruz@school.edu'),
(19, 'Lim', 'Kevin', 'S', '09170000019', 'kevin.lim@school.edu'),
(20, 'Tan', 'Sophia', 'T', '09170000020', 'sophia.tan@school.edu');

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
  `is_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_years`
--

INSERT INTO `school_years` (`school_year_id`, `school_year`, `is_active`) VALUES
(1, '2025-2026', 0),
(2, '2026-2027', 1);

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
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `units` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `units`) VALUES
(1, 'ORALCOM', 'Oral Communication', 3),
(2, 'READWRITE', 'Reading and Writing', 3),
(3, 'KOMPAN', 'Komunikasyon at Pananaliksik', 3),
(4, '21CLIT', '21st Century Literature', 3),
(5, 'UCSP', 'Understanding Culture, Society and Politics', 3),
(6, 'PHILO', 'Introduction to Philosophy', 3),
(7, 'PERSDEV', 'Personal Development', 2),
(8, 'PEH1', 'Physical Education and Health 1', 2),
(9, 'PEH2', 'Physical Education and Health 2', 2),
(10, 'PEH3', 'Physical Education and Health 3', 2),
(11, 'PEH4', 'Physical Education and Health 4', 2),
(12, 'MIL', 'Media and Information Literacy', 3),
(13, 'STATPROB', 'Statistics and Probability', 3),
(14, 'ELS', 'Earth and Life Science', 3),
(15, 'PPS', 'Physical Science', 3),
(16, 'CPAR', 'Contemporary Philippine Arts', 3),
(17, 'RIZAL', 'Life and Works of Rizal', 3),
(18, 'PRECAL', 'Pre-Calculus', 4),
(19, 'BASICCAL', 'Basic Calculus', 4),
(20, 'GENBIO1', 'General Biology 1', 4),
(21, 'GENBIO2', 'General Biology 2', 4),
(22, 'GENCHEM1', 'General Chemistry 1', 4),
(23, 'GENCHEM2', 'General Chemistry 2', 4),
(24, 'GENPHYS1', 'General Physics 1', 4),
(25, 'GENPHYS2', 'General Physics 2', 4),
(26, 'FABM1', 'Fundamentals of Accountancy 1', 4),
(27, 'FABM2', 'Fundamentals of Accountancy 2', 4),
(28, 'BFM', 'Business Finance', 4),
(29, 'MARKETING', 'Marketing Principles', 3),
(30, 'BUSMATH', 'Business Mathematics', 3),
(31, 'BUSETHICS', 'Business Ethics', 3),
(32, 'DISCIPLINE', 'Discipline and Ideas in Social Sciences', 3),
(33, 'CREATIVEWR', 'Creative Writing', 3),
(34, 'TRENDS', 'Trends, Networks and Critical Thinking', 3),
(35, 'COMMENG', 'Creative Nonfiction', 3),
(36, 'POLGOV', 'Philippine Politics and Governance', 3),
(37, 'WORLDREL', 'World Religions', 3),
(38, 'HUMANITIES', 'Humanities', 3),
(39, 'DISASTER', 'Disaster Readiness', 3),
(40, 'ORGMGT', 'Organization and Management', 3),
(41, 'EMPOWER', 'Empowerment Technologies', 3),
(42, 'ICT1', 'Computer Systems Servicing', 4),
(43, 'ICT2', 'Programming Fundamentals', 4),
(44, 'ICT3', 'Web Development', 4),
(45, 'ICT4', 'Computer Networking', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

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
  ADD UNIQUE KEY `subject_id` (`subject_id`,`strand_id`,`grade_level`,`semester`),
  ADD KEY `strand_id` (`strand_id`);

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
-- Indexes for table `professors`
--
ALTER TABLE `professors`
  ADD PRIMARY KEY (`professor_id`);

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
  ADD UNIQUE KEY `school_year` (`school_year`);

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
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `curriculum`
--
ALTER TABLE `curriculum`
  MODIFY `curriculum_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `educational_background`
--
ALTER TABLE `educational_background`
  MODIFY `education_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollment_documents`
--
ALTER TABLE `enrollment_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entrance_exam_results`
--
ALTER TABLE `entrance_exam_results`
  MODIFY `exam_result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `professors`
--
ALTER TABLE `professors`
  MODIFY `professor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `strands`
--
ALTER TABLE `strands`
  MODIFY `strand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_family_members`
--
ALTER TABLE `student_family_members`
  MODIFY `family_member_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

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
  ADD CONSTRAINT `curriculum_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`strand_id`);

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
