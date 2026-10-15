-- Migration: record coordinator-added entities distinctly
--
-- WHY
-- report_entities.source is:
--     enum('spacy','predefined','spacy_predefined') NOT NULL DEFAULT 'spacy_predefined'
--
-- coordinator/view_report.php has always inserted 'manual_addition' for entities
-- a coordinator types into the Add Entity modal. That value is not in the enum,
-- so MySQL silently coerced every one of them to '' (the enum's zero value).
-- The UI therefore could never tell a coordinator's manual term apart from an
-- extracted one.
--
-- src/services/entity_extraction.php now skips manual rows when clearing the
-- previous extraction result, and skips extracted terms the coordinator already
-- added. Both depend on 'manual_addition' actually being stored.
--
-- PURELY ADDITIVE: one more label on an existing enum. No rows are rewritten,
-- no column is dropped, and the DEFAULT is left exactly as it was. Existing
-- values ('spacy', 'predefined', 'spacy_predefined', '') all remain valid.
--
-- Verified against the live nbsc_ojt database before writing.

ALTER TABLE `report_entities`
  MODIFY COLUMN `source` enum('spacy','predefined','spacy_predefined','manual_addition')
  NOT NULL DEFAULT 'spacy_predefined';

-- ---------------------------------------------------------------
-- Follow-up: the one row already stored as '' cannot be attributed
-- with certainty, so it is left alone. It is an extracted term from
-- before this migration and remains visible exactly as it was.
-- ---------------------------------------------------------------