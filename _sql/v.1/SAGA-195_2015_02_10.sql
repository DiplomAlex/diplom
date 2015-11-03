--
-- Структура таблицы `remain`
--
DROP TABLE remain;
CREATE TABLE IF NOT EXISTS `remain` (
  `remain_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id записи',
  `remain_sku` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Артикул',
  `remain_code` int(6) NOT NULL COMMENT 'Штрих-код',
  `remain_material` varchar(255) DEFAULT NULL COMMENT 'Материал',
  `remain_probe` int(11) DEFAULT NULL COMMENT 'Проба',
  `remain_size` double DEFAULT NULL COMMENT 'Размер',
  `remain_characteristics` varchar(1000) DEFAULT NULL COMMENT 'Характеристики',
  `remain_weight` double DEFAULT NULL COMMENT 'Вес',
  `remain_price` double(12,2) NOT NULL COMMENT 'Цена',
  `remain_in_stock` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Наличие',
  PRIMARY KEY (`remain_id`),
  KEY `remain_sku` (`remain_sku`),
  KEY `remain_code` (`remain_code`),
  KEY `remain_material` (`remain_material`),
  KEY `remain_probe` (`remain_probe`),
  KEY `remain_price` (`remain_price`),
  KEY `remain_in_stock` (`remain_in_stock`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица остатков изделий' AUTO_INCREMENT=1 ;
