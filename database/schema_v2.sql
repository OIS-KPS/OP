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
  `criteria_ratings` text DEFAULT NULL,
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
  `category` enum('IT_TERM','CLERICAL_TERM') NOT NULL,
  `it_related` enum('yes','no') NOT NULL DEFAULT 'yes',
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `predefined_entities`
--
-- Generated from python/data/terms.csv. category is the IT_TERM/CLERICAL_TERM
-- mirror of it_related; activity_type and description were removed by
-- migration_simplify_predefined_entities.sql. Re-seed a live database with
-- database/seed_predefined_entities_from_terms.sql rather than editing this.

INSERT INTO `predefined_entities` (`id`, `entity_name`, `aliases`, `category`, `it_related`, `is_archived`, `created_at`, `updated_at`) VALUES
(1, 'Java', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(2, 'JavaScript', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(3, 'TypeScript', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(4, 'C#', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(5, 'PHP', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(6, 'React', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(7, 'Angular', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(8, 'Vue.js', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(9, 'Django', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(10, 'Flask', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(11, 'Laravel', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(12, 'Spring Boot', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(13, 'Node.js', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(14, 'MySQL', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(15, 'PostgreSQL', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(16, 'MongoDB', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(17, 'SQLite', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(18, 'Oracle Database', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(19, 'Git', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(20, 'GitHub', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(21, 'Docker', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(22, 'Kubernetes', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(23, 'Jenkins', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(24, 'Postman', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(25, 'Jira', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(26, 'Figma', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(27, 'AWS', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(28, 'Microsoft Azure', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(29, 'Google Cloud Platform', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(30, 'Linux', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(31, 'Windows Server', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(32, 'Ubuntu', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(33, 'REST API', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(34, 'Machine Learning', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(35, 'Data Structures', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(36, 'Object-Oriented Programming', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(37, 'Version Control', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(38, 'Agile', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(39, 'Scrum', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(40, 'DevOps', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(41, 'Computer Setup', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(42, 'Computer Assembly', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(43, 'PC Assembly', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(44, 'Hardware Assembly', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(45, 'Hardware Maintenance', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(46, 'Hardware Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(47, 'Hardware Repair', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(48, 'Hardware Troubleshooting', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(49, 'Printer Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(50, 'Printer Repair', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(51, 'Printer Troubleshooting', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(52, 'Printer Driver', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(53, 'Network Troubleshooting', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(54, 'Database Management', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(55, 'Database Schema', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(56, 'Database Queries', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(57, 'Database Seeder', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(58, 'Database Connections', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(59, 'Database Storage', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(60, 'Database Normalization', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(61, 'Schema Backup', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(62, 'System Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(63, 'System Testing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(64, 'System Debugging', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(65, 'System Deployment', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(66, 'System Maintenance', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(67, 'System Encryption', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(68, 'Backend API', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(69, 'API Routes', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(70, 'API Endpoints', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(71, 'Livewire', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(72, 'Spatie', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(73, 'Composer', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(74, 'PhpSpreadsheet', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(75, 'Flutter', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(76, 'Code Debugging', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(77, 'Code Refactoring', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(78, 'Bug Fixing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(79, 'Error Handling', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(80, 'Form Validation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(81, 'Session Management', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(82, 'RBAC', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(83, 'Access Control', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(84, 'Role Assignment', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(85, 'Password Recovery', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(86, 'Password Reset', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(87, 'Email Verification', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(88, 'Email Service', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(89, 'Mailer', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(90, 'Rate Limiting', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(91, 'GitHub Workflow', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(92, 'Git Commits', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(93, 'Git Branches', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(94, 'Merge Conflict', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(95, 'CSS', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(96, 'Responsive Design', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(97, 'UI Components', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(98, 'UI/UX Design', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(99, 'UI Design', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(100, 'UX Design', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(101, 'Dashboard Interface', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(102, 'Adobe Photoshop', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(103, 'Interactive Map', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(104, 'GeoJSON', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(105, 'Geospatial', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(106, 'Web Hosting', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(107, 'InfinityFree', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(108, 'Hosting Environment', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(109, 'Web Server', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(110, 'Server Settings', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(111, 'Server Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(112, 'Deployment Testing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(113, 'Production Deployment', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(114, 'Computer Hardware Maintenance', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(115, 'iClinicSys', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(116, 'eGOV.PH', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(117, 'GraphQL', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(118, 'CAT6 UTP Cable', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(119, 'LAN', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(120, 'system design', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(121, 'UI/UX', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(122, 'database design', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(123, 'data security', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(124, 'authentication system', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(125, 'coding', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(126, 'programming', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(127, 'debugging', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(128, 'troubleshooting', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(129, 'internet connection troubleshooting', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(130, 'preventive maintenance', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(131, 'code versioning', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(132, 'load testing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(133, 'unit testing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(134, 'vulnerability scanning', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(135, '3D printing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(136, 'Canva Editing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(137, 'web deployment', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(138, 'software updates', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(139, 'security assessment', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(140, 'vulnerability assessment', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(141, 'Profiling & Inventory System', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(142, 'Annual Investment Program (AIP) System', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(143, 'PPMP system', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(144, 'HTML', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(145, 'Computer Programming', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(146, 'Software Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(147, 'Software Engineering', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(148, 'Application Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(149, 'Web Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(150, 'Web Application Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(151, 'Mobile Application Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(152, 'Procedural Programming', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(153, 'Structured Programming', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(154, 'Algorithm Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(155, 'Algorithm Design', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(156, 'Code Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(157, 'Code Implementation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(158, 'Code Integration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(159, 'Code Testing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(160, 'Software Testing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(161, 'Software Debugging', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(162, 'Software Maintenance', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(163, 'Software Deployment', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(164, 'Database Administration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(165, 'Database Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(166, 'Database Implementation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(167, 'Database Querying', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(168, 'Database Security', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(169, 'Database Backup', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(170, 'Database Recovery', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(171, 'Data Modeling', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(172, 'Data Model', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(173, 'Entity Relationship Diagram', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(174, 'ER Diagram', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(175, 'SQL', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(176, 'SQL Queries', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(177, 'SQL Database', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(178, 'Relational Database', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(179, 'Database Server', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(180, 'Database Connection', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(181, 'Database Migration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(182, 'Data Integration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(183, 'Systems Analysis', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(184, 'System Analysis', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(185, 'Systems Design', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(186, 'Systems Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(187, 'System Implementation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(188, 'System Integration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(189, 'System Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(190, 'System Administration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(191, 'System Documentation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(192, 'System Architecture', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(193, 'System Requirements', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(194, 'Requirements Analysis', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(195, 'Requirements Gathering', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(196, 'Software Requirements', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(197, 'Functional Requirements', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(198, 'Computer Networking', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(199, 'Computer Network', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(200, 'Network Administration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(201, 'Network Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(202, 'Network Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(203, 'Network Maintenance', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(204, 'Network Security', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(205, 'Network Infrastructure', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(206, 'Network Devices', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(207, 'Network Connection', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(208, 'Network Monitoring', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(209, 'Network Setup', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(210, 'WAN', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(211, 'IP Address', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(212, 'IPv4', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(213, 'IPv6', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(214, 'TCP/IP', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(215, 'DNS', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(216, 'DHCP', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(217, 'Router Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(218, 'Switch Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(219, 'Router Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(220, 'Network Cabling', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(221, 'Ethernet', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(222, 'Ethernet Cable', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(223, 'CAT6 Cable', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(224, 'Wi-Fi Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(225, 'Computer Hardware', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(226, 'Hardware Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(227, 'Hardware Testing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(228, 'Hardware Upgrade', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(229, 'Computer Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(230, 'Computer Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(231, 'Computer Repair', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(232, 'Computer Troubleshooting', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(233, 'Peripheral Devices', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(234, 'Computer Peripherals', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(235, 'Motherboard Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(236, 'RAM Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(237, 'RAM Upgrade', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(238, 'Hard Drive Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(239, 'SSD Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(240, 'Power Supply Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(241, 'Printer Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(242, 'Printer Setup', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(243, 'Information Security', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(244, 'Cybersecurity', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(245, 'Computer Security', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(246, 'Information Security Assessment', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(247, 'Security Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(248, 'Security Testing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(249, 'Security Monitoring', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(250, 'Access Management', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(251, 'User Authentication', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(252, 'Authorization', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(253, 'Encryption', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(254, 'Data Encryption', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(255, 'Password Security', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(256, 'Firewall Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(257, 'Security Vulnerability', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(258, 'Vulnerability Testing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(259, 'Web Application', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(260, 'Web System', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(261, 'Web Services', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(262, 'Web Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(263, 'Web Interface', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(264, 'Frontend Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(265, 'Backend Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(266, 'Full-Stack Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(267, 'HTTP', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(268, 'HTTPS', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(269, 'RESTful API', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(270, 'API Development', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(271, 'API Integration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(272, 'API Testing', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(273, 'IT Infrastructure', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(274, 'IT Infrastructure Management', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(275, 'IT System Administration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(276, 'Server Administration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(277, 'Server Management', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(278, 'Server Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(279, 'Server Maintenance', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(280, 'Server Deployment', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(281, 'Operating System Installation', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(282, 'Operating System Configuration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(283, 'Operating System Maintenance', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(284, 'Linux Administration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(285, 'Windows Administration', NULL, 'IT_TERM', 'yes', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(286, 'Data Entry', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(287, 'Entry Data', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(288, 'Document Organization', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(289, 'Document Filing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(290, 'Document Sorting', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(291, 'Document Arrangement', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(292, 'Document Handling', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(293, 'Document Preparation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(294, 'Document Distribution', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(295, 'Document Routing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(296, 'Document Review', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(297, 'Document Checking', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(298, 'Document Processing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(299, 'Document Forwarding', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(300, 'Document Delivery', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(301, 'Document Safekeeping', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(302, 'Document Labeling', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(303, 'File Organization', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(304, 'File Arrangement', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(305, 'File Filing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(306, 'File Checking', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(307, 'Folder Organization', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(308, 'Filing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(309, 'Record Management', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(310, 'Record Keeping', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(311, 'Record Organization', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(312, 'Record Checking', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(313, 'Records Management', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(314, 'Incoming Documents', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(315, 'Data Correction', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(316, 'Data Recording', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(317, 'Data Consolidation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(318, 'Data Organization', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(319, 'Data Verification', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(320, 'Savings Accounts', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(321, 'Shares Accounts', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(322, 'Account Balances', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(323, 'Financial Transactions', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(324, 'Payment Records', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(325, 'Collection Reports', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(326, 'Financial Records', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(327, 'Beneficiary Records', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(328, 'Scholarship Data', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(329, 'PhilHealth Eligibility', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(330, 'Client Payments', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(331, 'Medicine Distribution', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(332, 'Medicine Labeling', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(333, 'Stock Transfer', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(334, 'Transfer Slips', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(335, 'Document Stamping', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(336, 'Certificate Distribution', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(337, 'Certificate Release', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(338, 'ID Release', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(339, 'ID Distribution', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(340, 'Form Distribution', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(341, 'Flyer Distribution', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(342, 'Promotional Materials', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(343, 'Office Documentation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(344, 'Office Coordination', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(345, 'Meeting Materials', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(346, 'Meeting Proceedings', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(347, 'Student Enrollment', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(348, 'Scholarship Records', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(349, 'Police Clearance', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(350, 'Audit Preparation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(351, 'Clearance Forms', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(352, 'Exit Clearance Forms', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(353, 'Public Notices', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(354, 'Report Preparation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(355, 'Activity Documentation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(356, 'Photo Documentation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(357, 'Attendance Records', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(358, 'Event Coordination', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(359, 'Event Preparation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(360, 'Health Records', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(361, 'Medical Inventory', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(362, 'Client Satisfaction Survey', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(363, 'Inventory Counting', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(364, 'Inventory Organization', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(365, 'Certificate Recognition', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(366, 'OPT Plus', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(367, 'Presentation Materials', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(368, 'Meeting Transcription', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(369, 'Materials Preparation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(370, 'Material Distribution', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(371, 'Inventory Analysis', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(372, 'Photocopying', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(373, 'log books', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(374, 'contracts', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(375, 'received documents', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(376, 'routed documents', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(377, 'physical documents', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(378, 'important documents', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(379, 'passbooks', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(380, 'PESO office book', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(381, 'forms', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(382, 'ID cards', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(383, 'remarks', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(384, 'encoding', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(385, 'bookkeeping', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(386, 'evaluation data', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(387, 'Data evaluation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(388, 'PPMP', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(389, 'financial/ledger accounts', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(390, 'document scanning', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(391, 'Data Input', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(392, 'Data Encoding', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(393, 'Record Filing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(394, 'Records Filing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(395, 'File Management', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(396, 'Folder Management', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(397, 'Document Management', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(398, 'Document Recording', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(399, 'Document Encoding', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(400, 'Document Registration', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(401, 'Document Tracking', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(402, 'Document Retrieval', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(403, 'File Retrieval', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(404, 'Record Retrieval', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(405, 'Record Updating', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(406, 'Record Encoding', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(407, 'Record Recording', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(408, 'Record Verification', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(409, 'Record Processing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(410, 'Record Maintenance', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(411, 'Records Processing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(412, 'Records Verification', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(413, 'Records Updating', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(414, 'Information Encoding', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(415, 'Information Recording', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(416, 'Information Verification', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(417, 'Information Updating', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(418, 'Data Checking', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(419, 'Data Updating', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(420, 'Data Filing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(421, 'Data Compilation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(422, 'Data Transcription', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(423, 'Document Compilation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(424, 'Document Verification', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(425, 'File Processing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(426, 'File Verification', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(427, 'File Sorting', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(428, 'Record Sorting', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(429, 'Record Arrangement', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(430, 'Record Documentation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(431, 'Application Forms', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(432, 'Registration Forms', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(433, 'Request Forms', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(434, 'Submission Forms', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(435, 'Payment Transactions', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(436, 'Inventory Data', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(437, 'Inventory Reports', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(438, 'Stock Inventory', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(439, 'Attendance Data', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(440, 'Attendance Sheets', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(441, 'Attendance Forms', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(442, 'Evaluation Forms', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(443, 'Evaluation Records', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(444, 'Evaluation Reports', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(445, 'Clearance Processing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(446, 'Clearance Verification', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(447, 'Certificate Processing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(448, 'Certificate Preparation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(449, 'Certificate Verification', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(450, 'Certificate Issuance', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(451, 'ID Preparation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(452, 'ID Processing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(453, 'ID Verification', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(454, 'Office Filing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(455, 'Office Filing System', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(456, 'Office Records Management', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(457, 'Administrative Filing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(458, 'Administrative Processing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(459, 'Administrative Documentation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(460, 'Administrative Support', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(461, 'Office Assistance', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(462, 'Office Assistance Activities', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(463, 'Scheduling', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(464, 'Appointment Scheduling', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(465, 'Meeting Scheduling', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(466, 'Meeting Preparation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(467, 'Minutes Preparation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(468, 'Minutes of Meeting', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(469, 'Correspondence', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(470, 'Email Correspondence', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(471, 'Letter Preparation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(472, 'Letter Distribution', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(473, 'Receiving Documents', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(474, 'Document Receiving', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(475, 'Document Submission', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(476, 'Document Release', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(477, 'Form Processing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(478, 'Form Checking', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(479, 'Form Verification', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(480, 'Form Preparation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(481, 'Form Completion', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(482, 'Photocopying Documents', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(483, 'Printing Documents', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(484, 'Scanning Documents', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(485, 'Report Compilation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(486, 'Report Filing', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(487, 'Report Submission', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(488, 'Report Distribution', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27'),
(489, 'Report Documentation', NULL, 'CLERICAL_TERM', 'no', 0, '2026-10-16 11:15:27', '2026-10-16 11:15:27');
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
