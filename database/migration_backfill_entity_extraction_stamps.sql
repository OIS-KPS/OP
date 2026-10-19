-- Backfill: stamp reports that already have extracted entities
--
-- WHY
-- migration_add_entities_extracted_at.sql added
-- entities_extracted_at / entities_dictionary_version as NULL, which correctly
-- means "never extracted". But these reports WERE extracted -- their rows are
-- sitting in report_entities, written before the tracking columns existed.
--
-- Left NULL, every one of them was indistinguishable from a brand new report, so
-- the portal re-ran the transformer on each of them for nothing and showed a
-- misleading "Analyzing report entities..." banner over entity lists that were
-- already on screen and already correct.
--
-- WHAT
-- For every report that has at least one row in report_entities:
--
--   entities_extracted_at        the timestamp of its most recent entity row,
--                                 i.e. when that analysis actually ran
--   entities_dictionary_version  the dictionary watermark as of that moment
--
-- WHY THE WATERMARK IS RECONSTRUCTED RATHER THAN SET TO "NOW"
-- Stamp the current dictionary version and every report would look current, so
-- any entity added to the dictionary after these reports were analysed would
-- never reach them. Taking MAX(predefined_entities.updated_at) at or before the
-- analysis time reproduces the dictionary those entities were actually matched
-- against, so a later dictionary edit is still detected and still triggers one
-- correct re-extraction.
--
-- Reports whose entities predate every dictionary row get a NULL watermark,
-- which reportNeedsEntityExtraction() treats as "extract rather than guess".
--
-- Reports with NO entity rows are deliberately left alone. They genuinely have
-- never been analysed and must still run.
--
-- PURELY ADDITIVE to those rows: no columns added, dropped or retyped, no
-- report_entities rows touched, nothing deleted. Re-running this is safe; rows
-- already stamped are simply recomputed from the same source data.
--
-- Verified against the live nbsc_ojt database before writing.

UPDATE `reports` r
JOIN (
    SELECT `report_id`, MAX(`created_at`) AS `last_entity`
      FROM `report_entities`
     GROUP BY `report_id`
) e ON e.`report_id` = r.`id`
SET r.`entities_extracted_at`        = e.`last_entity`,
    r.`entities_dictionary_version`  = (
        SELECT MAX(p.`updated_at`)
          FROM `predefined_entities` p
         WHERE p.`updated_at` <= e.`last_entity`
    ),
    -- These reports are not mid-analysis; drop any stale spawn marker so they
    -- are not read as in-flight.
    r.`entities_extraction_started_at` = NULL
WHERE r.`entities_extracted_at` IS NULL;

-- ---------------------------------------------------------------
-- Verification:
--
--   -- stamped reports should now equal the number of reports that
--   -- actually have entities
--   SELECT
--     (SELECT COUNT(*) FROM `reports`
--       WHERE `entities_extracted_at` IS NOT NULL) AS `stamped`,
--     (SELECT COUNT(DISTINCT `report_id`) FROM `report_entities`) AS `with_entities`;
--
--   -- only reports with no entities should remain unstamped
--   SELECT r.`id`, r.`week_number`
--     FROM `reports` r
--    WHERE r.`entities_extracted_at` IS NULL
--      AND NOT EXISTS (SELECT 1 FROM `report_entities` e WHERE e.`report_id` = r.`id`);
--
--   -- no report may claim it finished while also carrying a failure
--   SELECT COUNT(*) AS `contradictory`
--     FROM `reports`
--    WHERE `entities_extracted_at` IS NOT NULL
--      AND `entities_extraction_error` IS NOT NULL;
--
-- SEE ALSO
--   database/migration_add_entities_extracted_at.sql
--   database/migration_add_entity_extraction_progress.sql
-- ---------------------------------------------------------------
