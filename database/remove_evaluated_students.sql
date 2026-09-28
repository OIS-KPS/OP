USE `nbsc_ojt`;

-- Remove students whose final evaluation is already done.
--
-- A student counts as evaluated when a row exists in `evaluations`.
-- The app only ever inserts that row after the OTP is verified
-- (supervisor/api/evaluation_otp.php), and the coordinator list treats
-- `e.id IS NOT NULL AND e.otp_verified = 1` as Completed.
--
-- Deleting from `students` cascades to everything tied to the intern:
--   evaluations      (ON DELETE CASCADE)
--   evaluation_otps  (ON DELETE CASCADE)
--   reports          (ON DELETE CASCADE)
--   report_entities  (ON DELETE CASCADE, through reports)
-- The login is NOT removed by that cascade, so step 3 below handles it.
--
-- The uploaded WAR files in public/files are NOT touched by this script.
-- Step 2 lists them so they can be deleted by hand afterwards.
--
-- Prefer the soft option? `UPDATE users SET status = 'archived',
-- archived_at = NOW() WHERE ...` keeps the account and blocks sign in.

START TRANSACTION;

-- Lock the target set so every step below uses the same students.
DROP TEMPORARY TABLE IF EXISTS `tmp_evaluated_students`;

CREATE TEMPORARY TABLE `tmp_evaluated_students` (
  `student_id` int(11) NOT NULL PRIMARY KEY,
  `user_id`    int(11) NOT NULL
) ENGINE=InnoDB;

-- Add `AND e.otp_verified = 1` if only OTP signed evaluations should go.
INSERT INTO `tmp_evaluated_students` (`student_id`, `user_id`)
SELECT
    s.id,
    s.user_id
FROM students s
JOIN evaluations e
    ON e.student_id = s.id
GROUP BY
    s.id,
    s.user_id;

-- =============================================================
-- DRY RUN: review the two lists, then COMMIT to apply (ROLLBACK aborts)
-- =============================================================

-- 1) who goes
SELECT
    t.student_id,
    s.student_number,
    u.name AS student_name,
    u.email AS student_email,
    e.final_score,
    e.grade_equivalent,
    e.otp_signed_at
FROM `tmp_evaluated_students` t
JOIN students s
    ON s.id = t.student_id
JOIN users u
    ON u.id = t.user_id
LEFT JOIN evaluations e
    ON e.student_id = t.student_id
ORDER BY
    s.student_number;

-- 2) the report files that stay behind in public/files
SELECT
    r.id AS report_id,
    s.student_number,
    r.week_number,
    r.file_path,
    r.previous_file_path
FROM reports r
JOIN `tmp_evaluated_students` t
    ON t.student_id = r.student_id
JOIN students s
    ON s.id = t.student_id
ORDER BY
    s.student_number,
    r.week_number;

-- =============================================================
-- APPLY
-- =============================================================

-- 3) remove the intern. Cascades to evaluations, evaluation_otps,
--    reports and report_entities.
DELETE FROM students
WHERE id IN (
    SELECT student_id
    FROM `tmp_evaluated_students`
);

-- 4) remove the login the intern can no longer use. The NOT EXISTS
--    guards keep a user that is also a supervisor or still linked to
--    another student row.
DELETE FROM users
WHERE id IN (
    SELECT user_id
    FROM `tmp_evaluated_students`
)
  AND role = 'student'
  AND NOT EXISTS (
      SELECT 1
      FROM students s
      WHERE s.user_id = users.id
  )
  AND NOT EXISTS (
      SELECT 1
      FROM supervisors sup
      WHERE sup.user_id = users.id
  );

COMMIT;

DROP TEMPORARY TABLE IF EXISTS `tmp_evaluated_students`;

-- =============================================================
-- Result:
-- - removes every student with a completed final evaluation
-- - keeps supervisors, coordinators, companies and predefined_entities
-- - leaves the uploaded report files on disk, listed in step 2
-- =============================================================


-- =============================================================
-- ONE LINE VERSION, when the cascade is enough and the login may stay:
--
-- DELETE FROM students
-- WHERE EXISTS (
--     SELECT 1
--     FROM evaluations e
--     WHERE e.student_id = students.id
-- );
--
-- ONE STUDENT ONLY:
--
-- DELETE FROM students WHERE id = 111;
-- =============================================================
