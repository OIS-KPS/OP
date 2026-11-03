-- Migration: deadline-aware weekly accomplishment report submissions
-- Target: updated_nbsc_ojt v1 schema (nbsc_ojt database)
-- Run after the base schema and existing migration files.

USE `nbsc_ojt`;

-- 1. STUDENTS SIDE FIRST: anchor each student's OJT week timeline.
ALTER TABLE `students`
    ADD COLUMN `ojt_start_date` DATE NULL AFTER `office_id`;

-- 2. REPORTS SIDE: track submission timeliness and expected Sunday deadline.
ALTER TABLE `reports`
    ADD COLUMN `is_late` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
    ADD COLUMN `deadline_date` DATE NULL AFTER `is_late`;

-- Backfill existing report deadlines from their submitted date when possible.
-- (Expected deadline becomes the Sunday of the same week as submission.)
UPDATE `reports`
SET `deadline_date` = DATE_SUB(DATE(`submitted_at`), INTERVAL (DAYOFWEEK(DATE(`submitted_at`)) - 1) DAY) + INTERVAL 6 DAY
WHERE `deadline_date` IS NULL;