START TRANSACTION;

-- Keep only the coordinator accounts for testing.
-- Remove all mock/demo company, student, supervisor, report, evaluation, and user data.

-- 1) clear company references from student records
UPDATE students
SET company_id = NULL
WHERE company_id IS NOT NULL;

-- 2) remove company rows that were only demo/mock data
DELETE FROM companies
WHERE id >= 1;

-- 3) remove all evaluations tied to non-coordinator users
DELETE e
FROM evaluations e
JOIN students s ON s.id = e.student_id
JOIN users u ON u.id = s.user_id
WHERE u.email NOT IN (
  'comingkatelyn@gmail.com',
  'syder844@gmail.com'
);

-- 4) remove all extracted entities tied to non-coordinator users
DELETE re
FROM report_entities re
JOIN reports r ON r.id = re.report_id
JOIN students s ON s.id = r.student_id
JOIN users u ON u.id = s.user_id
WHERE u.email NOT IN (
  'comingkatelyn@gmail.com',
  'syder844@gmail.com'
);

-- 5) remove all report rows tied to non-coordinator users
DELETE r
FROM reports r
JOIN students s ON s.id = r.student_id
JOIN users u ON u.id = s.user_id
WHERE u.email NOT IN (
  'comingkatelyn@gmail.com',
  'syder844@gmail.com'
);

-- 6) remove all student rows for non-coordinator users
DELETE FROM students
WHERE user_id NOT IN (
  SELECT id
  FROM users
  WHERE email IN (
    'comingkatelyn@gmail.com',
    'syder844@gmail.com'
  )
);

-- 7) remove all supervisor rows for non-coordinator users
DELETE FROM supervisors
WHERE user_id NOT IN (
  SELECT id
  FROM users
  WHERE email IN (
    'comingkatelyn@gmail.com',
    'syder844@gmail.com'
  )
);

-- 8) delete all remaining non-coordinator users
DELETE FROM users
WHERE email NOT IN (
  'comingkatelyn@gmail.com',
  'syder844@gmail.com'
);

COMMIT;

-- Result:
-- - keeps only the coordinator accounts for testing
-- - removes mock companies, users, interns, supervisors, reports, entities, and evaluations
-- - keeps predefined_entities lookup data intact