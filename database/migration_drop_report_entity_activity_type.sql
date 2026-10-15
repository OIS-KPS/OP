-- Migration: Drop activity_type from report_entities
--
-- WHY
-- report_entities.activity_type (Software/Hardware/Clerical/Other) was a duplicate
-- of the IT/Clerical flag already stored in it_related (yes/no/unknown).
-- Every reporting query and UI badge uses it_related. The column added no
-- information and created a transitive dependency:
--
--     category/activity_type  →  it_related
--
-- which violates 3NF. predefined_entities.activity_type (the term-level
-- classification) is KEEPING — that is where Software/Hardware belongs.
--
-- PURELY SUBTRACTIVE: drops one column from report_entities. All readers
-- have been switched to it_related. Behaviour is identical because:
--     it_related <> 'no'  ≡  activity_type <> 'Clerical'
-- on every row in the current data (0 disagreements).
--
-- Verified against the live nbsc_ojt database before writing.

ALTER TABLE `report_entities`
  DROP COLUMN `activity_type`;