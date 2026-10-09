-- Cleanup: remove empty duplicate offices left by the initial offices backfill.
--
-- The first backfill run created one office per company (named after the company
-- department). The corrected backfill created one office per supervisor named
-- "<department> (<supervisor name>)". Both exist for the same company, so the
-- placement dropdowns listed both "HRMO" and "HRMO (Engr. X)".
--
-- This deletes ONLY offices that are:
--   - empty (no supervisor and no interns linked),
--   - the bare base name (not itself a "<name> (...)" office), and
--   - the base of a "<name> (...)" sibling office in the same company
--
-- The DELETE joins a temporary table instead of self-referencing `offices`,
-- which avoids MySQL error 1093 ("table specified twice as target and source").
--
-- Safe to re-run. Offices with a supervisor or interns are never touched.

DROP TEMPORARY TABLE IF EXISTS tmp_dup_offices;

CREATE TEMPORARY TABLE tmp_dup_offices (
    id INT PRIMARY KEY
);

INSERT INTO tmp_dup_offices (id)
SELECT o.id
FROM offices o
WHERE o.name NOT LIKE '% (%'
  AND NOT EXISTS (SELECT 1 FROM supervisors sup WHERE sup.office_id = o.id)
  AND NOT EXISTS (SELECT 1 FROM students s   WHERE s.office_id  = o.id)
  AND EXISTS (
        SELECT 1
        FROM offices o2
        WHERE o2.company_id = o.company_id
          AND o2.id <> o.id
          AND o2.name LIKE CONCAT(o.name, ' (%')
      );

SELECT COUNT(*) AS duplicates_to_remove FROM tmp_dup_offices;

DELETE o
FROM offices o
JOIN tmp_dup_offices d ON d.id = o.id;

DROP TEMPORARY TABLE IF EXISTS tmp_dup_offices;