-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2026 at 08:13 AM
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
(513, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:43:17'),
(514, 3, 'coordinator', 'STUDENT_CREATED', 'Added student Sander Perejan (20231969 - IT44A).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:44:05'),
(515, 3, 'coordinator', 'COMPANY_SUPERVISOR_CREATED', 'Added NBSC (ICTMO) with supervisor Sander Perejan (sanderperejan@gmail.com).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:44:36'),
(516, 3, 'coordinator', 'STUDENT_ASSIGNED', 'Assigned Sander Perejan to office ID 30 (company ID 15, supervisor ID 22).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:44:56'),
(517, 3, 'coordinator', 'USER_LOGOUT', 'User Prof. Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:44:59'),
(518, 9, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:45:06'),
(519, 9, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:45:17'),
(520, 8, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:45:30'),
(521, 8, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 1 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:45:55'),
(522, 8, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:47:55'),
(523, 9, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:48:05'),
(524, 9, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 1 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:49:06'),
(525, 8, 'student', 'USER_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in successfully.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:49:42'),
(526, 8, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 2 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:49:52'),
(527, 9, 'supervisor', 'REPORT_REVISION', 'Supervisor requested changes on Week 2 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:51:04'),
(528, 9, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:51:59'),
(529, 3, 'coordinator', 'GOOGLE_LOGIN', 'User Sander Perejan (syder844@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:52:07'),
(530, 3, 'coordinator', 'EVALUATION_FORM_UPDATED', '2 of 12 competencies changed.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:54:40'),
(531, 3, 'coordinator', 'USER_ARCHIVED', 'Archived user account ID 8.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:56:57'),
(532, 8, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:57:09'),
(533, 8, 'student', 'LOGIN_FAILED', 'Blocked login for account with status \'archived\': 20231969@nbsc.edu.ph.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:57:17'),
(534, 9, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:57:33'),
(535, 3, 'coordinator', 'USER_RESTORED', 'Restored user ID 8.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 01:58:37'),
(536, 9, 'supervisor', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 02:02:06'),
(537, 8, 'student', 'GOOGLE_LOGIN', 'User Sander Perejan (20231969@nbsc.edu.ph) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 02:02:14'),
(538, 8, 'student', 'REPORT_SUBMISSION', 'Student Sander Perejan submitted Week 3 accomplishment report.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 02:02:30'),
(539, 8, 'student', 'USER_LOGOUT', 'User Sander Perejan logged out of the system.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 02:02:40'),
(540, 9, 'supervisor', 'GOOGLE_LOGIN', 'User Sander Perejan (sanderperejan@gmail.com) logged in via Google OAuth.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 02:02:47'),
(541, 9, 'supervisor', 'REPORT_APPROVAL', 'Supervisor approved Week 3 accomplishment report for Sander Perejan.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-20 02:03:23');

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
(15, 'NBSC', 'ICTMO', 'tankulan, manolo fortich, bukidnon', '2026-10-20 01:44:36', 'active', NULL);

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
(30, 15, 'ICTMO', 'tankulan, manolo fortich, bukidnon', '2026-10-20 01:44:36');

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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `entities_extracted_at` datetime DEFAULT NULL,
  `entities_dictionary_version` varchar(64) DEFAULT NULL,
  `entities_extraction_started_at` datetime DEFAULT NULL,
  `entities_extraction_error` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `student_id`, `week_number`, `file_path`, `previous_file_path`, `ocr_activities`, `supervisor_remarks`, `revision_requested_at`, `status`, `is_late`, `deadline_date`, `submitted_at`, `approved_at`, `updated_at`, `entities_extracted_at`, `entities_dictionary_version`, `entities_extraction_started_at`, `entities_extraction_error`) VALUES
(71, 119, 1, 'uploads/reports/WAR_Week_1_Student_119_1792475155.pdf', NULL, NULL, NULL, NULL, 'approved', 0, '2026-10-25', '2026-10-20 01:45:55', '2026-10-20 01:49:06', '2026-10-20 01:49:06', '2026-10-20 01:48:12', '0', NULL, NULL),
(72, 119, 2, 'uploads/reports/WAR_Week_2_Student_119_1792475392.pdf', NULL, NULL, 'revise the day 3 report context', NULL, 'rejected', 0, '2026-11-01', '2026-10-20 01:49:52', NULL, '2026-10-20 01:51:04', '2026-10-20 01:49:58', '0', NULL, NULL),
(73, 119, 3, 'uploads/reports/WAR_Week_3_Student_119_1792476150.pdf', NULL, NULL, NULL, NULL, 'approved', 0, '2026-11-08', '2026-10-20 02:02:30', '2026-10-20 02:03:23', '2026-10-20 02:03:23', '2026-10-20 02:02:53', '0', NULL, NULL);

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
(706, 71, 'Hardcopy Records', 'Hardcopy Records', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:48:12', 0),
(707, 71, 'Excel', 'Excel', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:48:12', 0),
(708, 71, 'Database', 'Database', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:48:12', 0),
(709, 71, 'Encoding Beneficiary Information', 'Encoding Beneficiary Information', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:48:12', 0),
(710, 71, 'Physical Documents', 'Physical Documents', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:48:12', 0),
(711, 71, 'Encoded Data', 'Encoded Data', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:48:12', 0),
(712, 71, 'Incomplete Information', 'Incomplete Information', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:48:12', 0),
(713, 71, 'Encoding', 'Encoding', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:48:12', 0),
(714, 71, 'data encoding', 'data encoding', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:48:12', 0),
(715, 71, 'forms', 'forms', 'CLERICAL_TERM', 'no', 'predefined', NULL, NULL, 100.00, '2026-10-20 01:48:12', 0),
(716, 71, 'formatting', 'formatting', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:48:12', 0),
(717, 72, 'Encoding', 'Encoding', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:49:58', 0),
(718, 72, 'Excel', 'Excel', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:49:58', 0),
(719, 72, 'Numbers', 'Numbers', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:49:58', 0),
(720, 72, 'Block Assignments', 'Block Assignments', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:49:58', 0),
(721, 72, 'Database', 'Database', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:49:58', 0),
(722, 72, 'Hardcopy Records', 'Hardcopy Records', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:49:58', 0),
(723, 72, 'Completing Missing\nInformation', 'Completing Missing\nInformation', 'IT_TERM', 'yes', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:49:58', 0),
(724, 72, 'Excel File', 'Excel File', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:49:58', 0),
(725, 72, 'encoding data', 'encoding data', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:49:58', 0),
(726, 72, 'data organization', 'data organization', 'CLERICAL_TERM', 'no', 'spacy', NULL, NULL, 85.00, '2026-10-20 01:49:58', 0),
(727, 73, 'Section Rating Distribution', 'Section Rating Distribution', 'IT_TERM', 'yes', 'manual_addition', NULL, NULL, 100.00, '2026-10-20 02:04:22', 0);

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
(119, 8, '20231969', 'BSIT', 'IT44A', 15, 22, 30, '2026-10-19', 0, 0, '2026-10-20 01:44:05');

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
(22, 9, 15, 30, 'Office Head', '09922118799', '2026-10-20 01:44:36');

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
(1, 'Katelyn Coming', 'comingkatelyn@gmail.com', '$2y$10$77qvQjWUTPLFjy9h0hC9b.0zu1nJvUdpJA950GwQIe4CNaoqGPWgq', 'coordinator', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocIPjVqHwnhTZSamq2pJykgRZBiZfo5Aj_4Gqy0l41oGXJMcYA=s96-c', '2026-08-19 07:00:29', 1),
(3, 'Prof. Sander Perejan', 'syder844@gmail.com', '$2y$10$ILOV6OumfFksRaBxGkIiGe5MVkKu.5vsuGsPdYgwRL448h8X4/LlG', 'coordinator', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJ8hHv80_HQczTIj3PWOzVJDwzKYAmELAL6yXXxOcZJNkrmTg=s96-c', '2026-10-09 17:18:01', 1),
(8, 'Sander Perejan', '20231969@nbsc.edu.ph', '$2y$10$9oPbBeLYm3kxCPTQ9kBwUuSib0gP1icYa1m.aJF21EdBzQgW8LU0q', 'student', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJZ26hhJE1jpszXnioRlpMOCbGByYSk54nAm7SzZYLz3XV3j0ff=s96-c', '2026-10-09 10:42:31', 1),
(9, 'Sander Perejan', 'sanderperejan@gmail.com', '$2y$10$InJOxWhSVT840wQHk0gm8ODbQpeCxKcdQfwX1Xv48zcYbOuhEqBkG', 'supervisor', 'active', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocLo3ANHFlWgegCn6_J_KrGDO8Txnr27MTKMfkNmYSOLRA5xv9fL=s96-c', '2026-10-09 11:00:45', 1);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=542;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `report_entities`
--
ALTER TABLE `report_entities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=728;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `supervisors`
--
ALTER TABLE `supervisors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

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
