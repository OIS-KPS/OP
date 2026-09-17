-- Coordinator triggers final evaluation for the assigned supervisor
ALTER TABLE `students`
ADD COLUMN IF NOT EXISTS `evaluation_triggered` TINYINT(1) DEFAULT 0 AFTER `completion_requested`;