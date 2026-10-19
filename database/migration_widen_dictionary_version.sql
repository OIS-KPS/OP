-- Migration: widen entities_dictionary_version to hold the composite watermark
--
-- WHY
-- entityDictionaryVersion() used to return MAX(predefined_entities.updated_at),
-- which fits a datetime column. It now returns COUNT(*)'@'MAX(updated_at), for two
-- reasons:
--
--   1. An empty dictionary is a VALID state, not an unknown one. MAX() over no
--      rows returns NULL, and NULL meant "cannot tell", which
--      reportNeedsEntityExtraction() treats as "extract rather than guess". A
--      fresh install with no dictionary terms therefore re-ran the transformer on
--      every single view, for ever -- the exact problem the extraction cache
--      exists to prevent. An empty table now yields a stable '0'.
--
--   2. COUNT(*) sees deletes. MAX(updated_at) alone cannot notice removal of a
--      row that was not the most recently updated, so permanently deleting an
--      older archived term left the watermark unchanged and the deleted term kept
--      matching reports that had already matched it.
--
-- The composite string ('27@2026-10-11 09:03:21') does not fit a datetime, so
-- the column has to be text.
--
-- WHAT
-- The column is widened to varchar(64) and every stamped report is re-stamped in
-- the new format using the SAME historical reconstruction as
-- migration_backfill_entity_extraction_stamps.sql -- the dictionary watermark as
-- of when that report's entities were written.
--
-- That reconstruction matters: stamping the current watermark would mark every
-- report as fresh against today's dictionary. Reconstructing keeps a genuinely
-- newer dictionary edit detectable.
--
-- No reports are re-extracted by this migration. Reports whose entities predate
-- the latest dictionary change keep the older watermark and will correctly
-- re-extract once on their next view; the rest are already correct.
--
-- Reports with no entities stay NULL and are still queued for their first run.
--
-- PURELY ADDITIVE to the stored data: a column widening (datetime -> varchar is
-- lossless for these values) plus a rewrite of one nullable column. Nothing is
-- deleted, no table is rebuilt, no other column is touched.
--
-- Verified against the live nbsc_ojt database before writing.

ALTER TABLE `reports`
  MODIFY COLUMN `entities_dictionary_version` varchar(64) DEFAULT NULL;

-- Re-stamp in the new composite format. Only rows that have actually been
-- extracted are touched, so reports that have never run stay NULL and keep their
-- place in the first-run queue.
UPDATE `reports` r
JOIN (
    SELECT `report_id`, MAX(`created_at`) AS `last_entity`
      FROM `report_entities`
     GROUP BY `report_id`
) e ON e.`report_id` = r.`id`
SET r.`entities_dictionary_version` = (
    SELECT CONCAT(
        (SELECT COUNT(*) FROM `predefined_entities`),
        '@',
        IFNULL(MAX(p.`updated_at`), '0')
    )
      FROM `predefined_entities` p
     WHERE p.`updated_at` <= e.`last_entity`
)
WHERE r.`entities_extracted_at` IS NOT NULL;

-- ---------------------------------------------------------------
-- Verification:
--
--   -- every stamp must now be the composite text form, never a bare date
--   SELECT `id`, `entities_dictionary_version`
--     FROM `reports`
--    WHERE `entities_extracted_at` IS NOT NULL;
--     -> '27@2026-10-11 09:03:21' style, and no row left as a raw datetime
--
--   -- the column must be text now
--   SHOW COLUMNS FROM `reports` LIKE 'entities_dictionary_version';
--     -> Type: varchar(64)
--
--   -- never-extracted reports must remain NULL
--   SELECT COUNT(*) AS `still_queued`
--     FROM `reports`
--    WHERE `entities_extracted_at` IS NULL;
--
-- SEE ALSO
--   database/migration_add_entities_extracted_at.sql
--   database/migration_add_entity_extraction_progress.sql
--   database/migration_backfill_entity_extraction_stamps.sql
-- ---------------------------------------------------------------
