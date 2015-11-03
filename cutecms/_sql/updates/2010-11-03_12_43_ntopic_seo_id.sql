ALTER TABLE `news_topic` ADD `ntopic_seo_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `ntopic_id` ,
ADD INDEX ( `ntopic_seo_id` ) 