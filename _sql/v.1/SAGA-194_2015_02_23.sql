ALTER TABLE  `user` ADD  `user_bonus_account` FLOAT NULL DEFAULT NULL COMMENT  'Бонусный счет';
ALTER TABLE  `user` ADD  `user_comment` TEXT NULL DEFAULT NULL COMMENT  'Примечание';
ALTER TABLE  `user` ADD  `user_guid` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT  'Уникальный идентификатор';
ALTER TABLE  `user` ADD UNIQUE (`user_guid`);

--
-- Структура таблицы `user_history`
--

CREATE TABLE IF NOT EXISTS `user_history` (
  `uh_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id записи',
  `uh_event` enum('UPDATE','DELETE') NOT NULL COMMENT 'Событие',
  `uh_user_id` int(11) NOT NULL COMMENT 'id изменяемой записи',
  `uh_user_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Статус активности',
  `uh_user_sort` int(11) DEFAULT NULL COMMENT 'Номер при сортировки',
  `uh_user_export` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Произведен экспорт',
  `uh_user_login` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Логин',
  `uh_user_binding` binary(32) DEFAULT NULL COMMENT 'Связи пользователя',
  `uh_user_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Пароль',
  `uh_user_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Е-mail',
  `uh_user_dob` timestamp NULL DEFAULT NULL COMMENT 'Дата рождения',
  `uh_user_rc_id` int(11) DEFAULT NULL COMMENT 'Ссылка на ресурс',
  `uh_user_role_id` int(11) DEFAULT NULL COMMENT 'Роль',
  `uh_user_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Имя',
  `uh_user_last_login` timestamp NULL DEFAULT NULL COMMENT 'Последний вход',
  `uh_user_login_count` int(11) DEFAULT '0' COMMENT 'Количество входов на сайт',
  `uh_user_date_added` timestamp NULL DEFAULT NULL COMMENT 'Дата добавления',
  `uh_user_date_changed` timestamp NULL DEFAULT NULL COMMENT 'Дата изменения',
  `uh_user_adder_id` int(11) DEFAULT NULL COMMENT 'id добавившего',
  `uh_user_changer_id` int(11) DEFAULT NULL COMMENT 'id редактора',
  `uh_user_rows_per_page` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Количество на странице',
  `uh_user_binded_count` int(11) DEFAULT NULL COMMENT 'Кличество связанных',
  `uh_user_tel` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Телефон',
  `uh_user_address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Адрес',
  `uh_user_where_know` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `uh_user_firstname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Имя',
  `uh_user_fathersname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Отчество',
  `uh_user_lastname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Фамилия',
  `uh_user_bonus_account` float DEFAULT NULL COMMENT 'Бонусный счет',
  `uh_user_comment` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Примечание',
  `uh_user_guid` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Уникальный идентификатор',
  PRIMARY KEY (`uh_id`),
  KEY `uh_user_id` (`uh_user_id`),
  KEY `uh_changer_id` (`uh_user_changer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='История изменения пользователя' AUTO_INCREMENT=1 ;

ALTER TABLE `user_history`
ADD CONSTRAINT `user_history_key_changer` FOREIGN KEY (`uh_user_changer_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `user_history_key_user` FOREIGN KEY (`uh_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;


DROP TRIGGER IF EXISTS `before_update_user`;
DELIMITER //
CREATE TRIGGER `before_update_user` BEFORE UPDATE ON `user`
FOR EACH ROW BEGIN
  INSERT INTO `user_history` SET
    `uh_event` = 'UPDATE',
    `uh_user_id` = OLD.`user_id`,
    `uh_user_status` = OLD.`user_status`,
    `uh_user_sort` = OLD.`user_sort`,
    `uh_user_export` = OLD.`user_export`,
    `uh_user_login` = OLD.`user_login`,
    `uh_user_binding`= OLD.`user_binding`,
    `uh_user_password`= OLD.`user_password`,
    `uh_user_email` = OLD.`user_email`,
    `uh_user_dob` = OLD.`user_dob`,
    `uh_user_rc_id` = OLD.`user_rc_id`,
    `uh_user_role_id` = OLD.`user_role_id`,
    `uh_user_name` = OLD.`user_name`,
    `uh_user_last_login` = OLD.`user_last_login`,
    `uh_user_login_count` = OLD.`user_login_count`,
    `uh_user_date_added` = OLD.`user_date_added`,
    `uh_user_date_changed` = OLD.`user_date_changed`,
    `uh_user_adder_id` = OLD.`user_adder_id`,
    `uh_user_changer_id` = OLD.`user_changer_id`,
    `uh_user_rows_per_page` = OLD.`user_rows_per_page`,
    `uh_user_binded_count` = OLD.`user_binded_count`,
    `uh_user_tel` = OLD.`user_tel`,
    `uh_user_address` = OLD.`user_address`,
    `uh_user_where_know` = OLD.`user_where_know`,
    `uh_user_firstname` = OLD.`user_firstname`,
    `uh_user_fathersname` = OLD.`user_fathersname`,
    `uh_user_lastname` = OLD.`user_lastname`,
    `uh_user_bonus_account` = OLD.`user_bonus_account`,
    `uh_user_comment` = OLD.`user_comment`,
    `uh_user_guid` = OLD.`user_guid`;
END
//
DELIMITER ;

DROP TRIGGER IF EXISTS `before_delete_user`;
DELIMITER //
CREATE TRIGGER `before_delete_user` BEFORE DELETE ON `user`
FOR EACH ROW BEGIN
  INSERT INTO `user_history` SET
    `uh_event` = 'DELETE',
    `uh_user_id` = OLD.`user_id`,
    `uh_user_status` = OLD.`user_status`,
    `uh_user_sort` = OLD.`user_sort`,
    `uh_user_export` = OLD.`user_export`,
    `uh_user_login` = OLD.`user_login`,
    `uh_user_binding`= OLD.`user_binding`,
    `uh_user_password`= OLD.`user_password`,
    `uh_user_email` = OLD.`user_email`,
    `uh_user_dob` = OLD.`user_dob`,
    `uh_user_rc_id` = OLD.`user_rc_id`,
    `uh_user_role_id` = OLD.`user_role_id`,
    `uh_user_name` = OLD.`user_name`,
    `uh_user_last_login` = OLD.`user_last_login`,
    `uh_user_login_count` = OLD.`user_login_count`,
    `uh_user_date_added` = OLD.`user_date_added`,
    `uh_user_date_changed` = OLD.`user_date_changed`,
    `uh_user_adder_id` = OLD.`user_adder_id`,
    `uh_user_changer_id` = OLD.`user_changer_id`,
    `uh_user_rows_per_page` = OLD.`user_rows_per_page`,
    `uh_user_binded_count` = OLD.`user_binded_count`,
    `uh_user_tel` = OLD.`user_tel`,
    `uh_user_address` = OLD.`user_address`,
    `uh_user_where_know` = OLD.`user_where_know`,
    `uh_user_firstname` = OLD.`user_firstname`,
    `uh_user_fathersname` = OLD.`user_fathersname`,
    `uh_user_lastname` = OLD.`user_lastname`,
    `uh_user_bonus_account` = OLD.`user_bonus_account`,
    `uh_user_comment` = OLD.`user_comment`,
    `uh_user_guid` = OLD.`user_guid`;
END
//
DELIMITER ;

ALTER TABLE  `user` CHANGE  `user_dob`  `user_dob` DATE NULL DEFAULT NULL;
