-- 1. Create Database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `nbsc_ojt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nbsc_ojt`;

-- 2. Master Users Table (Base entity for authentication & core profile data)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `role` ENUM('student', 'supervisor', 'coordinator', 'admin') NOT NULL DEFAULT 'student',
  `avatar_url` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Partner Companies Table
CREATE TABLE IF NOT EXISTS `companies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `department` VARCHAR(255) DEFAULT 'Main Office',
  `address` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Industry Supervisors Table (Role-specific extension of users)
CREATE TABLE IF NOT EXISTS `supervisors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNIQUE NOT NULL,
  `company_id` INT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_supervisor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_supervisor_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. BSIT Students Table (Role-specific extension of users)
CREATE TABLE IF NOT EXISTS `students` (
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


CREATE TABLE IF NOT EXISTS `reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `week_number` INT NOT NULL,
  `file_path` TEXT NOT NULL,
  `ocr_activities` TEXT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_report_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Insert Sample Partner Company
INSERT INTO `companies` (`id`, `name`, `department`) 
VALUES (1, 'NBSC College of Computer Studies', 'IT Department')
ON DUPLICATE KEY UPDATE `id`=`id`;