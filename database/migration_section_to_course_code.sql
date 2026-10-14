-- migration_section_to_course_code.sql
-- Replaces the legacy "Section A/B/C/D" values in students.section with the
-- course codes IT44A, IT44B, IT44C, IT44D.
--
-- Run AFTER the base schema / previous migrations:
--   mysql -u root nbsc_ojt < database/migration_section_to_course_code.sql
--
-- Idempotent: values already stored as course codes are left untouched, so
-- re-running the script is safe.

-- 1. Preview what will change (run manually, then start the UPDATE below)
-- SELECT id, student_number, section FROM students WHERE section IN ('A','B','C','D');

-- 2. Rewrite the legacy single-letter sections as course codes
UPDATE students
SET section = CONCAT('IT44', section)
WHERE UPPER(section) IN ('A', 'B', 'C', 'D');

-- 3. Normalise any blank/whitespace sections left behind
UPDATE students
SET section = 'IT44A'
WHERE section IS NULL OR TRIM(section) = '';

-- 4. Normalise spacing/casing left by older data (e.g. ' it44b ').
--    BINARY is required: the default collation is case-insensitive, so a plain
--    <> comparison would never see a lowercase value as different.
UPDATE students
SET section = UPPER(TRIM(section))
WHERE BINARY section <> BINARY UPPER(TRIM(section));

-- 5. Move the column default from 'A' to 'IT44A' so rows inserted without an
--    explicit section get a course code. Guarded so re-running is a no-op.
SET @section_default_sql = (
    SELECT IF(
        COLUMN_DEFAULT = 'IT44A',
        'SELECT 1',
        CONCAT('ALTER TABLE students MODIFY `section` VARCHAR(20) NOT NULL DEFAULT ''IT44A''')
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'students'
      AND COLUMN_NAME  = 'section'
);

PREPARE section_default_stmt FROM @section_default_sql;
EXECUTE section_default_stmt;
DEALLOCATE PREPARE section_default_stmt;