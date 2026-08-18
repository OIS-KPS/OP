CREATE DATABASE `nbsc_ojt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nbsc_ojt`;

-- =============================================================
-- 1. Master Users Table (Base entity for authentication & core profile)
-- =============================================================
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `role` ENUM('student', 'supervisor', 'coordinator', 'admin') NOT NULL DEFAULT 'student',
  `avatar_url` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================
-- 2. Partner Companies Table
-- =============================================================
CREATE TABLE `companies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `department` VARCHAR(255) DEFAULT 'Main Office',
  `address` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================
-- 3. Industry Supervisors Table (Role-specific extension of users)
-- =============================================================
CREATE TABLE `supervisors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNIQUE NOT NULL,
  `company_id` INT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_supervisor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_supervisor_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================
-- 4. BSIT Students Table (Role-specific extension of users)
-- =============================================================
CREATE TABLE `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNIQUE NOT NULL,
  `student_number` VARCHAR(100) UNIQUE NOT NULL,
  `program` VARCHAR(50) DEFAULT 'BSIT',
  `company_id` INT NULL,
  `supervisor_id` INT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_student_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_student_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================
-- 5. Student Weekly Accomplishment Reports Table
-- =============================================================
CREATE TABLE `reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `week_number` INT NOT NULL,
  `file_path` TEXT NOT NULL,
  `ocr_activities` TEXT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_report_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `evaluations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT UNIQUE NOT NULL,
  `supervisor_id` INT NOT NULL,
  `technical_score` DECIMAL(5,2) DEFAULT 0.00,
  `work_ethics_score` DECIMAL(5,2) DEFAULT 0.00,
  `communication_score` DECIMAL(5,2) DEFAULT 0.00,
  `punctuality_score` DECIMAL(5,2) DEFAULT 0.00,
  `final_score` DECIMAL(5,2) DEFAULT 0.00,
  `grade_equivalent` VARCHAR(50) DEFAULT NULL,
  `feedback` TEXT NULL,
  `otp_verified` TINYINT(1) DEFAULT 0,
  `otp_signed_at` DATETIME NULL,
  `otp_ip_address` VARCHAR(45) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_eval_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_eval_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================
-- 6. SEED DATA (Initial Default Records & Test Accounts)
-- =============================================================

-- Seed 1: Sample Partner Company
INSERT INTO `companies` (`id`, `name`, `department`) 
VALUES (1, 'NBSC College of Computer Studies', 'IT Department')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `department` = VALUES(`department`);

-- Seed 2: Test Student User Account
INSERT INTO `users` (`name`, `email`, `role`) 
VALUES ('Katelyn L. Coming', '20231053@nbsc.edu.ph', 'student')
ON DUPLICATE KEY UPDATE `role` = 'student';

-- Seed 3: Test Supervisor User Account
INSERT INTO `users` (`name`, `email`, `role`) 
VALUES ('Engr. keyt', 'coming.katelyn08@gmail.com', 'supervisor')
ON DUPLICATE KEY UPDATE `role` = 'supervisor';

-- Seed 4: Test Coordinator User Account
INSERT INTO `users` (`name`, `email`, `role`) 
VALUES ('Prof. Coordinator', 'comingkatelyn@gmail.com', 'coordinator')
ON DUPLICATE KEY UPDATE `role` = 'coordinator';

-- Seed 5: Link Test Supervisor to Company #1 via Email Lookup
INSERT INTO `supervisors` (`user_id`, `company_id`) 
SELECT `id`, 1 FROM `users` WHERE `email` = 'coming.katelyn08@gmail.com'
ON DUPLICATE KEY UPDATE `company_id` = 1;

-- Seed 6: Link Test Student to User, Company #1, and Supervisor via Subqueries
INSERT INTO `students` (`user_id`, `student_number`, `program`, `company_id`, `supervisor_id`)
SELECT 
  u.`id` AS user_id, 
  '20231053' AS student_number, 
  'BSIT' AS program, 
  1 AS company_id, 
  s.`id` AS supervisor_id
FROM `users` u
LEFT JOIN `supervisors` s ON s.`user_id` = (SELECT `id` FROM `users` WHERE `email` = 'coming.katelyn08@gmail.com' LIMIT 1)
WHERE u.`email` = '20231053@nbsc.edu.ph'
ON DUPLICATE KEY UPDATE 
  `student_number` = VALUES(`student_number`), 
  `company_id` = VALUES(`company_id`), 
  `supervisor_id` = VALUES(`supervisor_id`);