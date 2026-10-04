-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2026 at 04:50 AM
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
-- Database: `nbsc_ojt`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT 'Main Office',
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `department`, `address`, `created_at`) VALUES
(1, 'NBSC IT Dept', 'College of Computer Studies', 'Tankulan, Manolo Fortich, Bukidnon', '2026-08-19 07:00:29');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `technical_score` decimal(5,2) DEFAULT 0.00,
  `work_ethics_score` decimal(5,2) DEFAULT 0.00,
  `communication_score` decimal(5,2) DEFAULT 0.00,
  `punctuality_score` decimal(5,2) DEFAULT 0.00,
  `final_score` decimal(5,2) DEFAULT 0.00,
  `grade_equivalent` varchar(50) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `otp_verified` tinyint(1) DEFAULT 0,
  `otp_signed_at` datetime DEFAULT NULL,
  `otp_ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `predefined_entities`
--

CREATE TABLE `predefined_entities` (
  `id` int(11) NOT NULL,
  `entity_name` varchar(255) NOT NULL,
  `aliases` text DEFAULT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'Other',
  `activity_type` enum('Software','Hardware','Clerical','Other') NOT NULL DEFAULT 'Other',
  `it_related` enum('yes','no') NOT NULL DEFAULT 'yes',
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `predefined_entities`
--

INSERT INTO `predefined_entities` (`id`, `entity_name`, `aliases`, `category`, `activity_type`, `it_related`, `description`, `created_at`, `updated_at`) VALUES
(1, 'PHP', 'php programming|php development|php system', 'Programming', 'Software', 'yes', 'PHP programming and development', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(2, 'MySQL', 'mysql database|mysql db|mysql database management', 'Database', 'Software', 'yes', 'MySQL database activities', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(3, 'Java', 'java programming|java development', 'Programming', 'Software', 'yes', 'Java programming', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(4, 'Python', 'python programming|python development', 'Programming', 'Software', 'yes', 'Python programming', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(5, 'HTML', 'html coding|html development', 'Programming', 'Software', 'yes', 'HTML development', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(6, 'CSS', 'css styling|css development', 'Programming', 'Software', 'yes', 'CSS development', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(7, 'JavaScript', 'javascript|js programming|js development', 'Programming', 'Software', 'yes', 'JavaScript programming', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(8, 'Git', 'git version control', 'Development Tool', 'Software', 'yes', 'Version control', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(9, 'GitHub', 'github repository|github repositories', 'Development Tool', 'Software', 'yes', 'GitHub repository and version control', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(10, 'Microsoft Word', 'ms word|word processing|microsoft word', 'Office Software', 'Software', 'yes', 'Word processing software', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(11, 'Microsoft Excel', 'ms excel|excel spreadsheet|spreadsheet', 'Office Software', 'Software', 'yes', 'Spreadsheet software', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(12, 'Microsoft PowerPoint', 'ms powerpoint|powerpoint|presentation', 'Office Software', 'Software', 'yes', 'Presentation software', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(13, 'Canva', 'canva design|canva editing|canva graphics', 'Design Software', 'Software', 'yes', 'Graphic design software', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(14, 'Windows', 'windows operating system|windows os', 'Operating System', 'Software', 'yes', 'Windows operating system', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(15, 'Windows 10', 'windows ten', 'Operating System', 'Software', 'yes', 'Windows 10 operating system', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(16, 'Windows 11', 'windows eleven', 'Operating System', 'Software', 'yes', 'Windows 11 operating system', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(17, 'Computer Installation', 'pc installation|computer setup|computer configuration', 'Hardware Task', 'Hardware', 'yes', 'Computer installation and setup', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(18, 'Computer Troubleshooting', 'pc troubleshooting|computer repair|pc repair', 'Hardware Task', 'Hardware', 'yes', 'Computer troubleshooting', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(19, 'Network Configuration', 'network setup|network configuration', 'Networking', 'Hardware', 'yes', 'Network configuration', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(20, 'LAN', 'local area network|lan configuration', 'Networking', 'Hardware', 'yes', 'Local area networking', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(21, 'Data Entry', 'data encoding|encoding|encode data', 'Administrative Task', 'Clerical', 'no', 'Data encoding and entry', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(22, 'Document Filing', 'file filing|filing documents|document organization', 'Administrative Task', 'Clerical', 'no', 'Document filing', '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(23, 'Printing', 'print documents|document printing', 'Administrative Task', 'Clerical', 'no', 'Printing documents', '2026-08-22 08:01:41', '2026-08-22 08:01:41');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `week_number` int(11) NOT NULL,
  `file_path` text NOT NULL,
  `ocr_activities` text DEFAULT NULL,
  `supervisor_remarks` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--



-- --------------------------------------------------------

--
-- Table structure for table `report_entities`
--

CREATE TABLE `report_entities` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `entity_name` varchar(255) NOT NULL,
  `canonical_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'Other',
  `activity_type` enum('Software','Hardware','Clerical','Other') NOT NULL DEFAULT 'Other',
  `it_related` enum('yes','no','unknown') NOT NULL DEFAULT 'unknown',
  `source` enum('spacy','predefined','spacy_predefined') NOT NULL DEFAULT 'spacy_predefined',
  `confidence_score` decimal(5,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_entities`
--



-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_number` varchar(100) NOT NULL,
  `program` varchar(50) DEFAULT 'BSIT',
  `company_id` int(11) DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_number`, `program`, `company_id`, `supervisor_id`, `created_at`) VALUES
(1, 1, '20231053', 'BSIT', 1, 1, '2026-08-19 07:00:29'),
(110, 113, '20231969', 'BSIT', NULL, 2, '2026-08-22 08:28:48');

-- --------------------------------------------------------

--
-- Table structure for table `supervisors`
--

CREATE TABLE `supervisors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisors`
--

INSERT INTO `supervisors` (`id`, `user_id`, `company_id`, `created_at`) VALUES
(1, 2, 1, '2026-08-19 07:00:29'),
(2, 112, 1, '2026-08-22 07:37:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('student','supervisor','coordinator','admin') NOT NULL DEFAULT 'student',
  `avatar_url` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `avatar_url`, `created_at`) VALUES
(1, 'Katelyn L. Coming', '20231053@nbsc.edu.ph', NULL, 'student', NULL, '2026-08-19 07:00:29'),
(2, 'Engr. keyt', 'coming.katelyn08@gmail.com', NULL, 'supervisor', NULL, '2026-08-19 07:00:29'),
(3, 'Prof. Coordinator', 'comingkatelyn@gmail.com', NULL, 'coordinator', NULL, '2026-08-19 07:00:29'),
(112, 'Spvr.Sander Perejan', 'sanderperejan@gmail.com', '$2y$10$iX8PH3eCaqwzaLtkWvXgFuIftvNxXVH6046FQ5D9cULARQ1O26aIO', 'supervisor', 'https://lh3.googleusercontent.com/a/ACg8ocLo3ANHFlWgegCn6_J_KrGDO8Txnr27MTKMfkNmYSOLRA5xv9fL=s96-c', '2026-08-22 07:36:22'),
(113, 'Sander Perejan', '20231969@nbsc.edu.ph', '$2y$10$KlzFsdVLaX6yzjeLbqFQx.5VMUwk0n2pPDnrTfZO1dvs8LThI5XGS', 'student', 'https://lh3.googleusercontent.com/a/ACg8ocJZ26hhJE1jpszXnioRlpMOCbGByYSk54nAm7SzZYLz3XV3j0ff=s96-c', '2026-08-22 07:37:13'),
(114, 'Prof. Sander', 'syder844@gmail.com', '$2y$10$ILOV6OumfFksRaBxGkIiGe5MVkKu.5vsuGsPdYgwRL448h8X4/LlG', 'coordinator', 'https://lh3.googleusercontent.com/a/ACg8ocJ8hHv80_HQczTIj3PWOzVJDwzKYAmELAL6yXXxOcZJNkrmTg=s96-c', '2026-08-22 08:26:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `fk_eval_supervisor` (`supervisor_id`);

--
-- Indexes for table `predefined_entities`
--
ALTER TABLE `predefined_entities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_predefined_entity` (`entity_name`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_report_student` (`student_id`);

--
-- Indexes for table `report_entities`
--
ALTER TABLE `report_entities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_report_id` (`report_id`),
  ADD KEY `idx_entity_name` (`entity_name`),
  ADD KEY `idx_canonical_name` (`canonical_name`),
  ADD KEY `idx_it_related` (`it_related`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD KEY `fk_student_company` (`company_id`),
  ADD KEY `fk_student_supervisor` (`supervisor_id`);

--
-- Indexes for table `supervisors`
--
ALTER TABLE `supervisors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `fk_supervisor_company` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `predefined_entities`
--
ALTER TABLE `predefined_entities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `report_entities`
--
ALTER TABLE `report_entities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=392;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `supervisors`
--
ALTER TABLE `supervisors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `fk_eval_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eval_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_report_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `report_entities`
--
ALTER TABLE `report_entities`
  ADD CONSTRAINT `fk_entity_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_student_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `supervisors`
--
ALTER TABLE `supervisors`
  ADD CONSTRAINT `fk_supervisor_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_supervisor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

ALTER TABLE students ADD COLUMN section VARCHAR(20) DEFAULT 'A' AFTER program;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
