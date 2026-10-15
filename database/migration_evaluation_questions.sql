-- Migration: coordinator-editable evaluation form
--
-- The 12 competency questions on the OJT Performance Evaluation form were
-- hardcoded in config/evaluation_criteria.php. This migration gives the
-- coordinator a place to edit their wording without a code deploy.
--
-- Scope is deliberately narrow: only `question_text` is editable. The 12
-- `criterion_key` values, their `group_name` and the 1-5 rating scale stay
-- fixed, because evaluations.technical_score / work_ethics_score /
-- communication_score and every stored criteria_ratings JSON blob are keyed
-- on those keys and on those groups. Editing a key or a group would silently
-- invalidate historical scores.
--
-- Two tables/columns are involved:
--
--   1. evaluation_questions      the live wording used for evaluations
--                                 submitted from now on.
--
--   2. evaluations.criteria_snapshot
--                                 the wording as it read at submission time,
--                                 frozen per evaluation. This is what keeps
--                                 an already-signed record displaying the 12
--                                 competencies the supervisor actually
--                                 answered, even after the coordinator
--                                 rewrites the form.
--
-- Run AFTER the base schema. Safe to re-run: every statement is guarded.

-- ------------------------------------------------------------
-- 1. Live evaluation questions
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `evaluation_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `criterion_key` varchar(100) NOT NULL,
  `question_text` text NOT NULL,
  `group_name` enum('technical','ethics','communication') NOT NULL DEFAULT 'technical',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_evaluation_question_key` (`criterion_key`),
  KEY `idx_evaluation_question_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed with the wording currently hardcoded in config/evaluation_criteria.php,
-- so behaviour is unchanged until a coordinator makes their first edit.
-- group_name and sort_order mirror that file exactly.
INSERT INTO `evaluation_questions` (`criterion_key`, `question_text`, `group_name`, `sort_order`) VALUES
  ('apply_knowledge', 'Ability to apply knowledge of information technology to solve IT problems', 'technical', 1),
  ('design_experiments', 'Ability to design and conduct experiments, as well as to analyze and interpret data', 'technical', 2),
  ('design_systems', 'Ability to design a system, component, or process to meet desired needs within realistic constraints such as economic, environmental, social, political, ethical, health and safety, manufacturability, and sustainability, in accordance with standards', 'technical', 3),
  ('multidisciplinary_teams', 'Ability to function on multidisciplinary teams', 'communication', 4),
  ('identify_solve_problems', 'Ability to identify, formulate, and solve information technology problems', 'technical', 5),
  ('professional_ethics', 'Understanding of professional and ethical responsibility', 'ethics', 6),
  ('communicate_effectively', 'Ability to communicate effectively', 'communication', 7),
  ('global_impact', 'Broad education necessary to understand the impact of IT solutions in a global, economic, environmental, and societal context', 'communication', 8),
  ('lifelong_learning', 'Recognition of the need for, and an ability to engage in life-long learning', 'ethics', 9),
  ('contemporary_issues', 'Knowledge of contemporary issues', 'ethics', 10),
  ('modern_it_tools', 'Ability to use techniques, skills, and modern IT tools necessary for information technology practice', 'technical', 11),
  ('it_management_principles', 'Knowledge and understanding of information technology and management principles as a member and leader in a team, to manage projects and in multidisciplinary environments', 'communication', 12)
ON DUPLICATE KEY UPDATE
  `question_text` = VALUES(`question_text`),
  `group_name`   = VALUES(`group_name`),
  `sort_order`   = VALUES(`sort_order`);

-- ------------------------------------------------------------
-- 2. Per-evaluation snapshot of the wording
-- ------------------------------------------------------------
--
-- NULL on every existing row, which is the signal to fall back to the live
-- wording: those evaluations predate the editable form, so the hardcoded
-- default text is the correct historical text for them.
SET @has_snapshot := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'evaluations'
    AND COLUMN_NAME = 'criteria_snapshot'
);

SET @snapshot_sql := IF(
  @has_snapshot = 0,
  'ALTER TABLE `evaluations` ADD COLUMN `criteria_snapshot` text DEFAULT NULL AFTER `criteria_ratings`',
  'DO 0'
);

PREPARE stmt_snapshot FROM @snapshot_sql;
EXECUTE stmt_snapshot;
DEALLOCATE PREPARE stmt_snapshot;