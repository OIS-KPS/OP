ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `status` ENUM('active', 'archived') DEFAULT 'active' AFTER `role`,
ADD COLUMN IF NOT EXISTS `archived_at` DATETIME NULL AFTER `status`;