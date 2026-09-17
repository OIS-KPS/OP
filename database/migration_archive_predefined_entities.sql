-- database/migration_archive_predefined_entities.sql
-- Adds soft-delete (archive) support to the coordinator entity dictionary.
-- Run once on existing databases after schema_v2.sql.

ALTER TABLE `predefined_entities`
  ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `description`;
