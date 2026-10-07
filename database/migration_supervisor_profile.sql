ALTER TABLE `supervisors`
ADD COLUMN IF NOT EXISTS `job_title` VARCHAR(150) NULL AFTER `company_id`,
ADD COLUMN IF NOT EXISTS `contact_number` VARCHAR(30) NULL AFTER `job_title`;
