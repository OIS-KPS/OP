ALTER TABLE `students`
ADD COLUMN `evaluation_auto_disabled` tinyint(1) NOT NULL DEFAULT 0
AFTER `evaluation_triggered`;