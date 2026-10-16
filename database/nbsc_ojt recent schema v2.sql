-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2026 at 05:53 AM
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
(1, NULL, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:37:26'),
(2, NULL, 'student', 'REPORT_SUBMISSION', 'Student submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:37:38'),
(3, NULL, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:39:19'),
(4, NULL, 'coordinator', 'GOOGLE_LOGIN', 'User Syder (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:39:27'),
(5, NULL, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:40:08'),
(6, NULL, 'student', 'LOGIN_FAILED', 'Incorrect password entered for user 20231969@nbsc.edu.ph.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 22:40:18'),
(7, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 11:58:40'),
(8, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 12:02:27'),
(9, NULL, 'student', 'REPORT_SUBMISSION', 'Student submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 12:03:11'),
(10, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 12:04:13'),
(11, NULL, 'student', 'REPORT_SUBMISSION', 'Student submitted Week 2 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 14:15:26'),
(12, NULL, 'student', 'REPORT_SUBMISSION', 'Student submitted Week 3 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 15:04:06'),
(13, NULL, 'student', 'REPORT_SUBMISSION', 'Student submitted Week 4 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 16:14:24'),
(14, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 22:54:26'),
(15, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 20:50:58'),
(16, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-10 20:51:54'),
(17, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 09:54:47'),
(18, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 10:05:59'),
(19, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 10:27:53'),
(20, NULL, 'student', 'REPORT_REUPLOAD', 'Student submitted Week 4 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 11:12:46'),
(21, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn L. Coming submitted revised Week 4 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 11:36:18'),
(22, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn L. Coming submitted Week 5 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 12:26:01'),
(23, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn L. Coming submitted Week 6 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 13:17:35'),
(24, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 15:26:23'),
(25, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 15:34:40'),
(26, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 15:39:39'),
(27, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn L. Coming submitted Week 7 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 15:41:07'),
(28, 3, 'coordinator', 'PLACEMENT_UNLINKED', 'Removed placement link for student ID 110.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 15:50:02'),
(29, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 20:06:39'),
(30, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-12 20:07:35'),
(31, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-15 20:51:35'),
(32, NULL, 'supervisor', 'USER_LOGOUT', 'User Engr. keyt logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-15 20:51:41'),
(33, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 10:46:52'),
(34, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 10:53:40'),
(35, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 10:54:57'),
(36, 3, 'coordinator', 'STUDENT_CREATED', 'Added student Mariel Coming (20260704 - Sec B).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 11:12:47'),
(37, NULL, 'student', 'GOOGLE_LOGIN', 'User Mariel Coming (20260704@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 11:15:16'),
(38, 3, 'coordinator', 'USER_LOGOUT', 'User Prof. Coordinator logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 12:07:25'),
(39, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 12:07:33'),
(40, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 17:49:46'),
(41, NULL, 'guest', 'GOOGLE_LOGIN_FAILED', 'Google OAuth failed for unknown: Google Auth Error: Bad Request', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 18:50:20'),
(42, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 18:50:33'),
(43, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 20:12:39'),
(44, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 20:15:12'),
(45, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 20:15:46'),
(46, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 09:49:56'),
(47, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 09:54:17'),
(48, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 20:39:48'),
(49, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 21:11:03'),
(50, 3, 'coordinator', 'EVAL_TRIGGERED', 'Coordinator triggered final evaluation request for student Katelyn L. Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 21:11:24'),
(51, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 21:13:40'),
(52, NULL, 'supervisor', 'USER_LOGOUT', 'User Engr. keyt logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 22:20:31'),
(53, NULL, 'supervisor', 'LOGIN_FAILED', 'Incorrect password entered for user coming.katelyn08@gmail.com.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 22:20:49'),
(54, NULL, 'supervisor', 'USER_LOGIN', 'User Engr. keyt (coming.katelyn08@gmail.com) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 22:21:07'),
(55, 3, 'coordinator', 'EVAL_TRIGGERED', 'Coordinator triggered final evaluation request for student Mariel Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 22:24:52'),
(56, 3, 'coordinator', 'STUDENT_ASSIGNED', 'Assigned student ID 110 to company ID 3 and supervisor ID 3.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-18 22:41:10'),
(57, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 09:06:45'),
(58, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 09:08:06'),
(59, 3, 'coordinator', 'EVAL_TRIGGERED', 'Coordinator triggered final evaluation request for student Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 09:12:46'),
(60, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 115.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 09:19:11'),
(61, NULL, 'guest', 'GOOGLE_LOGIN_DENIED', 'Unregistered Google account blocked: Mariel Coming (20260704@nbsc.edu.ph)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 09:19:48'),
(62, 3, 'coordinator', 'STUDENT_CREATED', 'Added student Mariel Coming (20260704 - Sec C).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 09:24:16'),
(63, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 11:42:15'),
(64, NULL, 'student', 'GOOGLE_LOGIN', 'User Mariel Coming (20260704@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 13:35:15'),
(65, NULL, 'student', 'REPORT_SUBMISSION', 'Student Mariel Coming submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 13:36:15'),
(66, NULL, 'student', 'REPORT_SUBMISSION', 'Student Mariel Coming submitted Week 2 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 13:44:20'),
(67, 3, 'coordinator', 'EVAL_TRIGGERED', 'Coordinator triggered final evaluation request for student Mariel Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 13:49:13'),
(68, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 117.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 14:09:19'),
(70, NULL, 'guest', 'GOOGLE_LOGIN_DENIED', 'Unregistered Google account blocked: Mariel Coming (20260704@nbsc.edu.ph)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 14:19:18'),
(71, 3, 'coordinator', 'STUDENT_CREATED', 'Added student Mariel Coming (20260704 - Sec C).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 14:20:01'),
(72, NULL, 'student', 'GOOGLE_LOGIN', 'User Mariel Coming (20260704@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 14:20:28'),
(73, NULL, 'student', 'GOOGLE_LOGIN', 'User Mariel Coming (20260704@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-20 14:21:26'),
(74, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 09:25:07'),
(75, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 09:52:02'),
(76, NULL, 'supervisor', 'USER_LOGOUT', 'User Engr. keyt logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 14:06:57'),
(77, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-01 14:08:29'),
(78, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-04 12:57:53'),
(79, NULL, 'supervisor', 'USER_LOGOUT', 'User Engr. keyt logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-04 12:58:03'),
(80, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-04 12:58:13'),
(81, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-04 12:58:26'),
(82, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-04 14:23:56'),
(83, NULL, 'student', 'USER_LOGOUT', 'User Katelyn L. Coming logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-04 14:33:20'),
(84, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-04 14:33:30'),
(85, NULL, 'student', 'USER_LOGOUT', 'User Katelyn L. Coming logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-04 14:33:40'),
(86, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-04 14:33:59'),
(87, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:01:00'),
(88, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:02:00'),
(89, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:02:18'),
(90, NULL, 'student', 'GOOGLE_LOGIN', 'User Mariel Coming (20260704@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:03:13'),
(91, NULL, 'student', 'REPORT_SUBMISSION', 'Student Mariel Coming submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:03:27'),
(92, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 1.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:25:06'),
(93, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 118.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:25:10'),
(94, 3, 'coordinator', 'STUDENT_CREATED', 'Added student Katelyn Coming (20231053 - Sec A).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:31:48'),
(95, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:32:44'),
(96, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:33:54'),
(97, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 1 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:35:28'),
(98, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 2 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 11:46:29'),
(99, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 3 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 12:04:12'),
(100, NULL, 'supervisor', 'REPORT_REVISION', 'Supervisor requested changes on Week 3 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 12:05:48'),
(101, NULL, 'supervisor', 'REPORT_REVISION', 'Supervisor requested changes on Week 2 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 12:06:19'),
(102, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 4 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 12:14:36'),
(103, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 5 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 12:35:33'),
(104, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 6 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 13:31:37'),
(105, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 7 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 13:38:09'),
(106, NULL, 'supervisor', 'SUPERVISOR_PROFILE_UPDATED', 'Supervisor updated their job title and contact number.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 14:56:22'),
(107, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 112.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 15:22:24'),
(108, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 8 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 17:42:43'),
(109, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added NBSC with supervisor mariel coming (keytc8c@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 18:29:22'),
(110, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted revised Week 3 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:52:52'),
(111, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted revised Week 2 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:53:09'),
(112, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 2 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:53:38'),
(113, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 3 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:54:02'),
(114, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 4 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:54:11'),
(115, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 5 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:54:16'),
(116, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 6 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:54:22'),
(117, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 7 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:54:27'),
(118, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 8 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:55:33'),
(119, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 9 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:55:53'),
(120, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 10 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:56:00'),
(121, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 11 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:56:07'),
(122, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 12 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:56:16'),
(123, NULL, 'student', 'REPORT_SUBMISSION', 'Student Katelyn Coming submitted Week 13 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:56:29'),
(124, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 9 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:58:07'),
(125, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 10 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:58:21'),
(126, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 11 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:58:31'),
(127, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 12 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:58:39'),
(128, NULL, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 13 accomplishment report for Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 19:58:47'),
(129, 3, 'coordinator', 'EVAL_TRIGGERED', 'Coordinator triggered final evaluation request for student Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 20:19:05'),
(130, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added NBSC with supervisor Katelyn Coming (comingkatelyn@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 20:26:17'),
(131, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:10:21'),
(132, NULL, 'student', 'USER_LOGOUT', 'User Katelyn Coming logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:10:43'),
(133, NULL, 'guest', 'GOOGLE_LOGIN_DENIED', 'Unregistered Google account blocked: Mariel Coming (20260704@nbsc.edu.ph)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:11:02'),
(134, 3, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:12:07'),
(135, 3, 'supervisor', 'USER_LOGOUT', 'User Katelyn Coming logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:14:51'),
(136, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:15:00'),
(137, 3, 'coordinator', 'STUDENT_CREATED', 'Added student Mariel Coming (20260704 - Sec B).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:21:51'),
(138, NULL, 'student', 'GOOGLE_LOGIN', 'User Mariel Coming (20260704@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:22:10'),
(139, NULL, 'student', 'REPORT_SUBMISSION', 'Student Mariel Coming submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:23:42'),
(140, NULL, 'supervisor', 'GOOGLE_LOGIN', 'User Katelyn Coming (coming.katelyn08@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:34:03'),
(141, 3, 'coordinator', 'STUDENT_ASSIGNED', 'Assigned student ID 115 to company ID 1 and supervisor ID 1.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:43:39'),
(142, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 121.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:55:34'),
(143, 3, 'coordinator', 'STUDENT_CREATED', 'Added student Mariel Coming (20260704 - Sec A).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:56:07'),
(144, NULL, 'student', 'GOOGLE_LOGIN', 'User Mariel Coming (20260704@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:56:42'),
(145, 3, 'coordinator', 'STUDENT_ASSIGNED', 'Assigned student ID 116 to company ID 1 and supervisor ID 1.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 12:00:04'),
(146, NULL, 'student', 'USER_LOGOUT', 'User Mariel Coming logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 12:04:39'),
(147, NULL, 'student', 'GOOGLE_LOGIN', 'User Mariel Coming (20260704@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 12:04:48'),
(148, NULL, 'student', 'USER_LOGOUT', 'User Mariel Coming logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 12:09:52'),
(149, NULL, 'student', 'GOOGLE_LOGIN', 'User Mariel Coming (20260704@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 12:10:03'),
(150, NULL, 'student', 'REPORT_SUBMISSION', 'Student Mariel Coming submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 12:11:36'),
(151, 3, 'coordinator', 'EVAL_REQUEST_CANCELLED', 'Coordinator cancelled the final evaluation request for student Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 12:20:19'),
(152, NULL, 'student', 'GOOGLE_LOGIN', 'User Katelyn Coming (20231053@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 12:20:57'),
(153, 3, 'coordinator', 'EVAL_TRIGGERED', 'Coordinator triggered final evaluation request for student Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 13:50:56'),
(154, 3, 'coordinator', 'EVAL_REQUEST_CANCELLED', 'Coordinator cancelled the final evaluation request for student Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 13:51:21'),
(155, 3, 'coordinator', 'EVAL_TRIGGERED', 'Coordinator triggered final evaluation request for student Katelyn Coming.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 13:51:33'),
(156, 3, 'coordinator', 'EVAL_REQUEST_CANCELLED', 'Coordinator cancelled the final evaluation request for student Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:36:08'),
(157, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 114.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:38:47'),
(158, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 120.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:39:04'),
(159, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 122.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:40:19'),
(160, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 113.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:40:22'),
(161, 3, 'coordinator', 'COMPANY_ARCHIVED', 'Archived company \'NBSC\' (ID 4).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:52:01'),
(162, 3, 'coordinator', 'COMPANY_ARCHIVED', 'Archived company \'NBSC\' (ID 5).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:52:08'),
(163, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 119.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:54:22'),
(164, 3, 'coordinator', 'COMPANY_ARCHIVED', 'Archived company \'NBSC IT Dept\' (ID 1).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:54:36'),
(165, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 2.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:54:45'),
(166, 3, 'coordinator', 'COMPANY_ARCHIVED', 'Archived company \'NBSC SASDD\' (ID 3).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:55:17'),
(167, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 116.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:55:23'),
(168, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added Northern Bukidnon State College (ICTMO) with supervisor Katelyn Coming (coming.katelyn08@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:57:25'),
(169, 3, 'coordinator', 'COMPANY_ARCHIVED', 'Archived company \'Northern Bukidnon State College\' (ID 6).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:58:54'),
(170, 3, 'coordinator', 'COMPANY_RESTORED', 'Restored company \'Northern Bukidnon State College\' (ID 6).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:59:01'),
(171, 3, 'coordinator', 'COMPANY_ARCHIVED', 'Archived company \'Northern Bukidnon State College\' (ID 6).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 15:59:51'),
(172, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added Northern Bukidnon State College (ICTMO) with supervisor Pau Coms (coming.katelyn08@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:00:30'),
(173, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added Northern Bukidnon State College (ICTMO) with supervisor Katelyn Coming (coming.katelyn@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:03:38'),
(174, 3, 'coordinator', 'COMPANY_ARCHIVED', 'Archived company \'Northern Bukidnon State College\' (ID 8).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:04:44'),
(175, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 124.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:04:53'),
(176, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added Northern Bukidnon State College (ICTMO) with supervisor adsadas (comingkateclyn@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:05:12'),
(177, 3, 'coordinator', 'COMPANY_ARCHIVED', 'Archived company \'Northern Bukidnon State College\' (ID 9).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:06:23'),
(178, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 125.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:06:27'),
(179, 3, 'coordinator', 'STUDENT_CREATED', 'Added student Katelyn Coming (20231053 - Sec A).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:07:30'),
(180, 3, 'coordinator', 'PLACEMENT_UNLINKED', 'Removed placement link for student ID 117.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:09:21'),
(181, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Katelyn Coming (comingkatelyn@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:42:49'),
(182, 3, 'coordinator', 'COMPANY_ARCHIVED', 'Archived company \'Northern Bukidnon State College\' (ID 7).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:43:49'),
(183, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 123.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:43:53'),
(184, 3, 'coordinator', 'COMPANY_DELETED', 'Permanently deleted company \'NBSC IT Dept\' (ID 1).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:44:31'),
(185, 3, 'coordinator', 'COMPANY_DELETED', 'Permanently deleted company \'NBSC\' (ID 5).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:44:34'),
(186, 3, 'coordinator', 'COMPANY_DELETED', 'Permanently deleted company \'Northern Bukidnon State College\' (ID 7).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:44:48'),
(187, 3, 'coordinator', 'COMPANY_DELETED', 'Permanently deleted company \'Northern Bukidnon State College\' (ID 9).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:44:52'),
(188, 3, 'coordinator', 'COMPANY_DELETED', 'Permanently deleted company \'Northern Bukidnon State College\' (ID 8).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:44:55'),
(189, 3, 'coordinator', 'COMPANY_DELETED', 'Permanently deleted company \'Northern Bukidnon State College\' (ID 6).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:44:58'),
(190, 3, 'coordinator', 'COMPANY_DELETED', 'Permanently deleted company \'NBSC SASDD\' (ID 3).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:45:00'),
(191, 3, 'coordinator', 'COMPANY_DELETED', 'Permanently deleted company \'NBSC\' (ID 4).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:45:04'),
(192, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added Northern Bukidnon State College (ICTMO) with supervisor Katelyn Coming (coming.katelyn08@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:45:32'),
(193, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added Northern Bukidnon State College (HRMO) with supervisor Mariel Coms (marielcoms@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:49:09'),
(194, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 128.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:55:12'),
(195, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added Northern Bukidnon State College (IT Dept) with supervisor Mariel Coms (marcoms@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 16:56:55'),
(196, 3, 'coordinator', 'COMPANY_ARCHIVED', 'Archived company \'Northern Bukidnon State College\' (ID 10).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:12:33'),
(197, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 127.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:12:43'),
(198, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 129.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:12:46');
INSERT INTO `audit_logs` (`id`, `user_id`, `role`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(199, 3, 'coordinator', 'COMPANY_DELETED', 'Permanently deleted company \'Northern Bukidnon State College\' (ID 10).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:13:13'),
(200, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added Northern Bukidnon State College (ICTMO) with supervisor Katelyn Coming (coming.katelyn08@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:13:29'),
(201, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added Northern Bukidnon State College (IT Dept) with supervisor Mariel Coming (marielcoms@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:15:27'),
(202, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 131.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:16:24'),
(203, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added Northern Bukidnon State College (IT Dept) with supervisor Mariel Coms (marcoms@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:18:01'),
(204, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added FICCO (HRMO) with supervisor Pau Coms (paucoms@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:19:50'),
(205, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 130.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:22:03'),
(206, 3, 'coordinator', 'OFFICE_DELETED', 'Deleted office \'ICTMO\' at \'Northern Bukidnon State College\' (ID 22).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:37:17'),
(207, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added LGU (ICTMO) with supervisor Kie COms (kiecoms@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:38:53'),
(208, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'LGU\' (ID 13).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:41:05'),
(209, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'LGU\' (ID 13).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:41:24'),
(210, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'LGUS\' (ID 13).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:41:32'),
(211, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'Northern Bukidnon State College\' (ID 13).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:41:44'),
(212, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'Northern Bukidnon State College\' (ID 11).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:42:43'),
(213, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'FICCO\' (ID 13).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:43:03'),
(214, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'LGU\' (ID 12).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:43:37'),
(215, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'Northern Bukidnon State College\' (ID 12).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:43:54'),
(216, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'LGU\' (ID 12).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:44:22'),
(217, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'LGU\' (ID 11).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:44:48'),
(218, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'FICCO\' (ID 12).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:45:07'),
(219, 3, 'coordinator', 'COMPANY_ARCHIVED', 'Archived company \'FICCO\' (ID 13).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:52:04'),
(220, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 134.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:52:11'),
(221, 3, 'coordinator', 'COMPANY_DELETED', 'Permanently deleted company \'FICCO\' (ID 13).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:52:21'),
(222, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added FICCO (ICTMO) with supervisor KKK cm (km@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 17:55:51'),
(223, 3, 'coordinator', 'COMPANY_UPDATED', 'Updated company \'FICCO\' (ID 12).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 18:59:30'),
(225, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 10:39:43'),
(226, NULL, 'guest', 'GOOGLE_LOGIN_DENIED', 'Unregistered Google account blocked: Sander Perejan (20231969@nbsc.edu.ph)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:42:02'),
(227, 132, 'coordinator', 'STUDENT_CREATED', 'Added student Sander Perejan (20231969 - Sec B).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 10:42:31'),
(228, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 10:43:53'),
(229, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 10:58:30'),
(230, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 10:58:57'),
(231, 132, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added NBSC (ICTMO) with supervisor Sander Perejan (sanderperejan@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:00:45'),
(232, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:01:05'),
(233, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:01:30'),
(234, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:03:46'),
(235, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:04:06'),
(236, 132, 'coordinator', 'STUDENT_ASSIGNED', 'Assigned student ID 118 to office ID 28 (company ID 14, supervisor ID 20).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:04:24'),
(237, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:08:46'),
(238, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:12:28'),
(239, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:12:35'),
(240, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 1 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:12:41'),
(241, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:12:44'),
(242, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:12:53'),
(243, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:14:15'),
(244, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 2 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:15:35'),
(245, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 3 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:15:41'),
(246, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 4 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:15:46'),
(247, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 5 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:15:50'),
(248, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 6 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:15:57'),
(249, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 7 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:16:02'),
(250, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 8 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:16:08'),
(251, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 9 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:16:17'),
(252, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 10 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:16:23'),
(253, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:16:44'),
(254, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 10 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:17:11'),
(255, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 2 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:17:19'),
(256, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 3 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:17:25'),
(257, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 5 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:17:33'),
(258, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 4 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:17:54'),
(259, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 6 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:18:10'),
(260, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 7 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:18:19'),
(261, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:18:22'),
(262, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-09 11:18:31'),
(263, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 8 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:18:48'),
(264, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 9 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:18:56'),
(265, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:18:59'),
(266, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:19:59'),
(267, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:21:02'),
(268, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:21:10'),
(269, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:27:50'),
(270, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 11:27:57'),
(271, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:48:02'),
(272, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:54:18'),
(273, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:54:25'),
(274, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:58:43'),
(275, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:58:53'),
(276, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:59:12'),
(277, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:59:22'),
(278, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:18:50'),
(279, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:18:56'),
(280, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:21:38'),
(281, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:21:46'),
(282, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 11 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:22:44'),
(283, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:23:37'),
(284, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:23:43'),
(285, 137, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 07:45:45'),
(286, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 12 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 07:50:06'),
(287, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 13 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 08:06:06'),
(288, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 14 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 08:06:56'),
(289, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 15 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 08:11:08'),
(290, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 16 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 08:48:57'),
(291, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 17 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 08:52:32'),
(292, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 18 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 08:56:21'),
(293, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 19 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 09:11:19'),
(294, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 20 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 09:13:35'),
(295, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 21 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 09:22:44'),
(296, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:34:57'),
(297, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:35:07'),
(298, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 21 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:35:20'),
(299, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 21 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:35:20'),
(300, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:35:22'),
(301, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:35:30'),
(302, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:36:36'),
(303, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:36:45'),
(304, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 22 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 09:38:40'),
(305, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:40:11'),
(306, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:40:20'),
(307, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:49:00'),
(308, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:49:09'),
(309, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 23 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 09:49:13'),
(310, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 24 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 09:50:27'),
(311, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:51:21'),
(312, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:51:29'),
(313, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:52:08'),
(314, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 09:52:33'),
(315, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 25 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 09:55:41'),
(316, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 10:05:53'),
(317, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 10:06:00'),
(318, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 26 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 10:07:13'),
(319, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 10:14:58'),
(320, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 10:15:07'),
(321, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 27 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 10:18:35'),
(322, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 28 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 10:19:06'),
(323, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 29 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 11:00:20'),
(324, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 30 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-10 11:00:53'),
(325, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 03:46:06'),
(326, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 03:47:05'),
(327, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 03:49:16'),
(328, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 30 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 03:49:36'),
(329, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 03:58:53'),
(330, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 03:59:06'),
(331, 138, 'supervisor', 'USER_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-11 03:59:59'),
(332, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 29 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-11 04:00:10'),
(333, 137, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-11 04:01:18'),
(334, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 31 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0', '2026-10-11 04:01:41'),
(335, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 08:35:27'),
(336, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 08:55:17'),
(337, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 08:55:34'),
(338, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 08:55:56'),
(339, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 08:56:03'),
(340, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 08:56:31'),
(341, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 08:56:57'),
(342, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 08:57:09'),
(343, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 08:57:22'),
(344, 138, 'supervisor', 'USER_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-11 08:58:17'),
(345, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-11 09:03:56'),
(346, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-11 09:04:04'),
(347, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-11 09:21:15'),
(348, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-11 09:21:24'),
(349, 137, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 21:27:03'),
(350, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 21:27:11'),
(351, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 21:27:23'),
(352, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 21:27:27'),
(353, NULL, 'guest', 'GOOGLE_LOGIN_DENIED', 'Unregistered Google account blocked: sunny (sunny2012.x@gmail.com)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 21:27:48'),
(354, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 21:28:03'),
(355, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 32 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 21:29:19'),
(356, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 22:53:57'),
(357, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 23:23:40'),
(358, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-11 23:23:47'),
(359, 138, 'supervisor', 'USER_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 02:15:00'),
(360, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 04:33:04'),
(361, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 04:47:12'),
(362, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 04:47:25'),
(363, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 04:47:40'),
(364, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 04:47:49'),
(365, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 33 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 04:49:44'),
(366, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 04:58:44'),
(367, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 04:59:14'),
(368, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 08:51:40'),
(369, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-03 09:01:44'),
(370, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-03 09:01:53'),
(371, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-03 09:04:11'),
(372, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-03 09:04:19'),
(373, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 34 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-03 09:04:27'),
(374, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 09:13:35'),
(375, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 09:13:43'),
(376, 137, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 09:13:48'),
(377, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 09:15:17'),
(378, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 09:15:31'),
(379, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 09:15:42'),
(380, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 1 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 09:16:56'),
(381, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 09:17:01'),
(382, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-09 09:17:11'),
(383, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 2 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 09:25:09'),
(384, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 09:25:31'),
(385, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 09:27:36'),
(386, 138, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 2 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 09:27:55'),
(387, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 09:28:00'),
(388, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-26 09:28:12'),
(389, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 3 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-11-03 09:29:39'),
(390, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 22:09:24'),
(391, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 4 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-11-09 22:12:00'),
(392, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-11-09 22:12:10'),
(393, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-11-09 22:12:40'),
(394, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 02:30:01'),
(395, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 02:55:27'),
(396, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 02:55:39'),
(397, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 08:24:37'),
(398, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 08:43:14');
INSERT INTO `audit_logs` (`id`, `user_id`, `role`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(399, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 08:43:21'),
(400, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 08:47:21'),
(401, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 08:47:31'),
(402, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 09:49:30'),
(403, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 09:49:38'),
(404, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 09:51:02'),
(405, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 09:51:11'),
(406, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 09:54:20'),
(407, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 09:54:27'),
(408, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 09:59:36'),
(409, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 09:59:43'),
(412, 132, 'coordinator', 'STUDENT_ASSIGNED', 'Assigned Katelyn Coming to office ID 26 (company ID 12, supervisor ID 18).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 10:35:22'),
(413, 132, 'coordinator', 'PLACEMENT_UNLINKED', 'Removed placement link for 2 student(s): Katelyn Coming, Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 10:37:50'),
(414, 132, 'coordinator', 'STUDENT_ASSIGNED', 'Assigned Sander Perejan to office ID 28 (company ID 14, supervisor ID 20).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 10:38:02'),
(415, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 19:28:34'),
(416, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 21:11:09'),
(417, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 21:49:50'),
(418, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 5 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 22:19:54'),
(419, 137, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 6 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 22:41:25'),
(420, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 22:42:03'),
(421, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 22:42:23'),
(422, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 23:27:36'),
(423, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 23:27:46'),
(424, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 23:29:39'),
(425, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-14 23:29:49');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT 'Main Office',
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` enum('active','archived') NOT NULL DEFAULT 'active',
  `archived_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `department`, `address`, `created_at`, `status`, `archived_at`) VALUES
(11, 'LGU', 'ICTMOS', 'Tankulan, Manolo Fortich Bukidnon', '2026-10-09 17:13:29', 'active', NULL),
(12, 'FICCO', 'HRMO', 'Kihare, Manolo Fortich Bukidnon', '2026-10-09 17:19:50', 'active', NULL),
(14, 'NBSC', 'ICTMO', 'tankulan, manolo fortich, bukidnon', '2026-10-09 11:00:45', 'active', NULL);

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
-- Table structure for table `evaluations_legacy_backup`
--

CREATE TABLE `evaluations_legacy_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
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
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`id`, `company_id`, `name`, `address`, `created_at`) VALUES
(23, 11, 'IT Dept', 'Tankulan, Manolo Fortich Bukidnon', '2026-10-09 17:15:27'),
(24, 12, 'HRMO', 'Kihare, Manolo Fortich Bukidnon', '2026-10-09 17:19:50'),
(26, 12, 'ICTMO', 'Kihare, Manolo Fortich Bukidnon', '2026-10-09 17:55:51'),
(27, 12, 'Billing and Accounting Office', 'Kihare, Manolo Fortich Bukidnon', '2026-10-09 18:59:30'),
(28, 14, 'ICTMO', 'tankulan, manolo fortich, bukidnon', '2026-10-09 11:00:45');

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
(27, 'student complaints', 'student complaints', 'task', 'Clerical', 'no', NULL, 0, '2026-10-11 08:58:01', '2026-10-11 09:03:21');

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
  `is_late` tinyint(1) NOT NULL DEFAULT 0,
  `deadline_date` date DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `student_id`, `week_number`, `file_path`, `previous_file_path`, `ocr_activities`, `supervisor_remarks`, `revision_requested_at`, `status`, `is_late`, `deadline_date`, `submitted_at`, `approved_at`, `updated_at`) VALUES
(61, 118, 1, 'uploads/reports/WAR_Week_1_Student_118_1791551717.pdf', NULL, NULL, NULL, NULL, 'approved', 0, '2026-10-18', '2026-10-09 09:15:17', '2026-10-09 09:16:56', '2026-10-09 09:16:56'),
(62, 118, 2, 'uploads/reports/WAR_Week_2_Student_118_1793021109.pdf', NULL, NULL, NULL, NULL, 'approved', 1, '2026-10-25', '2026-10-26 09:25:09', '2026-10-26 09:27:55', '2026-10-26 09:27:55'),
(63, 118, 3, 'uploads/reports/WAR_Week_3_Student_118_1793716179.pdf', NULL, NULL, NULL, NULL, 'pending', 1, '2026-11-01', '2026-11-03 09:29:39', NULL, '2026-11-03 09:29:39'),
(64, 118, 4, 'uploads/reports/WAR_Week_4_Student_118_1794280320.pdf', NULL, NULL, NULL, NULL, 'pending', 1, '2026-11-08', '2026-11-09 22:12:00', NULL, '2026-11-09 22:12:00'),
(65, 118, 5, 'uploads/reports/WAR_Week_5_Student_118_1792030794.pdf', NULL, NULL, NULL, NULL, 'pending', 0, '2026-11-15', '2026-10-14 22:19:54', NULL, '2026-10-14 22:19:54'),
(66, 118, 6, 'uploads/reports/WAR_Week_6_Student_118_1792032085.pdf', NULL, NULL, NULL, NULL, 'pending', 0, '2026-11-22', '2026-10-14 22:41:25', NULL, '2026-10-14 22:41:25');

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
  `it_related` enum('yes','no','unknown') NOT NULL DEFAULT 'unknown',
  `source` enum('spacy','predefined','spacy_predefined','manual_addition') NOT NULL DEFAULT 'spacy_predefined',
  `reclassified_at` datetime DEFAULT NULL,
  `original_it_related` varchar(10) DEFAULT NULL,
  `confidence_score` decimal(5,2) DEFAULT 100.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_entities`
--

INSERT INTO `report_entities` (`id`, `report_id`, `entity_name`, `canonical_name`, `category`, `it_related`, `source`, `reclassified_at`, `original_it_related`, `confidence_score`, `created_at`, `is_archived`) VALUES
(488, 64, 'System', 'System', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 22:43:02', 0),
(489, 64, 'Forms', 'Forms', 'CLERICAL_TERM', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-14 22:43:02', 0),
(490, 64, 'student complaints', 'student complaints', 'CLERICAL_TERM', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-14 22:43:02', 0),
(491, 64, 'Violation', 'Violation', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 98.37, '2026-10-14 22:43:02', 0),
(492, 66, 'Hardcopy Records', 'Hardcopy Records', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 22:48:54', 0),
(493, 66, 'Excel\nDatabase', 'Excel\nDatabase', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 22:48:54', 0),
(494, 66, 'encoding', 'Data Entry', 'CLERICAL_TERM', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-14 22:48:54', 0),
(495, 66, 'Encoding Beneficiary Information', 'Encoding Beneficiary Information', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 22:48:54', 0),
(496, 66, 'Physical Documents', 'Physical Documents', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 22:48:54', 0),
(497, 66, 'Encoded Data', 'Encoded Data', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 22:48:54', 0),
(498, 66, 'Excel Tables', 'Excel Tables', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 22:48:54', 0),
(499, 66, 'Database', 'Database', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 58.56, '2026-10-14 22:48:54', 0),
(500, 66, 'data encoding', 'data encoding', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 22:48:54', 0),
(501, 66, 'records', 'records', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 68.64, '2026-10-14 22:48:54', 0),
(502, 66, 'hard copy forms', 'hard copy forms', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-14 22:48:54', 0),
(503, 66, 'encoded', 'encoded', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 22:48:54', 0),
(504, 66, 'formatting', 'formatting', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 22:48:54', 0),
(505, 66, 'Excel', 'Excel', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 22:48:54', 0),
(506, 66, 'office', 'office', 'CLERICAL_TERM', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-14 22:48:54', 0),
(507, 65, 'scanned documents', 'scanned documents', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 12.23, '2026-10-14 23:29:27', 0),
(508, 65, 'PDF\nformat', 'PDF\nformat', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 97.83, '2026-10-14 23:29:27', 0),
(509, 65, 'computers', 'computers', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:29:27', 0),
(510, 65, 'office', 'office', 'task', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-14 23:29:27', 0),
(511, 65, 'computer', 'computer', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:29:27', 0),
(512, 65, 'documents', 'documents', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 92.45, '2026-10-14 23:29:27', 0),
(513, 65, 'wire\ntracking', 'wire\ntracking', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:29:27', 0),
(514, 65, 'LAN', 'LAN', 'Networking', 'yes', 'predefined', NULL, NULL, 100.00, '2026-10-14 23:29:27', 0),
(515, 65, 'LAN cables', 'LAN cables', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:29:27', 0),
(516, 65, 'PC', 'PC', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:29:27', 0),
(517, 65, 'printer', 'printer', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:29:27', 0),
(518, 65, 'file management', 'file management', 'CLERICAL_TERM', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-14 23:29:27', 0),
(519, 65, 'Stamping', 'Stamping', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:29:27', 0),
(520, 65, 'authorization', 'authorization', 'IT_TERM', 'yes', 'predefined', NULL, NULL, 100.00, '2026-10-14 23:29:27', 0),
(521, 65, 'computer\nmaintenance', 'computer\nmaintenance', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:29:27', 0),
(522, 65, 'cables', 'cables', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:29:27', 0),
(527, 62, 'System', 'System', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:44:39', 0),
(528, 62, 'Forms', 'Forms', 'CLERICAL_TERM', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-14 23:44:39', 0),
(529, 62, 'Violation', 'Violation', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:44:39', 0),
(530, 62, 'student complaints', 'student complaints', 'task', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-14 23:44:39', 0),
(531, 61, 'System', 'System', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:49:58', 0),
(532, 61, 'Forms', 'Forms', 'CLERICAL_TERM', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-14 23:49:58', 0),
(533, 61, 'Violation', 'Violation', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-14 23:49:58', 0),
(534, 61, 'student complaints', 'student complaints', 'task', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-14 23:49:58', 0);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_number` varchar(100) NOT NULL,
  `program` varchar(50) DEFAULT 'BSIT',
  `section` varchar(20) NOT NULL DEFAULT 'IT44A',
  `company_id` int(11) DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `ojt_start_date` date DEFAULT NULL,
  `completion_requested` tinyint(1) DEFAULT 0,
  `evaluation_triggered` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_number`, `program`, `section`, `company_id`, `supervisor_id`, `office_id`, `ojt_start_date`, `completion_requested`, `evaluation_triggered`, `created_at`) VALUES
(117, 126, '20231053', 'BSIT', 'IT44B', NULL, NULL, NULL, '2026-10-12', 0, 0, '2026-10-09 16:07:30'),
(118, 137, '20231969', 'BSIT', 'IT44B', 14, 20, 28, '2026-10-12', 0, 0, '2026-10-09 10:42:31');

-- --------------------------------------------------------

--
-- Table structure for table `supervisors`
--

CREATE TABLE `supervisors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `job_title` varchar(150) DEFAULT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supervisors`
--

INSERT INTO `supervisors` (`id`, `user_id`, `company_id`, `office_id`, `job_title`, `contact_number`, `created_at`) VALUES
(15, 132, 11, 23, NULL, NULL, '2026-10-09 17:18:01'),
(16, 133, 12, 24, NULL, NULL, '2026-10-09 17:19:50'),
(18, 135, 12, 26, NULL, NULL, '2026-10-09 17:55:51'),
(19, 136, 12, 27, NULL, NULL, '2026-10-09 18:59:30'),
(20, 138, 14, 28, 'Supervisor', '09222111233', '2026-10-09 11:00:45');

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
  `created_at` datetime DEFAULT current_timestamp(),
  `privacy_consent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `status`, `archived_at`, `avatar_url`, `created_at`, `privacy_consent`) VALUES
(3, 'Katelyn Coming', 'comingkatelyn@gmail.com', '$2y$10$77qvQjWUTPLFjy9h0hC9b.0zu1nJvUdpJA950GwQIe4CNaoqGPWgq', 'coordinator', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocIPjVqHwnhTZSamq2pJykgRZBiZfo5Aj_4Gqy0l41oGXJMcYA=s96-c', '2026-08-19 07:00:29', 1),
(126, 'Katelyn Coming', '20231053@nbsc.edu.ph', NULL, 'student', 'active', NULL, NULL, '2026-10-09 16:07:30', 0),
(132, 'Prof. Sander Perejan', 'syder844@gmail.com', '$2y$10$ILOV6OumfFksRaBxGkIiGe5MVkKu.5vsuGsPdYgwRL448h8X4/LlG', 'coordinator', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJ8hHv80_HQczTIj3PWOzVJDwzKYAmELAL6yXXxOcZJNkrmTg=s96-c', '2026-10-09 17:18:01', 1),
(133, 'Pau Coms', 'paucoms@gmail.com', NULL, 'supervisor', 'active', NULL, NULL, '2026-10-09 17:19:50', 0),
(135, 'KKK cm', 'km@gmail.com', NULL, 'supervisor', 'active', NULL, NULL, '2026-10-09 17:55:51', 0),
(136, 'Krs CC', 'krr@gmail.com', NULL, 'supervisor', 'active', NULL, NULL, '2026-10-09 18:59:30', 0),
(137, 'Sander Perejan', '20231969@nbsc.edu.ph', '$2y$10$9oPbBeLYm3kxCPTQ9kBwUuSib0gP1icYa1m.aJF21EdBzQgW8LU0q', 'student', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJZ26hhJE1jpszXnioRlpMOCbGByYSk54nAm7SzZYLz3XV3j0ff=s96-c', '2026-10-09 10:42:31', 1),
(138, 'Sander Perejan', 'sanderperejan@gmail.com', '$2y$10$InJOxWhSVT840wQHk0gm8ODbQpeCxKcdQfwX1Xv48zcYbOuhEqBkG', 'supervisor', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocLo3ANHFlWgegCn6_J_KrGDO8Txnr27MTKMfkNmYSOLRA5xv9fL=s96-c', '2026-10-09 11:00:45', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_user` (`user_id`),
  ADD KEY `idx_audit_created_at` (`created_at`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_company_status` (`status`);

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
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_office_company_name` (`company_id`,`name`);

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
  ADD UNIQUE KEY `uq_predefined_entity` (`entity_name`),
  ADD KEY `idx_entity_archived` (`is_archived`);

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
  ADD KEY `fk_student_supervisor` (`supervisor_id`),
  ADD KEY `idx_student_section` (`section`);

--
-- Indexes for table `supervisors`
--
ALTER TABLE `supervisors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_supervisor_user` (`user_id`),
  ADD UNIQUE KEY `uq_supervisor_office` (`office_id`),
  ADD KEY `fk_supervisor_company` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=426;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `evaluation_otps`
--
ALTER TABLE `evaluation_otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `predefined_entities`
--
ALTER TABLE `predefined_entities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `report_entities`
--
ALTER TABLE `report_entities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=535;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `supervisors`
--
ALTER TABLE `supervisors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

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
-- Constraints for table `offices`
--
ALTER TABLE `offices`
  ADD CONSTRAINT `fk_office_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
