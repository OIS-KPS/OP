-- Migration: Add status/archived_at to companies (soft archive support)
-- Mirrors the users table pattern so partner companies can be archived
-- and restored from the Archived tab in User Management.
-- Additive change: no existing rows are modified (all default to 'active').

ALTER TABLE `companies`
  ADD COLUMN `status` enum('active','archived') NOT NULL DEFAULT 'active',
  ADD COLUMN `archived_at` datetime DEFAULT NULL;

-- Index for the "active only" company lookups
ALTER TABLE `companies`
  ADD KEY `idx_company_status` (`status`);