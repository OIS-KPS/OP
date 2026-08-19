-- database/seed_mock_dashboard_data.sql
-- Database-ready Mock Seed Data for CQI Analytics Dashboard

USE `nbsc_ojt`;

-- 1. Seed Partner Companies
INSERT INTO `companies` (`id`, `name`, `department`, `address`) VALUES
(1, 'NBSC IT Dept', 'College of Computer Studies', 'Tankulan, Manolo Fortich, Bukidnon'),
(2, 'NBSC ICTMO', 'Management Information System', 'Tankulan, Manolo Fortich, Bukidnon'),
(3, 'NBSC SASDD', 'Student Affairs & Services', 'Tankulan, Manolo Fortich, Bukidnon'),
(4, 'LGU Manolo Fortich', 'Information Tech Unit', 'Municipal Hall, Manolo Fortich, Bukidnon')
ON DUPLICATE KEY UPDATE 
  `name` = VALUES(`name`), 
  `department` = VALUES(`department`),
  `address` = VALUES(`address`);

-- 2. Seed Mock Student User Accounts (IDs 101 to 108)
INSERT INTO `users` (`id`, `name`, `email`, `role`) VALUES
(101, 'Alex Mercer', 'alex.mercer@nbsc.edu.ph', 'student'),
(102, 'Beatrix Vance', 'beatrix.vance@nbsc.edu.ph', 'student'),
(103, 'Carlo Dizon', 'carlo.dizon@nbsc.edu.ph', 'student'),
(104, 'Diana Prince', 'diana.prince@nbsc.edu.ph', 'student'),
(105, 'Ethan Hunt', 'ethan.hunt@nbsc.edu.ph', 'student'),
(106, 'Fiona Gallagher', 'fiona.gallagher@nbsc.edu.ph', 'student'),
(107, 'Gabriel Reyes', 'gabriel.reyes@nbsc.edu.ph', 'student'),
(108, 'Hannah Abbott', 'hannah.abbott@nbsc.edu.ph', 'student')
ON DUPLICATE KEY UPDATE 
  `name` = VALUES(`name`),
  `role` = VALUES(`role`);

-- 3. Seed Students linked to Companies (IDs 101 to 108)
INSERT INTO `students` (`id`, `user_id`, `student_number`, `program`, `company_id`) VALUES
(101, 101, '2023-IT01', 'BSIT', 1), -- NBSC IT Dept
(102, 102, '2023-IT02', 'BSIT', 1), -- NBSC IT Dept
(103, 103, '2023-IT03', 'BSIT', 2), -- NBSC ICTMO
(104, 104, '2023-IT04', 'BSIT', 2), -- NBSC ICTMO
(105, 105, '2023-IT05', 'BSIT', 3), -- NBSC SASDD
(106, 106, '2023-IT06', 'BSIT', 3), -- NBSC SASDD
(107, 107, '2023-IT07', 'BSIT', 4), -- LGU Manolo Fortich
(108, 108, '2023-IT08', 'BSIT', 4)  -- LGU Manolo Fortich
ON DUPLICATE KEY UPDATE 
  `student_number` = VALUES(`student_number`),
  `company_id` = VALUES(`company_id`);

-- 4. Seed Reports for Students (IDs 101 to 108)
INSERT INTO `reports` (`id`, `student_id`, `week_number`, `file_path`, `ocr_activities`, `status`, `submitted_at`) VALUES
(101, 101, 1, '/uploads/reports/rep_101.pdf', 'Built PHP REST API endpoints for user authentication', 'approved', '2026-07-24 10:30:00'),
(102, 102, 2, '/uploads/reports/rep_102.pdf', 'Optimized MySQL database queries and indexed foreign keys', 'approved', '2026-07-24 14:15:00'),
(103, 103, 1, '/uploads/reports/rep_103.pdf', 'Troubleshot hardware desktop units and reinstalled OS', 'approved', '2026-07-18 09:00:00'),
(104, 104, 2, '/uploads/reports/rep_104.pdf', 'Assisted in document encoding and filing departmental memos', 'approved', '2026-07-15 11:45:00'),
(105, 105, 1, '/uploads/reports/rep_105.pdf', 'Conducted student supply inventory audit and cataloging', 'approved', '2026-07-12 16:20:00'),
(106, 106, 2, '/uploads/reports/rep_106.pdf', 'Generated database backup scripts and validated form inputs', 'approved', '2026-07-21 13:10:00'),
(107, 107, 1, '/uploads/reports/rep_107.pdf', 'Configured VLAN tagging and setup core network routers', 'approved', '2026-07-20 15:30:00'),
(108, 108, 2, '/uploads/reports/rep_108.pdf', 'Performed government document digitization and clerical encoding', 'approved', '2026-07-19 10:00:00')
ON DUPLICATE KEY UPDATE 
  `status` = VALUES(`status`),
  `submitted_at` = VALUES(`submitted_at`);

-- 5. Seed Extracted NLP Report Entities
INSERT INTO `report_entities` (`id`, `report_id`, `entity_name`, `category`, `classification`, `created_at`) VALUES
-- Company 1: NBSC IT Dept (Reports 101, 102)
(101, 101, 'PHP REST API', 'Software Dev', 'Technical', '2026-07-24'),
(102, 101, 'Laravel MVC Framework', 'Software Dev', 'Technical', '2026-07-24'),
(103, 102, 'MySQL Optimization', 'Database', 'Technical', '2026-07-24'),
(104, 102, 'Git Version Control', 'Software Dev', 'Technical', '2026-07-23'),

-- Company 2: NBSC ICTMO (Reports 103, 104)
(105, 103, 'Hardware Support', 'Hardware', 'Technical', '2026-07-18'),
(106, 103, 'LAN Cable Crimping', 'Hardware', 'Technical', '2026-07-18'),
(107, 104, 'Document Encoding', 'Administrative', 'Clerical', '2026-07-15'),
(108, 104, 'Supply Inventory Audit', 'Administrative', 'Clerical', '2026-07-12'),

-- Company 3: NBSC SASDD (Reports 105, 106)
(109, 105, 'Supply Inventory Audit', 'Administrative', 'Clerical', '2026-07-12'),
(110, 106, 'MySQL Optimization', 'Database', 'Technical', '2026-07-21'),
(111, 106, 'Form Validation Script', 'Software Dev', 'Technical', '2026-07-21'),

-- Company 4: LGU Manolo Fortich (Reports 107, 108)
(112, 107, 'VLAN & Router Setup', 'Networking', 'Technical', '2026-07-20'),
(113, 107, 'Firewall Policy Config', 'Networking', 'Technical', '2026-07-20'),
(114, 108, 'Document Encoding', 'Administrative', 'Clerical', '2026-07-19'),
(115, 108, 'Hardware Support', 'Hardware', 'Technical', '2026-07-19')
ON DUPLICATE KEY UPDATE 
  `entity_name` = VALUES(`entity_name`),
  `category` = VALUES(`category`),
  `classification` = VALUES(`classification`);
