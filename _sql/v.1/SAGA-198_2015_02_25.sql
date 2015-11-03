ALTER TABLE  `order` ADD  `order_export` TINYINT( 1 ) DEFAULT  '1' COMMENT  'Произведен экспорт';

ALTER TABLE  `order` ADD  `order_guid` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT  'Уникальный идентификатор' AFTER  `order_client_comment`;