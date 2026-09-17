-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2026 at 02:06 PM
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
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `role`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 113, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:37:26'),
(2, 113, 'student', 'REPORT_SUBMISSION', 'Student submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:37:38'),
(3, 113, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:39:19'),
(4, 114, 'coordinator', 'GOOGLE_LOGIN', 'User Syder (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:39:27'),
(5, 114, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:40:08'),
(6, 113, 'student', 'LOGIN_FAILED', 'Incorrect password entered for user 20231969@nbsc.edu.ph.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:40:18'),
(7, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 11:58:40'),
(8, 1, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 12:02:27'),
(9, 1, 'student', 'REPORT_SUBMISSION', 'Student submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 12:03:11'),
(10, 2, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 12:04:13'),
(11, 1, 'student', 'REPORT_SUBMISSION', 'Student submitted Week 2 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 14:15:26'),
(12, 1, 'student', 'REPORT_SUBMISSION', 'Student submitted Week 3 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 15:04:06'),
(13, 1, 'student', 'REPORT_SUBMISSION', 'Student submitted Week 4 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 16:14:24'),
(14, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 22:54:26'),
(15, 2, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 20:50:58'),
(16, 1, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 20:51:54'),
(17, 1, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 09:54:47'),
(18, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 10:05:59'),
(19, 2, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 10:27:53'),
(20, 1, 'student', 'REPORT_REUPLOAD', 'Student submitted Week 4 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 11:12:46'),
(21, 1, 'student', 'REPORT_SUBMISSION', 'Student Katelyn L. Coming submitted revised Week 4 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 11:36:18'),
(22, 1, 'student', 'REPORT_SUBMISSION', 'Student Katelyn L. Coming submitted Week 5 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:26:01'),
(23, 1, 'student', 'REPORT_SUBMISSION', 'Student Katelyn L. Coming submitted Week 6 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 13:17:35'),
(24, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 15:26:23'),
(25, 2, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 15:34:40'),
(26, 1, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 15:39:39'),
(27, 1, 'student', 'REPORT_SUBMISSION', 'Student Katelyn L. Coming submitted Week 7 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 15:41:07'),
(28, 3, 'coordinator', 'PLACEMENT_UNLINKED', 'Removed placement link for student ID 110.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 15:50:02'),
(29, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 20:06:39'),
(30, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 20:07:35'),
(31, 2, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-15 20:51:35'),
(32, 2, 'supervisor', 'USER_LOGOUT', 'User Engr. keyt logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-15 20:51:41'),
(33, 2, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 10:46:52'),
(34, 1, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 10:53:40'),
(35, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 10:54:57'),
(36, 3, 'coordinator', 'STUDENT_CREATED', 'Added student Mariel Coming (20260704 - Sec B).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 11:12:47'),
(37, 115, 'student', 'GOOGLE_LOGIN', 'User Mariel Coming (20260704@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 11:15:16'),
(38, 3, 'coordinator', 'USER_LOGOUT', 'User Prof. Coordinator logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 12:07:25'),
(39, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 12:07:33'),
(40, 1, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 17:49:46'),
(41, NULL, 'guest', 'GOOGLE_LOGIN_FAILED', 'Google OAuth failed for unknown: Google Auth Error: Bad Request', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 18:50:20'),
(42, 1, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 18:50:33');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `department`, `address`, `created_at`) VALUES
(1, 'NBSC IT Dept', 'College of Computer Studies', 'Tankulan, Manolo Fortich, Bukidnon', '2026-08-19 07:00:29'),
(2, 'NBSC ICTMO', 'Main Office', NULL, '2026-09-16 11:26:39'),
(3, 'NBSC SASDD', 'Main Office', NULL, '2026-09-16 11:33:37');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_otps`
--

CREATE TABLE `evaluation_otps` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `supervisor_user_id` int(11) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
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
  `previous_file_path` text DEFAULT NULL,
  `ocr_activities` text DEFAULT NULL,
  `supervisor_remarks` text DEFAULT NULL,
  `revision_requested_at` datetime DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `student_id`, `week_number`, `file_path`, `previous_file_path`, `ocr_activities`, `supervisor_remarks`, `revision_requested_at`, `status`, `submitted_at`, `approved_at`, `updated_at`) VALUES
(1, 110, 1, 'uploads/reports/WAR_Week_1_Student_110_1788921458.pdf', NULL, NULL, NULL, NULL, 'pending', '2026-09-08 22:37:38', NULL, '2026-09-08 22:37:38'),
(2, 1, 1, 'uploads/reports/WAR_Week_1_Student_1_1788926591.pdf', NULL, NULL, NULL, NULL, 'approved', '2026-09-09 12:03:11', '2026-09-09 13:45:23', '2026-09-09 13:45:23'),
(3, 1, 2, 'uploads/reports/WAR_Week_2_Student_1_1788934526.pdf', NULL, NULL, NULL, NULL, 'approved', '2026-09-09 14:15:26', '2026-09-09 14:17:27', '2026-09-09 14:17:27'),
(4, 1, 3, 'uploads/reports/WAR_Week_3_Student_1_1788937446.pdf', NULL, NULL, NULL, NULL, 'approved', '2026-09-09 15:04:06', '2026-09-09 16:23:47', '2026-09-09 16:23:47'),
(5, 1, 4, 'uploads/reports/WAR_Week_4_Student_1_1789184178.pdf', 'uploads/reports/WAR_Week_4_Student_1_1789182766.pdf', 'fgdfg', NULL, NULL, 'approved', '2026-09-12 11:36:18', '2026-09-12 12:25:08', '2026-09-12 12:25:08'),
(6, 1, 5, 'uploads/reports/WAR_Week_5_Student_1_1789187161.pdf', NULL, NULL, NULL, NULL, 'approved', '2026-09-12 12:26:01', '2026-09-12 12:55:40', '2026-09-12 12:55:40'),
(7, 1, 6, 'uploads/reports/WAR_Week_6_Student_1_1789190255.pdf', NULL, NULL, NULL, NULL, 'approved', '2026-09-12 13:17:35', '2026-09-12 15:34:45', '2026-09-12 15:34:45'),
(8, 1, 7, 'uploads/reports/WAR_Week_7_Student_1_1789198867.pdf', NULL, NULL, NULL, NULL, 'approved', '2026-09-12 15:41:07', '2026-09-12 15:41:53', '2026-09-12 15:41:53');

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
  `confidence_score` decimal(5,2) DEFAULT 100.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_entities`
--

INSERT INTO `report_entities` (`id`, `report_id`, `entity_name`, `canonical_name`, `category`, `activity_type`, `it_related`, `source`, `confidence_score`, `created_at`, `is_archived`) VALUES
(1, 2, 'Microsoft Excel', 'Microsoft Excel', 'Office Software', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 12:05:12', 0),
(2, 2, 'JavaScript', 'JavaScript', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 12:05:12', 0),
(3, 2, 'Windows 11', 'Windows 11', 'Operating System', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 12:05:12', 0),
(4, 2, 'GitHub', 'GitHub', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 12:05:12', 0),
(5, 2, 'MySQL', 'MySQL', 'Database', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 12:05:12', 0),
(6, 2, 'Git', 'Git', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 12:05:12', 0),
(7, 2, 'PHP', 'PHP', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 12:05:12', 0),
(8, 3, 'Microsoft Excel', 'Microsoft Excel', 'Office Software', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 14:16:41', 0),
(9, 3, 'JavaScript', 'JavaScript', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 14:16:41', 0),
(10, 3, 'Windows 11', 'Windows 11', 'Operating System', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 14:16:41', 0),
(11, 3, 'GitHub', 'GitHub', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 14:16:41', 0),
(12, 3, 'MySQL', 'MySQL', 'Database', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 14:16:41', 0),
(13, 3, 'Git', 'Git', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 14:16:41', 0),
(14, 3, 'PHP', 'PHP', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 14:16:41', 0),
(15, 4, 'Microsoft Excel', 'Microsoft Excel', 'Office Software', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 16:20:56', 0),
(16, 4, 'JavaScript', 'JavaScript', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 16:20:56', 0),
(17, 4, 'Windows 11', 'Windows 11', 'Operating System', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 16:20:56', 0),
(18, 4, 'GitHub', 'GitHub', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 16:20:56', 0),
(19, 4, 'MySQL', 'MySQL', 'Database', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 16:20:56', 0),
(20, 4, 'Git', 'Git', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 16:20:56', 0),
(21, 4, 'PHP', 'PHP', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 16:20:56', 0),
(22, 5, 'Microsoft Excel', 'Microsoft Excel', 'Office Software', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 20:22:54', 0),
(23, 5, 'JavaScript', 'JavaScript', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 20:22:54', 0),
(24, 5, 'Windows 11', 'Windows 11', 'Operating System', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 20:22:54', 0),
(25, 5, 'GitHub', 'GitHub', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 20:22:54', 0),
(26, 5, 'MySQL', 'MySQL', 'Database', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 20:22:54', 0),
(27, 5, 'Git', 'Git', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 20:22:54', 0),
(28, 5, 'PHP', 'PHP', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-09 20:22:54', 0),
(29, 6, 'Microsoft Excel', 'Microsoft Excel', 'Office Software', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 12:43:10', 0),
(30, 6, 'JavaScript', 'JavaScript', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 12:43:10', 0),
(31, 6, 'Windows 11', 'Windows 11', 'Operating System', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 12:43:10', 0),
(32, 6, 'GitHub', 'GitHub', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 12:43:10', 0),
(33, 6, 'MySQL', 'MySQL', 'Database', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 12:43:10', 0),
(34, 6, 'Git', 'Git', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 12:43:10', 0),
(35, 6, 'PHP', 'PHP', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 12:43:10', 0),
(36, 7, 'Microsoft Excel', 'Microsoft Excel', 'Office Software', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 13:53:56', 0),
(37, 7, 'JavaScript', 'JavaScript', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 13:53:56', 0),
(38, 7, 'Windows 11', 'Windows 11', 'Operating System', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 13:53:56', 0),
(39, 7, 'GitHub', 'GitHub', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 13:53:56', 0),
(40, 7, 'MySQL', 'MySQL', 'Database', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 13:53:56', 0),
(41, 7, 'Git', 'Git', 'Development Tool', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 13:53:56', 0),
(42, 7, 'PHP', 'PHP', 'Programming', 'Software', 'yes', 'predefined', 100.00, '2026-09-12 13:53:56', 0),
(44, 8, 'Data Entry', 'Data Entry', 'Administrative Task', 'Clerical', 'no', 'predefined', 100.00, '2026-09-12 15:41:34', 0),
(45, 8, 'Microsoft Excel', 'Microsoft Excel', 'Clerical (Non-IT)', 'Clerical', 'no', 'spacy_predefined', 100.00, '2026-09-16 12:50:44', 0);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_number` varchar(100) NOT NULL,
  `program` varchar(50) DEFAULT 'BSIT',
  `section` varchar(20) NOT NULL DEFAULT 'A',
  `company_id` int(11) DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `completion_requested` tinyint(1) DEFAULT 0,
  `evaluation_triggered` tinyint(1) DEFAULT 0,
  `requested_company_name` varchar(255) DEFAULT NULL,
  `requested_supervisor_name` varchar(255) DEFAULT NULL,
  `requested_supervisor_email` varchar(255) DEFAULT NULL,
  `placement_request_status` enum('none','pending','approved','rejected') NOT NULL DEFAULT 'none',
  `placement_rejection_reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_number`, `program`, `section`, `company_id`, `supervisor_id`, `completion_requested`, `requested_company_name`, `requested_supervisor_name`, `requested_supervisor_email`, `placement_request_status`, `placement_rejection_reason`, `created_at`) VALUES
(1, 1, '20231053', 'BSIT', 'A', 1, 1, 0, NULL, NULL, NULL, 'none', NULL, '2026-08-19 07:00:29'),
(110, 113, '20231969', 'BSIT', 'B', NULL, NULL, 0, NULL, NULL, NULL, 'none', NULL, '2026-08-22 08:28:48'),
(111, 115, '20260704', 'BSIT', 'B', 3, 3, 0, NULL, NULL, NULL, 'approved', NULL, '2026-09-16 11:12:47');

-- --------------------------------------------------------

--
-- Table structure for table `supervisors`
--

CREATE TABLE `supervisors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supervisors`
--

INSERT INTO `supervisors` (`id`, `user_id`, `company_id`, `created_at`) VALUES
(1, 2, 1, '2026-08-19 07:00:29'),
(2, 112, 1, '2026-08-22 07:37:47'),
(3, 116, 3, '2026-09-16 11:33:37');

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
  `status` enum('active','archived') NOT NULL DEFAULT 'active',
  `archived_at` datetime DEFAULT NULL,
  `avatar_url` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `status`, `archived_at`, `avatar_url`, `created_at`) VALUES
(1, 'Katelyn L. Coming', '20231053@nbsc.edu.ph', '$2y$10$T7KgXoAXCcsP2uvr8SM.iOV/AP4oO8DkZonEUmsudaoP31U4DO2wm', 'student', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocLHAgOCZRItKzWsxXbfAJwljTysZbGLYmB_K6E5xwPUGoU2FxE=s96-c', '2026-08-19 07:00:29'),
(2, 'Engr. keyt', 'coming.katelyn08@gmail.com', '$2y$10$YwqN2J3LaNPxL3IbXaylteQ72d.5p2TDVBFzlXW3Z7yLWuQrJFHHy', 'supervisor', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJ-qSrJ1WSY2ELXl0_gIow48m-CXPP60j8c8NF8mYJcGjd0oQ=s96-c', '2026-08-19 07:00:29'),
(3, 'Prof. Coordinator', 'comingkatelyn@gmail.com', '$2y$10$77qvQjWUTPLFjy9h0hC9b.0zu1nJvUdpJA950GwQIe4CNaoqGPWgq', 'coordinator', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocIPjVqHwnhTZSamq2pJykgRZBiZfo5Aj_4Gqy0l41oGXJMcYA=s96-c', '2026-08-19 07:00:29'),
(112, 'Spvr. Sander Perejan', 'sanderperejan@gmail.com', '$2y$10$iX8PH3eCaqwzaLtkWvXgFuIftvNxXVH6046FQ5D9cULARQ1O26aIO', 'supervisor', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocLo3ANHFlWgegCn6_J_KrGDO8Txnr27MTKMfkNmYSOLRA5xv9fL=s96-c', '2026-08-22 07:36:22'),
(113, 'Sander Perejan', '20231969@nbsc.edu.ph', '$2y$10$KlzFsdVLaX6yzjeLbqFQx.5VMUwk0n2pPDnrTfZO1dvs8LThI5XGS', 'student', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJZ26hhJE1jpszXnioRlpMOCbGByYSk54nAm7SzZYLz3XV3j0ff=s96-c', '2026-08-22 07:37:13'),
(114, 'Prof. Sander', 'syder844@gmail.com', '$2y$10$ILOV6OumfFksRaBxGkIiGe5MVkKu.5vsuGsPdYgwRL448h8X4/LlG', 'coordinator', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJ8hHv80_HQczTIj3PWOzVJDwzKYAmELAL6yXXxOcZJNkrmTg=s96-c', '2026-08-22 08:26:29'),
(115, 'Mariel Coming', '20260704@nbsc.edu.ph', '$2y$10$WUx8qI.avbxhM7tFmQyKnOgEnZxSbSdjPRss5fsZhz/G8SX81kE.G', 'student', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocLs68G_ZwgRg2TExj7QBpxyLORqrTB7NHoFMtMCi3wm1m6AHQ=s96-c', '2026-09-16 11:12:47'),
(116, 'Mars Comsss', 'katec@gmail.com', NULL, 'supervisor', 'active', NULL, NULL, '2026-09-16 11:33:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_user` (`user_id`);

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
  ADD UNIQUE KEY `uq_eval_student` (`student_id`),
  ADD KEY `fk_eval_supervisor` (`supervisor_id`);

--
-- Indexes for table `evaluation_otps`
--
ALTER TABLE `evaluation_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_eval_otp` (`student_id`,`supervisor_user_id`),
  ADD KEY `fk_eval_otp_supervisor` (`supervisor_user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reset_token` (`token`),
  ADD KEY `fk_reset_token_user` (`user_id`);

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
  ADD UNIQUE KEY `uq_student_user` (`user_id`),
  ADD UNIQUE KEY `uq_student_number` (`student_number`),
  ADD KEY `fk_student_company` (`company_id`),
  ADD KEY `fk_student_supervisor` (`supervisor_id`);

--
-- Indexes for table `supervisors`
--
ALTER TABLE `supervisors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_supervisor_user` (`user_id`),
  ADD KEY `fk_supervisor_company` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluation_otps`
--
ALTER TABLE `evaluation_otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `report_entities`
--
ALTER TABLE `report_entities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `supervisors`
--
ALTER TABLE `supervisors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `fk_eval_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eval_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `evaluation_otps`
--
ALTER TABLE `evaluation_otps`
  ADD CONSTRAINT `fk_eval_otp_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eval_otp_supervisor` FOREIGN KEY (`supervisor_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `fk_reset_token_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
