ALTER TABLE `page_content_t`
	CHANGE COLUMN `se_description` `se_description` VARCHAR(500) NULL DEFAULT NULL AFTER `se_title`;