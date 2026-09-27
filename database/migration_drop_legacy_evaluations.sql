-- Removes legacy OJT evaluations that were signed before the 12-competency
-- CHED form was adopted, so the "recorded before the 12-competency OJT form"
-- notice stops appearing in the scorecards.
--
-- A row is treated as legacy when criteria_ratings is NULL/empty.
--
-- WARNING: this DELETES signed records. For each affected student it also:
--   * flips them back to "Awaiting Evaluation" on the student dashboard
--   * UNLOCKS their Weekly Accomplishment Report submission, which
--     submit_report.php locks once an evaluation exists
--   * drops the coordinator's Completed count back to 0
--   * leaves the matching audit_logs row pointing at a missing evaluation
-- Run the backup statement below first if you want a way back.

-- 1. Keep a copy (drop this line if you do not want the safety net)
CREATE TABLE IF NOT EXISTS `evaluations_legacy_backup` AS
    SELECT * FROM `evaluations`
    WHERE `criteria_ratings` IS NULL OR `criteria_ratings` = '';

-- 2. Delete the legacy rows
DELETE FROM `evaluations`
    WHERE `criteria_ratings` IS NULL OR `criteria_ratings` = '';
