-- Migration: Scale indexes for 100+ user rollout
-- Verified against the live nbsc_ojt database with EXPLAIN before writing.
-- Purely additive: indexes only, no columns or rows are modified.

-- audit_logs is the fastest-growing table in the portal (logActivity() writes a
-- row on every login, logout and action). coordinator/audit_logs.php runs:
--     ORDER BY a.created_at DESC LIMIT <n>
-- EXPLAIN returned type=ALL / "Using filesort" without this index.
ALTER TABLE `audit_logs`
  ADD KEY `idx_audit_created_at` (`created_at`);

-- Every authenticated coordinator page filters WHERE u.status = 'active'
-- (students, supervisors, companies, offices listings). companies.status already
-- has idx_company_status from migration_add_company_status.sql; users.status
-- never received the equivalent.
ALTER TABLE `users`
  ADD KEY `idx_users_status` (`status`);

-- coordinator/assignments.php both filters and sorts on section:
--     WHERE s.section = :sec ... ORDER BY s.section ASC, u.name ASC
ALTER TABLE `students`
  ADD KEY `idx_student_section` (`section`);

-- coordinator/entities.php filters every listing query on is_archived.
ALTER TABLE `predefined_entities`
  ADD KEY `idx_entity_archived` (`is_archived`);