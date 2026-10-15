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

INSERT INTO `predefined_entities` (`id`, `entity_name`, `aliases`, `category`, `activity_type`, `it_related`, `description`, `is_archived`, `created_at`, `updated_at`) VALUES
(1, 'PHP', 'php programming|php development|php system', 'Programming', 'Software', 'yes', 'PHP programming and development', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(2, 'MySQL', 'mysql database|mysql db|mysql database management', 'Database', 'Software', 'yes', 'MySQL database activities', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(3, 'Java', 'java programming|java development', 'Programming', 'Software', 'yes', 'Java programming', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(4, 'Python', 'python programming|python development', 'Programming', 'Software', 'yes', 'Python programming', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(5, 'HTML', 'html coding|html development', 'Programming', 'Software', 'yes', 'HTML development', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(6, 'CSS', 'css styling|css development', 'Programming', 'Software', 'yes', 'CSS development', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(7, 'JavaScript', 'javascript|js programming|js development', 'Programming', 'Software', 'yes', 'JavaScript programming', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(8, 'Git', 'git version control', 'Development Tool', 'Software', 'yes', 'Version control', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(9, 'GitHub', 'github repository|github repositories', 'Development Tool', 'Software', 'yes', 'GitHub repository and version control', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(10, 'Microsoft Word', 'ms word|word processing|microsoft word', 'Office Software', 'Software', 'yes', 'Word processing software', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(11, 'Microsoft Excel', 'ms excel|excel spreadsheet|spreadsheet', 'Office Software', 'Software', 'yes', 'Spreadsheet software', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(12, 'Microsoft PowerPoint', 'ms powerpoint|powerpoint|presentation', 'Office Software', 'Software', 'yes', 'Presentation software', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(13, 'Canva', 'canva design|canva editing|canva graphics', 'Design Software', 'Software', 'yes', 'Graphic design software', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(14, 'Windows', 'windows operating system|windows os', 'Operating System', 'Software', 'yes', 'Windows operating system', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(15, 'Windows 10', 'windows ten', 'Operating System', 'Software', 'yes', 'Windows 10 operating system', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(16, 'Windows 11', 'windows eleven', 'Operating System', 'Software', 'yes', 'Windows 11 operating system', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(17, 'Computer Installation', 'pc installation|computer setup|computer configuration', 'Hardware Task', 'Hardware', 'yes', 'Computer installation and setup', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(18, 'Computer Troubleshooting', 'pc troubleshooting|computer repair|pc repair', 'Hardware Task', 'Hardware', 'yes', 'Computer troubleshooting', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(19, 'Network Configuration', 'network setup|network configuration', 'Networking', 'Hardware', 'yes', 'Network configuration', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(20, 'LAN', 'local area network|lan configuration', 'Networking', 'Hardware', 'yes', 'Local area networking', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(21, 'Data Entry', 'data encoding|encoding|encode data', 'Administrative Task', 'Clerical', 'no', 'Data encoding and entry', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(22, 'Document Filing', 'file filing|filing documents|document organization', 'Administrative Task', 'Clerical', 'no', 'Document filing', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(23, 'Printing', 'print documents|document printing', 'Administrative Task', 'Clerical', 'no', 'Printing documents', 0, '2026-08-22 08:01:41', '2026-08-22 08:01:41'),
(24, 'homework', NULL, 'tasl', 'Clerical', 'no', NULL, 0, '2026-10-10 07:21:23', '2026-10-10 07:21:23'),
(25, 'Rating Distributiom', NULL, 'Software', 'Software', 'yes', NULL, 0, '2026-10-10 09:40:44', '2026-10-10 09:40:44'),
(26, 'office', 'office work', 'task', 'Clerical', 'no', NULL, 0, '2026-10-10 10:06:41', '2026-10-10 10:06:41'),
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
