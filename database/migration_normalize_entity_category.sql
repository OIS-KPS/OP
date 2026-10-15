-- Migration: reduce report_entities.category to the two real classifications
--
-- WHY
-- report_entities carries two classification columns:
--
--     it_related  enum('yes','no','unknown')   the source of truth
--     category    varchar(100)                used for grouping and filtering
--
-- They were allowed to drift apart. coordinator/view_report.php reclassified a
-- term by writing it_related only, so the row ended up like this:
--
--     id 289  Office Documents   category = IT_TERM   it_related = no
--
-- A term the coordinator had explicitly moved to Clerical still carried an
-- IT_TERM category, and the CQI entity-frequency chart -- which grouped its
-- band labels off category -- printed that clerical term inside a band headed
-- "IT_TERM". The classification was right in the database and wrong on screen.
--
-- Nothing here is a new concept. category is now a mirror of it_related:
--
--     it_related = 'no'    ->  category = 'CLERICAL_TERM'
--     anything else        ->  category = 'IT_TERM'
--
-- 'Other' disappears as a category value; a term is either an IT term or a
-- clerical one. coordinator/view_report.php now writes both columns together on
-- add_entity and on update_entity_type, so they cannot diverge going forward.
--
-- The chart was also changed to group its bands off classification rather than
-- category, so the display no longer depends on this column staying in sync.
--
-- SAFE TO RUN TWICE: the statement is idempotent -- it recomputes the same value
-- from it_related every time.
--
-- Verified against the live nbsc_ojt database before writing.

UPDATE `report_entities`
   SET `category` = CASE
                       WHEN `it_related` = 'no' THEN 'CLERICAL_TERM'
                       ELSE 'IT_TERM'
                   END;

-- ---------------------------------------------------------------
-- Verification (run after applying):
--
--   SELECT `it_related`, `category`, COUNT(*) AS n
--     FROM `report_entities`
--    GROUP BY `it_related`, `category`;
--
-- Expect exactly two pairs: ('no','CLERICAL_TERM') and
-- ('yes'/'unknown','IT_TERM'). No 'Other', no mismatches.
-- ---------------------------------------------------------------