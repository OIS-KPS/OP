-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2026 at 03:03 PM
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
(448, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 03:12:53'),
(449, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 03:13:01'),
(453, 132, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 03:28:18'),
(454, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 03:28:26'),
(455, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 03:30:06'),
(456, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 03:30:16'),
(458, 132, 'coordinator', 'EVALUATION_FORM_UPDATED', '2 of 12 competencies changed.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 03:32:20'),
(459, 132, 'coordinator', 'EVALUATION_FORM_UPDATED', '2 of 12 competencies changed.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 03:32:33'),
(460, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 04:29:11'),
(461, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 04:30:22'),
(462, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 04:31:04'),
(463, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 06:50:17'),
(464, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 11:13:18'),
(465, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 11:14:31'),
(466, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 11:14:39'),
(467, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 19:47:41'),
(468, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 19:51:42'),
(469, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 19:51:49'),
(470, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 19:52:38'),
(471, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 19:53:14'),
(472, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:04:13'),
(473, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:04:20'),
(474, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:48:56'),
(475, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:49:04'),
(476, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:51:20'),
(477, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:51:27'),
(478, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:52:25'),
(479, 137, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:52:36'),
(480, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:55:32'),
(481, 138, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:55:39'),
(482, 138, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:58:53'),
(483, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:59:01'),
(484, 137, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-15 20:59:34'),
(485, 137, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 08:40:54'),
(486, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 08:41:38'),
(487, 137, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 08:41:46'),
(488, 137, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 08:49:31'),
(489, 132, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 08:50:35'),
(490, 137, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 09:02:31');

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
  `criteria_snapshot` text DEFAULT NULL,
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
-- Table structure for table `evaluation_questions`
--

CREATE TABLE `evaluation_questions` (
  `id` int(11) NOT NULL,
  `criterion_key` varchar(100) NOT NULL,
  `question_text` text NOT NULL,
  `group_name` enum('technical','ethics','communication') NOT NULL DEFAULT 'technical',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evaluation_questions`
--

INSERT INTO `evaluation_questions` (`id`, `criterion_key`, `question_text`, `group_name`, `sort_order`, `updated_at`, `updated_by`) VALUES
(1, 'apply_knowledge', 'Ability to apply knowledge of information technology to solve IT problems', 'technical', 1, '2026-10-15 03:32:33', 132),
(2, 'design_experiments', 'Ability to design and conduct experiments, as well as to analyze and interpret data', 'technical', 2, '2026-10-15 03:32:33', 132),
(3, 'design_systems', 'Ability to design a system, component, or process to meet desired needs within realistic constraints such as economic, environmental, social, political, ethical, health and safety, manufacturability, and sustainability, in accordance with standards', 'technical', 3, '2026-10-15 03:32:20', 132),
(4, 'multidisciplinary_teams', 'Ability to function on multidisciplinary teams', 'communication', 4, '2026-10-15 03:32:20', 132),
(5, 'identify_solve_problems', 'Ability to identify, formulate, and solve information technology problems', 'technical', 5, '2026-10-15 03:32:20', 132),
(6, 'professional_ethics', 'Understanding of professional and ethical responsibility', 'ethics', 6, '2026-10-15 03:32:20', 132),
(7, 'communicate_effectively', 'Ability to communicate effectively', 'communication', 7, '2026-10-15 03:32:20', 132),
(8, 'global_impact', 'Broad education necessary to understand the impact of IT solutions in a global, economic, environmental, and societal context', 'communication', 8, '2026-10-15 03:32:20', 132),
(9, 'lifelong_learning', 'Recognition of the need for, and an ability to engage in life-long learning', 'ethics', 9, '2026-10-15 03:32:20', 132),
(10, 'contemporary_issues', 'Knowledge of contemporary issues', 'ethics', 10, '2026-10-15 03:32:20', 132),
(11, 'modern_it_tools', 'Ability to use techniques, skills, and modern IT tools necessary for information technology practice', 'technical', 11, '2026-10-15 03:32:20', 132),
(12, 'it_management_principles', 'Knowledge and understanding of information technology and management principles as a member and leader in a team, to manage projects and in multidisciplinary environments', 'communication', 12, '2026-10-15 03:32:20', 132);

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
(547, 61, 'System', 'System', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-15 00:18:18', 0),
(548, 61, 'Forms', 'Forms', 'CLERICAL_TERM', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-15 00:18:18', 0),
(549, 61, 'Violation', 'Violation', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-15 00:18:18', 0),
(550, 61, 'student complaints', 'student complaints', 'task', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-15 00:18:18', 0),
(551, 62, 'System', 'System', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 99.99, '2026-10-15 00:18:47', 0),
(552, 62, 'Forms', 'Forms', 'CLERICAL_TERM', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-15 00:18:47', 0),
(553, 62, 'Violation', 'Violation', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 99.99, '2026-10-15 00:18:47', 0),
(554, 62, 'student complaints', 'student complaints', 'task', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-15 00:18:47', 0);

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
-- Indexes for table `evaluation_questions`
--
ALTER TABLE `evaluation_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_evaluation_question_key` (`criterion_key`),
  ADD KEY `idx_evaluation_question_sort` (`sort_order`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=491;

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
-- AUTO_INCREMENT for table `evaluation_questions`
--
ALTER TABLE `evaluation_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=555;

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
