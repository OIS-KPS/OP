-- Migration: Drop unused student placement-request columns
-- The student-side "Request Initial Placement / Request Placement Transfer"
-- flow was removed. Placements are assigned directly by the OJT Coordinator
-- (coordinator/assignments.php via company_id / supervisor_id).
-- Run this on existing databases after the base schema (nbsc_ojt v3.sql).

ALTER TABLE `students`
    DROP COLUMN `requested_company_name`,
    DROP COLUMN `requested_supervisor_name`,
    DROP COLUMN `requested_supervisor_email`,
    DROP COLUMN `placement_request_status`,
    DROP COLUMN `placement_rejection_reason`;
