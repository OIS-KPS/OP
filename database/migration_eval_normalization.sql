-- Migration: normalize evaluation storage
-- Date: 2026-10-30
--
-- Applies the normalized evaluation model:
--
-- 1. `evaluations_legacy_backup` was a one-off backup made during the
--    migration to the 12-competency form. Zero rows, no primary key, and no
--    references anywhere in the application code.
--
-- 2. Every derived scalar on `evaluations` is removed. Nothing computed from
--    the ratings is stored any more:
--       final_score            = AVG(rating) across all rated competencies
--       grade_equivalent       = label of that average (ojtRatingLabel)
--       technical_score        = per-group average (evaluation_questions.group_name)
--       work_ethics_score        "
--       communication_score      "
--       punctuality_score      = leftover of the old 4-category form; always
--                                written as the hardcoded 0.00, never rendered
--    Averages are computed at read time from the rating rows.
--
-- 3. `otp_verified` duplicated state: every write path sets it to 1 together
--    with otp_signed_at = NOW(), so "verified" is exactly
--    "otp_signed_at IS NOT NULL". Consumers now check the timestamp.
--
-- 4. `evaluation_ratings` becomes the 1NF parent/child answer table: one row
--    per (evaluation, criterion). The 12 criterion keys are owned by
--    `evaluation_questions.criterion_key`, group membership by
--    `evaluation_questions.group_name` — no facts are stored twice.
--
-- None of the dropped columns carries an index or FK, so plain DROP COLUMN
-- is enough. evaluation_otps is untouched by this migration.

USE `nbsc_ojt`;

-- ------------------------------------------------------------------
-- 1. Retire the legacy backup table
-- ------------------------------------------------------------------
DROP TABLE IF EXISTS `evaluations_legacy_backup`;

-- ------------------------------------------------------------------
-- 2. New child table for the twelve competency ratings
--    (rating is 1-5; all applicability/total checks stay in PHP)
-- ------------------------------------------------------------------
CREATE TABLE `evaluation_ratings` (
  `id` int(11) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `criterion_key` varchar(100) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `evaluation_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_eval_rating` (`evaluation_id`,`criterion_key`),
  ADD KEY `idx_rating_key` (`criterion_key`),
  ADD KEY `idx_rating_evaluation` (`evaluation_id`);

ALTER TABLE `evaluation_ratings`
  ADD CONSTRAINT `fk_rating_evaluation` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ------------------------------------------------------------------
-- 3. Drop the derived / duplicated columns
-- ------------------------------------------------------------------
ALTER TABLE `evaluations`
  DROP COLUMN `technical_score`,
  DROP COLUMN `work_ethics_score`,
  DROP COLUMN `communication_score`,
  DROP COLUMN `punctuality_score`,
  DROP COLUMN `final_score`,
  DROP COLUMN `grade_equivalent`,
  DROP COLUMN `otp_verified`;
