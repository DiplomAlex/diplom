ALTER TABLE `site` ADD `site_default_language_id` INT( 11 ) NULL ,
ADD INDEX ( `site_default_language_id` );

ALTER TABLE `site` ADD FOREIGN KEY ( `site_default_language_id` ) REFERENCES `language` (
`language_id`
) ON DELETE SET NULL ;
