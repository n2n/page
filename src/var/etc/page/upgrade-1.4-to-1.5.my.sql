ALTER TABLE `page`
	ADD COLUMN `indexable` TINYINT UNSIGNED NOT NULL DEFAULT '1' AFTER `last_mod_by`;