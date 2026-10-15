-- Migration: tell a coordinator-added entity apart from a reclassified one
--
-- WHY
-- The entity card in src/pages/shared/report_workspace.php labels every row
-- with source = 'manual_addition' as "Coordinator Added". That is wrong for an
-- entity the extraction model found and a coordinator then reclassified: the
-- coordinator did not add that term, they corrected it.
--
-- confidence_score cannot be used as the signal. Measured on this database:
--
--     source              rows  min_conf  max_conf  exactly 100
--     spacy                 16    99.34     99.99             0
--     predefined             4   100.00    100.00             4
--     manual_addition        6    85.00    100.00             5
--
-- Every 'predefined' row sits at exactly 100.00, so a reclassified predefined
-- term is indistinguishable from a hand-typed one by score alone.
--
-- WHAT THIS ADDS
-- One nullable timestamp. NULL means "typed in by hand"; a timestamp means "the
-- coordinator corrected an existing term". coordinator/view_report.php stamps it
-- on update_entity_type only; add_entity leaves it NULL by default.
--
-- The extraction cleanup in src/services/entity_extraction.php keys off
-- source = 'manual_addition', which is unchanged, so reclassified rows keep
-- surviving the next extraction run exactly as they already did.
--
-- PURELY ADDITIVE: a new nullable column. No rows are rewritten except the
-- backfill below, and no existing column is dropped or altered.
--
-- Verified against the live nbsc_ojt database before writing.

ALTER TABLE `report_entities`
  ADD COLUMN `reclassified_at` DATETIME NULL DEFAULT NULL AFTER `source`;

-- ---------------------------------------------------------------
-- Backfill: one row predates this column.
--
-- id 289 "Office Documents" has confidence_score 85.00 while every other
-- manual_addition row is 100.00, which is the signature of a term that was
-- extracted and then reclassified rather than typed in. Its source was
-- already 'manual_addition' before this column existed, so it has no
-- timestamp and would otherwise be reported as "Coordinator Added".
--
-- The timestamp is a stand-in for the real time of the earlier
-- reclassification, which was not recorded anywhere.
-- ---------------------------------------------------------------

UPDATE `report_entities`
   SET `reclassified_at` = NOW()
 WHERE `source` = 'manual_addition'
   AND `confidence_score` < 100;