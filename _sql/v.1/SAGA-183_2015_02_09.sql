CREATE TABLE IF NOT EXISTS `api_history` (
  `api_history_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id записи',
  `api_history_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата добавления запроса',
  `api_history_request_method` varchar(255) NOT NULL COMMENT 'Идентификатор метода запроса',
  `api_history_request` mediumtext COLLATE utf8_unicode_ci COMMENT 'Данные запроса',
  `api_history_response` mediumtext COLLATE utf8_unicode_ci COMMENT 'Ответ запроса',
  PRIMARY KEY (`api_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Таблица истории запросов' AUTO_INCREMENT=1 ;