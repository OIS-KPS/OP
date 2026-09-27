-- OJT Performance Evaluation Form (per public/files/OJT_performance_evaluation_form.docx)
-- Adds storage for the 12 per-competency ratings (1-5 scale) captured by the form.
--
-- Scoring note: `final_score` now holds the RAW 1-5 average of the 12 competencies,
-- not a 0-100 percentage. `technical_score`, `work_ethics_score` and
-- `communication_score` hold the 1-5 average of their competency group.
-- `punctuality_score` is unused by the new form and is written as 0.00.
-- `grade_equivalent` holds the docx legend label (e.g. 'Very Good').

ALTER TABLE `evaluations`
    ADD COLUMN IF NOT EXISTS `criteria_ratings` TEXT DEFAULT NULL AFTER `punctuality_score`;
