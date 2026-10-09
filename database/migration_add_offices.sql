-- One office per existing supervisor (name includes supervisor to stay unique)
INSERT INTO offices (company_id, name, address)
SELECT c.id,
       CONCAT(COALESCE(NULLIF(c.department, ''), c.name), ' (', u.name, ')'),
       c.address
FROM supervisors sup
JOIN companies c ON sup.company_id = c.id
JOIN users u ON sup.user_id = u.id
WHERE NOT EXISTS (
  SELECT 1 FROM offices o
  WHERE o.company_id = c.id
    AND o.name = CONCAT(COALESCE(NULLIF(c.department, ''), c.name), ' (', u.name, ')')
);

-- Assign each supervisor to their own backfilled office
UPDATE supervisors sup
JOIN companies c ON sup.company_id = c.id
JOIN users u ON sup.user_id = u.id
JOIN offices o ON o.company_id = c.id
  AND o.name = CONCAT(COALESCE(NULLIF(c.department, ''), c.name), ' (', u.name, ')')
SET sup.office_id = o.id;

-- Students inherit their supervisor's office
UPDATE students s
JOIN supervisors sup ON s.supervisor_id = sup.id
SET s.office_id = sup.office_id
WHERE sup.office_id IS NOT NULL;