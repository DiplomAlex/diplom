-- phpMyAdmin SQL Dump
-- version 3.3.6
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Сен 24 2010 г., 08:24
-- Версия сервера: 5.1.42
-- Версия PHP: 5.2.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `cutecms`
--

-- --------------------------------------------------------

--
-- Структура таблицы `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_seo_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `article_status` tinyint(1) NOT NULL DEFAULT '0',
  `article_sort` int(11) DEFAULT '0',
  `article_rc_id` int(11) DEFAULT NULL,
  `article_adder_id` int(11) DEFAULT NULL,
  `article_changer_id` int(11) DEFAULT NULL,
  `article_date_added` timestamp NULL DEFAULT NULL,
  `article_date_changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `article_site_ids` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`article_id`),
  KEY `article_seo_id` (`article_seo_id`),
  KEY `article_adder_id` (`article_adder_id`),
  KEY `article_changer_id` (`article_changer_id`),
  KEY `article_sort` (`article_sort`),
  KEY `article_rc_id` (`article_rc_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `article`
--

INSERT INTO `article` (`article_id`, `article_seo_id`, `article_status`, `article_sort`, `article_rc_id`, `article_adder_id`, `article_changer_id`, `article_date_added`, `article_date_changed`, `article_site_ids`) VALUES
(6, 'asdfasdfas', 1, NULL, NULL, 1, 1, '2010-07-05 04:54:26', '2010-08-31 07:50:09', 'a:2:{i:0;s:1:"1";i:1;s:1:"3";}');

-- --------------------------------------------------------

--
-- Структура таблицы `article_description`
--

CREATE TABLE IF NOT EXISTS `article_description` (
  `article_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_desc_article_id` int(11) NOT NULL,
  `article_desc_language_id` int(11) NOT NULL,
  `article_desc_title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `article_desc_brief` text COLLATE utf8_unicode_ci,
  `article_desc_text` text COLLATE utf8_unicode_ci,
  `article_desc_author` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `article_desc_html_title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `article_desc_meta_keywords` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `article_desc_meta_description` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`article_desc_id`),
  KEY `article_desc_article_id` (`article_desc_article_id`,`article_desc_language_id`),
  KEY `article_desc_language_id` (`article_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `article_description`
--

INSERT INTO `article_description` (`article_desc_id`, `article_desc_article_id`, `article_desc_language_id`, `article_desc_title`, `article_desc_brief`, `article_desc_text`, `article_desc_author`, `article_desc_html_title`, `article_desc_meta_keywords`, `article_desc_meta_description`) VALUES
(5, 6, 2, 'asdfasdfas', 'dfasdfsa', '<p>\r\n	dfasdfasdfasdf</p>\r\n', 'asdfasdfasd', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `article_site_ref`
--

CREATE TABLE IF NOT EXISTS `article_site_ref` (
  `as_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `as_ref_article_id` int(11) NOT NULL,
  `as_ref_site_id` int(11) NOT NULL,
  `as_ref_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`as_ref_id`),
  KEY `as_ref_article_id` (`as_ref_article_id`),
  KEY `as_ref_site_id` (`as_ref_site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `article_site_ref`
--

INSERT INTO `article_site_ref` (`as_ref_id`, `as_ref_article_id`, `as_ref_site_id`, `as_ref_status`) VALUES
(5, 6, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `article_topic`
--

CREATE TABLE IF NOT EXISTS `article_topic` (
  `topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_seo_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `topic_date_added` timestamp NULL DEFAULT NULL,
  `topic_date_changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `topic_adder_id` int(11) DEFAULT NULL,
  `topic_changer_id` int(11) DEFAULT NULL,
  `topic_topic_tree_id` int(11) NOT NULL,
  `topic_site_ids` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`topic_id`),
  KEY `topic_seo_id` (`topic_seo_id`),
  KEY `topic_adder_id` (`topic_adder_id`),
  KEY `topic_changer_id` (`topic_changer_id`),
  KEY `topic_topic_tree_id` (`topic_topic_tree_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `article_topic`
--

INSERT INTO `article_topic` (`topic_id`, `topic_seo_id`, `topic_date_added`, `topic_date_changed`, `topic_adder_id`, `topic_changer_id`, `topic_topic_tree_id`, `topic_site_ids`) VALUES
(1, 'o-vendinge', '2010-07-03 07:41:37', '2010-08-30 17:47:23', 1, 1, 2, 'a:1:{i:0;s:1:"1";}');

-- --------------------------------------------------------

--
-- Структура таблицы `article_topic_description`
--

CREATE TABLE IF NOT EXISTS `article_topic_description` (
  `topic_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_desc_topic_id` int(11) NOT NULL,
  `topic_desc_language_id` int(11) NOT NULL,
  `topic_desc_name` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `topic_desc_brief` text COLLATE utf8_unicode_ci,
  `topic_desc_full` text COLLATE utf8_unicode_ci,
  `topic_desc_html_title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `topic_desc_meta_keywords` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `topic_desc_meta_description` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`topic_desc_id`),
  UNIQUE KEY `topic_desc_topic_id` (`topic_desc_topic_id`,`topic_desc_language_id`),
  KEY `topic_desc_language_id` (`topic_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `article_topic_description`
--

INSERT INTO `article_topic_description` (`topic_desc_id`, `topic_desc_topic_id`, `topic_desc_language_id`, `topic_desc_name`, `topic_desc_brief`, `topic_desc_full`, `topic_desc_html_title`, `topic_desc_meta_keywords`, `topic_desc_meta_description`) VALUES
(1, 1, 2, 'О вендинге', '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `article_topic_ref`
--

CREATE TABLE IF NOT EXISTS `article_topic_ref` (
  `at_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `at_ref_article_id` int(11) NOT NULL,
  `at_ref_topic_id` int(11) NOT NULL,
  `at_ref_sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`at_ref_id`),
  KEY `at_ref_article_id` (`at_ref_article_id`,`at_ref_topic_id`),
  KEY `at_ref_topic_id` (`at_ref_topic_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=24 ;

--
-- Дамп данных таблицы `article_topic_ref`
--

INSERT INTO `article_topic_ref` (`at_ref_id`, `at_ref_article_id`, `at_ref_topic_id`, `at_ref_sort`) VALUES
(23, 6, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `article_topic_site_ref`
--

CREATE TABLE IF NOT EXISTS `article_topic_site_ref` (
  `ts_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `ts_ref_topic_id` int(11) NOT NULL,
  `ts_ref_site_id` int(11) NOT NULL,
  `ts_ref_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ts_ref_id`),
  KEY `ts_ref_topic_id` (`ts_ref_topic_id`),
  KEY `ts_ref_site_id` (`ts_ref_site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `article_topic_site_ref`
--

INSERT INTO `article_topic_site_ref` (`ts_ref_id`, `ts_ref_topic_id`, `ts_ref_site_id`, `ts_ref_status`) VALUES
(1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `article_topic_tree`
--

CREATE TABLE IF NOT EXISTS `article_topic_tree` (
  `topic_tree_id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_tree_left` int(11) NOT NULL,
  `topic_tree_right` int(11) NOT NULL,
  `topic_tree_level` int(11) NOT NULL,
  `topic_tree_parent` int(11) NOT NULL,
  PRIMARY KEY (`topic_tree_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `article_topic_tree`
--

INSERT INTO `article_topic_tree` (`topic_tree_id`, `topic_tree_left`, `topic_tree_right`, `topic_tree_level`, `topic_tree_parent`) VALUES
(1, 1, 4, 0, 0),
(2, 2, 3, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `attribute`
--

CREATE TABLE IF NOT EXISTS `attribute` (
  `attr_id` int(11) NOT NULL AUTO_INCREMENT,
  `attr_code` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `attr_sort` int(11) DEFAULT NULL,
  `attr_status` tinyint(1) DEFAULT '0',
  `attr_type` enum('int','decimal','datetime','string','text','variant') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'int',
  `attr_price` decimal(12,4) DEFAULT NULL,
  `attr_default_value` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attr_default_value_variant` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attr_default_value_int` int(11) DEFAULT NULL,
  `attr_default_value_decimal` decimal(12,4) DEFAULT NULL,
  `attr_default_value_datetime` datetime DEFAULT NULL,
  `attr_default_value_string` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attr_default_value_text` text COLLATE utf8_unicode_ci,
  `attr_variants_xml` text COLLATE utf8_unicode_ci,
  `attr_date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attr_adder_id` int(11) DEFAULT NULL,
  `attr_date_changed` timestamp NULL DEFAULT NULL,
  `attr_changer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`attr_id`),
  UNIQUE KEY `attr_code` (`attr_code`),
  KEY `attr_type` (`attr_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `attribute`
--

INSERT INTO `attribute` (`attr_id`, `attr_code`, `attr_sort`, `attr_status`, `attr_type`, `attr_price`, `attr_default_value`, `attr_default_value_variant`, `attr_default_value_int`, `attr_default_value_decimal`, `attr_default_value_datetime`, `attr_default_value_string`, `attr_default_value_text`, `attr_variants_xml`, `attr_date_added`, `attr_adder_id`, `attr_date_changed`, `attr_changer_id`) VALUES
(1, 'bend', 0, 1, 'variant', NULL, NULL, 'SAKIC_L', NULL, NULL, NULL, 'dsgsdfg', NULL, '<collection class="Catalog_Model_Collection_AttributeVariant">\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[SAKIC L]]></text>\n<value><![CDATA[SAKIC_L]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[SAKIC R]]></text>\n<value><![CDATA[SAKIC_R]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n</collection>', '2010-04-29 10:06:59', 1, '2010-04-29 11:06:59', 1),
(5, 'color', NULL, 0, 'variant', NULL, 'черный', 'белый', NULL, NULL, NULL, NULL, NULL, '<collection class="Catalog_Model_Collection_AttributeVariant">\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[белый]]></text>\n<value><![CDATA[белый]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[черный]]></text>\n<value><![CDATA[черный]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n</collection>', '2010-09-17 19:13:22', 1, '2010-09-17 20:13:22', 1),
(7, 'size', 0, 1, 'variant', NULL, 'XXL', '', 0, 0.0000, '0000-00-00 00:00:00', '', '', '<collection class="Catalog_Model_Collection_AttributeVariant">\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[L]]></text>\n<value><![CDATA[L]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[XL]]></text>\n<value><![CDATA[XL]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[XXL]]></text>\n<value><![CDATA[XXL]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n</collection>', '2010-09-17 19:12:37', 1, '2010-09-17 20:12:37', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `attribute_description`
--

CREATE TABLE IF NOT EXISTS `attribute_description` (
  `attr_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `attr_desc_attr_id` int(11) NOT NULL,
  `attr_desc_language_id` int(11) NOT NULL,
  `attr_desc_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `attr_desc_brief` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`attr_desc_id`),
  UNIQUE KEY `attr_desc_attr_id` (`attr_desc_attr_id`,`attr_desc_language_id`),
  KEY `attr_desc_language_id` (`attr_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `attribute_description`
--

INSERT INTO `attribute_description` (`attr_desc_id`, `attr_desc_attr_id`, `attr_desc_language_id`, `attr_desc_name`, `attr_desc_brief`) VALUES
(1, 1, 1, 'Attribute1', ''),
(2, 1, 2, 'Загиб/хват', NULL),
(7, 5, 2, 'Цвет', NULL),
(8, 7, 2, 'Размер', NULL),
(9, 5, 1, 'Colour', NULL),
(10, 7, 1, 'Size', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `attribute_group`
--

CREATE TABLE IF NOT EXISTS `attribute_group` (
  `ag_id` int(11) NOT NULL AUTO_INCREMENT,
  `ag_sort` int(11) DEFAULT NULL,
  `ag_status` tinyint(1) NOT NULL,
  `ag_rc_id` int(11) DEFAULT NULL,
  `ag_ag_tree_id` int(11) DEFAULT NULL,
  `ag_date_added` timestamp NULL DEFAULT NULL,
  `ag_adder_id` int(11) DEFAULT NULL,
  `ag_date_changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ag_changer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ag_id`),
  KEY `ag_rc_id` (`ag_rc_id`),
  KEY `ag_adder_id` (`ag_adder_id`),
  KEY `ag_changer_id` (`ag_changer_id`),
  KEY `ag_ag_tree_id` (`ag_ag_tree_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `attribute_group`
--

INSERT INTO `attribute_group` (`ag_id`, `ag_sort`, `ag_status`, `ag_rc_id`, `ag_ag_tree_id`, `ag_date_added`, `ag_adder_id`, `ag_date_changed`, `ag_changer_id`) VALUES
(1, NULL, 1, NULL, 2, '2010-04-29 11:04:57', 1, '2010-04-29 11:04:57', 1),
(2, NULL, 1, NULL, 3, '2010-04-29 11:06:59', 1, '2010-04-29 11:06:59', 1),
(3, NULL, 1, NULL, 4, '2010-04-29 11:47:11', 1, '2010-04-29 11:47:11', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `attribute_group_description`
--

CREATE TABLE IF NOT EXISTS `attribute_group_description` (
  `ag_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `ag_desc_ag_id` int(11) NOT NULL,
  `ag_desc_language_id` int(11) NOT NULL,
  `ag_desc_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ag_desc_brief` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ag_desc_id`),
  UNIQUE KEY `ag_desc_ag_id` (`ag_desc_ag_id`,`ag_desc_language_id`),
  KEY `ag_desc_language_id` (`ag_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=72 ;

--
-- Дамп данных таблицы `attribute_group_description`
--

INSERT INTO `attribute_group_description` (`ag_desc_id`, `ag_desc_ag_id`, `ag_desc_language_id`, `ag_desc_name`, `ag_desc_brief`) VALUES
(69, 1, 2, 'Все товары', ''),
(70, 2, 2, 'Клюшки', NULL),
(71, 3, 2, 'Новые товары', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `attribute_group_ref`
--

CREATE TABLE IF NOT EXISTS `attribute_group_ref` (
  `agr_id` int(11) NOT NULL AUTO_INCREMENT,
  `agr_ag_id` int(11) NOT NULL,
  `agr_attr_id` int(11) NOT NULL,
  `agr_sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`agr_id`),
  UNIQUE KEY `agr_ag_id` (`agr_ag_id`,`agr_attr_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=31 ;

--
-- Дамп данных таблицы `attribute_group_ref`
--

INSERT INTO `attribute_group_ref` (`agr_id`, `agr_ag_id`, `agr_attr_id`, `agr_sort`) VALUES
(3, 2, 1, 0),
(28, 1, 7, 0),
(30, 1, 5, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `attribute_group_tree`
--

CREATE TABLE IF NOT EXISTS `attribute_group_tree` (
  `ag_tree_id` int(11) NOT NULL AUTO_INCREMENT,
  `ag_tree_left` int(11) NOT NULL DEFAULT '0',
  `ag_tree_right` int(11) NOT NULL DEFAULT '0',
  `ag_tree_level` int(11) NOT NULL DEFAULT '0',
  `ag_tree_parent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ag_tree_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `attribute_group_tree`
--

INSERT INTO `attribute_group_tree` (`ag_tree_id`, `ag_tree_left`, `ag_tree_right`, `ag_tree_level`, `ag_tree_parent`) VALUES
(1, 1, 8, 0, 0),
(2, 6, 7, 1, 1),
(3, 4, 5, 1, 1),
(4, 2, 3, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `banner`
--

CREATE TABLE IF NOT EXISTS `banner` (
  `banner_id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_sort` int(11) DEFAULT NULL,
  `banner_status` tinyint(1) NOT NULL,
  `banner_place` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `banner_image_id` int(11) DEFAULT NULL,
  `banner_link` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `banner_date_added` timestamp NULL DEFAULT NULL,
  `banner_adder_id` int(11) DEFAULT NULL,
  `banner_date_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `banner_changer_id` int(11) DEFAULT NULL,
  `banner_site_ids` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`banner_id`),
  KEY `banner_adder_id` (`banner_adder_id`),
  KEY `banner_changer_id` (`banner_changer_id`),
  KEY `banner_image_id` (`banner_image_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `banner`
--

INSERT INTO `banner` (`banner_id`, `banner_sort`, `banner_status`, `banner_place`, `banner_image_id`, `banner_link`, `banner_date_added`, `banner_adder_id`, `banner_date_changed`, `banner_changer_id`, `banner_site_ids`) VALUES
(2, NULL, 1, 'frontend_shop', NULL, '', '2009-12-25 05:44:11', NULL, '2010-08-30 13:42:59', NULL, 'a:2:{i:0;s:1:"1";i:1;s:1:"2";}'),
(3, NULL, 1, 'backend_top_client', NULL, '', '2009-12-25 05:51:33', NULL, '2009-12-25 05:51:33', NULL, NULL),
(4, NULL, 0, 'backend_top_coworker', NULL, '', '2009-12-25 07:34:46', NULL, '2009-12-25 07:34:47', NULL, NULL),
(5, NULL, 1, 'backend_top_coworker', NULL, '/', '2009-12-25 07:38:20', NULL, '2009-12-28 01:57:57', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `banner_description`
--

CREATE TABLE IF NOT EXISTS `banner_description` (
  `banner_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_desc_banner_id` int(11) NOT NULL,
  `banner_desc_language_id` int(11) NOT NULL,
  `banner_desc_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `banner_desc_text` text COLLATE utf8_unicode_ci,
  `banner_desc_html` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`banner_desc_id`),
  UNIQUE KEY `banner_desc_banner_id` (`banner_desc_banner_id`,`banner_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `banner_description`
--

INSERT INTO `banner_description` (`banner_desc_id`, `banner_desc_banner_id`, `banner_desc_language_id`, `banner_desc_name`, `banner_desc_text`, `banner_desc_html`) VALUES
(1, 2, 2, 'banner1', '', ''),
(2, 3, 2, 'banner2', '', ''),
(3, 4, 2, 'banner1_cw', '', ''),
(4, 5, 2, 'banner2_cw', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `banner_site_ref`
--

CREATE TABLE IF NOT EXISTS `banner_site_ref` (
  `bs_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `bs_ref_banner_id` int(11) NOT NULL,
  `bs_ref_site_id` int(11) NOT NULL,
  `bs_ref_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`bs_ref_id`),
  KEY `bs_ref_banner_id` (`bs_ref_banner_id`),
  KEY `bs_ref_site_id` (`bs_ref_site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `banner_site_ref`
--

INSERT INTO `banner_site_ref` (`bs_ref_id`, `bs_ref_banner_id`, `bs_ref_site_id`, `bs_ref_status`) VALUES
(3, 2, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_seo_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_status` tinyint(1) NOT NULL DEFAULT '0',
  `category_is_popular` tinyint(1) NOT NULL DEFAULT '0',
  `category_rc_id` int(11) DEFAULT NULL,
  `category_category_tree_id` int(11) DEFAULT NULL,
  `category_date_added` timestamp NULL DEFAULT NULL,
  `category_adder_id` int(11) DEFAULT NULL,
  `category_date_changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `category_changer_id` int(11) DEFAULT NULL,
  `category_param1` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_param2` varchar(552) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_param3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_guid` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_parent_guid` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_site_ids` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `category_adder_id` (`category_adder_id`),
  KEY `category_changer_id` (`category_changer_id`),
  KEY `category_rc_id` (`category_rc_id`),
  KEY `category_category_tree_id` (`category_category_tree_id`),
  KEY `category_guid` (`category_guid`),
  KEY `category_parent_guid` (`category_parent_guid`),
  KEY `category_seo_id` (`category_seo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=180 ;

--
-- Дамп данных таблицы `category`
--

INSERT INTO `category` (`category_id`, `category_seo_id`, `category_status`, `category_is_popular`, `category_rc_id`, `category_category_tree_id`, `category_date_added`, `category_adder_id`, `category_date_changed`, `category_changer_id`, `category_param1`, `category_param2`, `category_param3`, `category_guid`, `category_parent_guid`, `category_site_ids`) VALUES
(179, 'kategorija-1', 1, 0, NULL, 130, '2010-09-01 16:13:39', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'a:1:{i:0;s:1:"1";}');

-- --------------------------------------------------------

--
-- Структура таблицы `category_description`
--

CREATE TABLE IF NOT EXISTS `category_description` (
  `category_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_desc_category_id` int(11) NOT NULL DEFAULT '0',
  `category_desc_language_id` int(11) NOT NULL DEFAULT '0',
  `category_desc_name` text COLLATE utf8_unicode_ci NOT NULL,
  `category_desc_brief` text COLLATE utf8_unicode_ci,
  `category_desc_full` text COLLATE utf8_unicode_ci,
  `category_desc_html_title` tinytext COLLATE utf8_unicode_ci,
  `category_desc_meta_keywords` text COLLATE utf8_unicode_ci,
  `category_desc_meta_description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`category_desc_id`),
  UNIQUE KEY `category_desc_category_id` (`category_desc_category_id`,`category_desc_language_id`),
  KEY `category_desc_language_id` (`category_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1662 ;

--
-- Дамп данных таблицы `category_description`
--

INSERT INTO `category_description` (`category_desc_id`, `category_desc_category_id`, `category_desc_language_id`, `category_desc_name`, `category_desc_brief`, `category_desc_full`, `category_desc_html_title`, `category_desc_meta_keywords`, `category_desc_meta_description`) VALUES
(1660, 179, 2, 'Категория 1', '', '', '', '', ''),
(1661, 179, 1, 'Category 1', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `category_item_ref`
--

CREATE TABLE IF NOT EXISTS `category_item_ref` (
  `ci_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `ci_ref_category_id` int(11) NOT NULL,
  `ci_ref_item_id` int(11) NOT NULL,
  `ci_ref_sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`ci_ref_id`),
  UNIQUE KEY `ci_ref_category_id` (`ci_ref_category_id`,`ci_ref_item_id`),
  KEY `ci_ref_item_id` (`ci_ref_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1959 ;

--
-- Дамп данных таблицы `category_item_ref`
--

INSERT INTO `category_item_ref` (`ci_ref_id`, `ci_ref_category_id`, `ci_ref_item_id`, `ci_ref_sort`) VALUES
(1958, 179, 432, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `category_site_ref`
--

CREATE TABLE IF NOT EXISTS `category_site_ref` (
  `cs_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `cs_ref_category_id` int(11) NOT NULL,
  `cs_ref_site_id` int(11) NOT NULL,
  `cs_ref_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cs_ref_id`),
  KEY `cs_ref_category_id` (`cs_ref_category_id`),
  KEY `cs_ref_site_id` (`cs_ref_site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=81 ;

--
-- Дамп данных таблицы `category_site_ref`
--

INSERT INTO `category_site_ref` (`cs_ref_id`, `cs_ref_category_id`, `cs_ref_site_id`, `cs_ref_status`) VALUES
(80, 179, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `category_tree`
--

CREATE TABLE IF NOT EXISTS `category_tree` (
  `category_tree_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_tree_left` int(11) NOT NULL DEFAULT '0',
  `category_tree_right` int(11) NOT NULL DEFAULT '0',
  `category_tree_level` int(11) NOT NULL DEFAULT '0',
  `category_tree_parent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_tree_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=131 ;

--
-- Дамп данных таблицы `category_tree`
--

INSERT INTO `category_tree` (`category_tree_id`, `category_tree_left`, `category_tree_right`, `category_tree_level`, `category_tree_parent`) VALUES
(1, 1, 4, 0, 0),
(130, 2, 3, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_status` tinyint(1) DEFAULT '0',
  `comment_subject` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_text` text COLLATE utf8_unicode_ci,
  `comment_rc_id` int(11) DEFAULT NULL,
  `comment_comment_tree_id` int(11) DEFAULT NULL,
  `comment_date_added` timestamp NULL DEFAULT NULL,
  `comment_date_changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `comment_adder_id` int(11) DEFAULT NULL,
  `comment_adder_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_adder_email` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_changer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `comment_rc_id` (`comment_rc_id`),
  KEY `comment_comment_tree_id` (`comment_comment_tree_id`),
  KEY `comment_adder_id` (`comment_adder_id`),
  KEY `comment_changer_id` (`comment_changer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Дамп данных таблицы `comment`
--

INSERT INTO `comment` (`comment_id`, `comment_status`, `comment_subject`, `comment_text`, `comment_rc_id`, `comment_comment_tree_id`, `comment_date_added`, `comment_date_changed`, `comment_adder_id`, `comment_adder_name`, `comment_adder_email`, `comment_changer_id`) VALUES
(1, 1, 'xcbxcvb', '<p>\r\n	xcvbxcvbxcvbxcvb sd</p>\r\n<p>\r\n	f fgsdfgsdfgs</p>\r\n<p>\r\n	sdf gsdfgsdfgsd</p>\r\n<p>\r\n	sdfgsdf</p>\r\n<p>\r\n	gsdf sddfgsdf&nbsp; dsfgsdfgsdfgsdfgsdfgdsfgsdfgsdfg doi, p u[ dgpoiu m po posd fsdf</p>\r\n<p>\r\n	dsfpg df m p df[pdofi gdsfg&#39;dfgsdlkfjg;ljh;lfjh;lfkjhrtij[wptoiwerpt 54=5 96=-45 09roi rt</p>\r\n<p>\r\n	t [yertiuj e;rltkjh f;f;h[09yhirthio ertioh pfogih fdg;od fgh;ok rt[hpwo rghp[owh r]toer][ hoert</p>\r\n<p>\r\n	вапржвд пждвало рвазпщр шэещршэвжщро вапржд вавапр</p>\r\n', NULL, 3, '2010-07-15 16:59:19', '2010-07-15 18:44:06', 1, NULL, NULL, 1),
(3, 1, 'gjfjhfghjfghjfgjh', '<p>\r\n	fgjhfgjhfghjfghjfghjfgjh</p>\r\n', NULL, 5, '2010-07-15 19:03:46', NULL, 1, NULL, NULL, NULL),
(4, 1, 'ddddddddddddddd', '<p>\r\n	ddddddddddddddddd</p>\r\n', NULL, 6, '2010-07-15 19:05:02', NULL, 1, NULL, NULL, NULL),
(6, 1, 'xcvbxcvbxcvbxcvb', '<p>\r\n	xcvbxcvbxcvbxcvbxcvbxcvbxcvbxcvbxcvb</p>\r\n', NULL, 8, '2010-07-15 19:14:03', NULL, 1, NULL, NULL, NULL),
(7, -1, '', '<p>\r\n	mhkbhmnb,mnb,mnb,mb</p>\r\n', NULL, 9, '2010-07-28 11:18:21', '2010-07-28 13:25:33', 1, NULL, NULL, 1),
(8, 1, '', '<p>\r\n	asdfasdfasdfasd</p>\r\n', NULL, 10, '2010-07-28 11:37:15', NULL, 1, NULL, NULL, NULL),
(9, 1, '', '<p>\r\n	111111111111111111111111</p>\r\n', NULL, 11, '2010-07-28 11:37:35', NULL, 1, NULL, NULL, NULL),
(10, 1, '', '<p>\r\n	222222222222222</p>\r\n', NULL, 12, '2010-07-28 11:37:47', NULL, 1, NULL, NULL, NULL),
(11, 1, '', '<p>\r\n	dfghdfghdfghdfgh3333</p>\r\n', NULL, 13, '2010-07-28 11:38:46', NULL, 1, NULL, NULL, NULL),
(12, 0, '', '<p>\r\n	444444444444444444444444444</p>\r\n', NULL, 14, '2010-07-28 11:41:07', NULL, 1, NULL, NULL, NULL),
(13, 0, '', '<p>\r\n	555555555555555555555555555</p>\r\n', NULL, 15, '2010-07-28 11:41:35', NULL, 1, NULL, NULL, NULL),
(14, -1, '', '<p>\r\n	66666666666666666666666666666666666</p>\r\n', NULL, 16, '2010-07-28 12:07:10', NULL, 1, NULL, NULL, NULL),
(15, -1, NULL, 'er00000000000', NULL, 17, '2010-07-28 16:10:15', NULL, NULL, NULL, NULL, NULL),
(16, -1, NULL, 'te5467567567567', NULL, 18, '2010-07-28 16:12:21', NULL, NULL, NULL, NULL, NULL),
(17, -1, NULL, 'fg3456345634', NULL, 19, '2010-07-28 16:13:44', NULL, NULL, NULL, NULL, NULL),
(18, -1, NULL, '56345634563456', NULL, 20, '2010-07-28 16:14:15', NULL, NULL, '345634563456', '3456345634', NULL),
(19, -1, NULL, 'bvbnmvbnmvbmn', NULL, 21, '2010-08-05 17:01:50', NULL, NULL, 'vbvbmn', 'vbnmvbmnv', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `comment_ref`
--

CREATE TABLE IF NOT EXISTS `comment_ref` (
  `comment_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_ref_comment_id` int(11) NOT NULL,
  `comment_ref_content_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment_ref_content_id` int(11) NOT NULL,
  PRIMARY KEY (`comment_ref_id`),
  KEY `comment_ref_content_type` (`comment_ref_content_type`,`comment_ref_content_id`),
  KEY `comment_ref_comment_id` (`comment_ref_comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=24 ;

--
-- Дамп данных таблицы `comment_ref`
--

INSERT INTO `comment_ref` (`comment_ref_id`, `comment_ref_comment_id`, `comment_ref_content_type`, `comment_ref_content_id`) VALUES
(4, 1, 'article', 6),
(6, 3, 'article', 6),
(7, 4, 'article', 6),
(9, 6, 'article', 6),
(11, 8, 'article', 6),
(12, 9, 'article', 6),
(13, 10, 'article', 6),
(14, 11, 'article', 6),
(15, 12, 'article', 6),
(16, 13, 'article', 6),
(17, 14, 'article', 6),
(18, 7, 'article', 6),
(19, 15, 'article', 6),
(20, 16, 'article', 6),
(21, 17, 'article', 6),
(22, 18, 'article', 6),
(23, 19, 'catalog/item', 232);

-- --------------------------------------------------------

--
-- Структура таблицы `comment_tree`
--

CREATE TABLE IF NOT EXISTS `comment_tree` (
  `comment_tree_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_tree_left` int(11) NOT NULL,
  `comment_tree_right` int(11) NOT NULL,
  `comment_tree_level` int(11) NOT NULL,
  `comment_tree_parent` int(11) NOT NULL,
  PRIMARY KEY (`comment_tree_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;

--
-- Дамп данных таблицы `comment_tree`
--

INSERT INTO `comment_tree` (`comment_tree_id`, `comment_tree_left`, `comment_tree_right`, `comment_tree_level`, `comment_tree_parent`) VALUES
(1, 1, 36, 0, 0),
(3, 2, 25, 1, 1),
(5, 21, 22, 2, 3),
(6, 3, 20, 2, 3),
(8, 23, 24, 2, 3),
(9, 4, 7, 3, 6),
(10, 8, 19, 3, 6),
(11, 5, 6, 4, 9),
(12, 9, 18, 4, 10),
(13, 10, 13, 5, 12),
(14, 11, 12, 6, 13),
(15, 14, 15, 5, 12),
(16, 16, 17, 5, 12),
(17, 26, 27, 1, 1),
(18, 28, 29, 1, 1),
(19, 30, 31, 1, 1),
(20, 32, 33, 1, 1),
(21, 34, 35, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `email_queue`
--

CREATE TABLE IF NOT EXISTS `email_queue` (
  `email_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_from` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_from_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_to` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_to_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_subject` text COLLATE utf8_unicode_ci NOT NULL,
  `email_body_text` text COLLATE utf8_unicode_ci NOT NULL,
  `email_body_html` text COLLATE utf8_unicode_ci,
  `email_date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`email_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `email_queue`
--

INSERT INTO `email_queue` (`email_id`, `email_from`, `email_from_name`, `email_to`, `email_to_name`, `email_subject`, `email_body_text`, `email_body_html`, `email_date_added`) VALUES
(1, 'admin@bubblegum.ru', 'bubblegum.ru', 'donenkodv@gmail.com', 'Admin', 'Добавлена новая задача N18', 'Пользователь  добавил для Вас задачу\n\nТема: dgfhfhgdfghdfgСрок выполнения: 2010-06-25Задача: <p>\r\n	hdfghdfghfdgh</p>\r\n\nДанное сообщение создано автоматически, отвечать на это письмо не обязательно.\nАдминистрация сайта Bubblegum.Ru\n\n', '<h1>Пользователь  добавил для Вас задачу</h1>\n<ul>\n    <li><strong>Тема:</strong> dgfhfhgdfghdfg </li>\n    <li><strong>Срок выполнения:</strong> 2010-06-25 </li>\n    <li><strong>Задача</strong> <p>\r\n	hdfghdfghfdgh</p>\r\n</li>\n</ul>\n<p>Данное сообщение создано автоматически, отвечать на это письмо не обязательно.<p>\n<p>Администрация сайта Bubblegum.Ru</p>\n\n\n', '2010-06-04 10:17:30'),
(2, 'admin@bubblegum.ru', 'bubblegum.ru', 'samoylova@test.com', 'Самойлова Анна', 'Добавлена новая задача N18', 'Пользователь  добавил для Вас задачу\n\nТема: dgfhfhgdfghdfgСрок выполнения: 2010-06-25Задача: <p>\r\n	hdfghdfghfdgh</p>\r\n\nДанное сообщение создано автоматически, отвечать на это письмо не обязательно.\nАдминистрация сайта Bubblegum.Ru\n\n', '<h1>Пользователь  добавил для Вас задачу</h1>\n<ul>\n    <li><strong>Тема:</strong> dgfhfhgdfghdfg </li>\n    <li><strong>Срок выполнения:</strong> 2010-06-25 </li>\n    <li><strong>Задача</strong> <p>\r\n	hdfghdfghfdgh</p>\r\n</li>\n</ul>\n<p>Данное сообщение создано автоматически, отвечать на это письмо не обязательно.<p>\n<p>Администрация сайта Bubblegum.Ru</p>\n\n\n', '2010-06-04 10:17:30'),
(3, 'admin@bubblegum.ru', 'bubblegum.ru', 'info@gumballs.ru', 'Янчик Евгений', 'Добавлена новая задача N18', 'Пользователь  добавил для Вас задачу\n\nТема: dgfhfhgdfghdfgСрок выполнения: 2010-06-25Задача: <p>\r\n	hdfghdfghfdgh</p>\r\n\nДанное сообщение создано автоматически, отвечать на это письмо не обязательно.\nАдминистрация сайта Bubblegum.Ru\n\n', '<h1>Пользователь  добавил для Вас задачу</h1>\n<ul>\n    <li><strong>Тема:</strong> dgfhfhgdfghdfg </li>\n    <li><strong>Срок выполнения:</strong> 2010-06-25 </li>\n    <li><strong>Задача</strong> <p>\r\n	hdfghdfghfdgh</p>\r\n</li>\n</ul>\n<p>Данное сообщение создано автоматически, отвечать на это письмо не обязательно.<p>\n<p>Администрация сайта Bubblegum.Ru</p>\n\n\n', '2010-06-04 10:17:30');

-- --------------------------------------------------------

--
-- Структура таблицы `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `faq_id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_seo_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `faq_status` tinyint(1) NOT NULL,
  `faq_sort` int(11) DEFAULT NULL,
  `faq_date_added` timestamp NULL DEFAULT NULL,
  `faq_date_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `faq_adder_id` int(11) DEFAULT NULL,
  `faq_changer_id` int(11) DEFAULT NULL,
  `faq_site_ids` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`faq_id`),
  KEY `faq_seo_id` (`faq_seo_id`),
  KEY `faq_adder_id` (`faq_adder_id`,`faq_changer_id`),
  KEY `faq_changer_id` (`faq_changer_id`),
  KEY `faq_sort` (`faq_sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `faq`
--


-- --------------------------------------------------------

--
-- Структура таблицы `faq_description`
--

CREATE TABLE IF NOT EXISTS `faq_description` (
  `faq_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_desc_faq_id` int(11) NOT NULL,
  `faq_desc_language_id` int(11) NOT NULL,
  `faq_desc_quest` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `faq_desc_brief` text COLLATE utf8_unicode_ci,
  `faq_desc_full` text COLLATE utf8_unicode_ci,
  `faq_desc_html_title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `faq_desc_meta_description` text COLLATE utf8_unicode_ci,
  `faq_desc_meta_keywords` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`faq_desc_id`),
  UNIQUE KEY `faq_desc_faq_id_2` (`faq_desc_faq_id`,`faq_desc_language_id`),
  KEY `faq_desc_language_id` (`faq_desc_language_id`),
  KEY `faq_desc_faq_id` (`faq_desc_faq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `faq_description`
--


-- --------------------------------------------------------

--
-- Структура таблицы `faq_site_ref`
--

CREATE TABLE IF NOT EXISTS `faq_site_ref` (
  `fs_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `fs_ref_faq_id` int(11) NOT NULL,
  `fs_ref_site_id` int(11) NOT NULL,
  `fs_ref_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`fs_ref_id`),
  KEY `fs_ref_faq_id` (`fs_ref_faq_id`),
  KEY `fs_ref_site_id` (`fs_ref_site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `faq_site_ref`
--


-- --------------------------------------------------------

--
-- Структура таблицы `gallery_item`
--

CREATE TABLE IF NOT EXISTS `gallery_item` (
  `gallery_id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gallery_status` int(1) NOT NULL DEFAULT '0',
  `gallery_content_type` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gallery_content_id` int(11) DEFAULT NULL,
  `gallery_rc_id` int(11) DEFAULT NULL,
  `gallery_date_added` timestamp NULL DEFAULT NULL,
  `gallery_date_changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `gallery_adder_id` int(11) DEFAULT NULL,
  `gallery_changer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`gallery_id`),
  KEY `gallery_content_type` (`gallery_content_type`,`gallery_content_id`),
  KEY `gallery_rc_id` (`gallery_rc_id`),
  KEY `gallery_adder_id` (`gallery_adder_id`),
  KEY `gallery_changer_id` (`gallery_changer_id`),
  KEY `gallery_hash` (`gallery_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `gallery_item`
--


-- --------------------------------------------------------

--
-- Структура таблицы `gallery_item_description`
--

CREATE TABLE IF NOT EXISTS `gallery_item_description` (
  `gallery_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_desc_gallery_id` int(11) NOT NULL,
  `gallery_desc_language_id` int(11) NOT NULL,
  `gallery_desc_name` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gallery_desc_description` text COLLATE utf8_unicode_ci,
  `gallery_desc_html_title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gallery_desc_meta_keywords` text COLLATE utf8_unicode_ci,
  `gallery_desc_meta_description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`gallery_desc_id`),
  KEY `gallery_desc_gallery_id` (`gallery_desc_gallery_id`,`gallery_desc_language_id`),
  KEY `gallery_desc_language_id` (`gallery_desc_language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `gallery_item_description`
--


-- --------------------------------------------------------

--
-- Структура таблицы `gallery_item_ref`
--

CREATE TABLE IF NOT EXISTS `gallery_item_ref` (
  `gi_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `gi_ref_gallery_id` int(11) NOT NULL,
  `gi_ref_content_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gi_ref_content_id` int(11) NOT NULL,
  PRIMARY KEY (`gi_ref_id`),
  KEY `gi_ref_content_type` (`gi_ref_content_type`,`gi_ref_content_id`),
  KEY `gi_ref_gallery_id` (`gi_ref_gallery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `gallery_item_ref`
--


-- --------------------------------------------------------

--
-- Структура таблицы `issue`
--

CREATE TABLE IF NOT EXISTS `issue` (
  `issue_id` int(11) NOT NULL AUTO_INCREMENT,
  `issue_status` tinyint(1) NOT NULL DEFAULT '0',
  `issue_subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `issue_text` text COLLATE utf8_unicode_ci NOT NULL,
  `issue_date_complete` datetime DEFAULT NULL,
  `issue_date_due` datetime NOT NULL,
  `issue_date_added` timestamp NULL DEFAULT NULL,
  `issue_date_changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `issue_adder_id` int(11) DEFAULT NULL,
  `issue_changer_id` int(11) DEFAULT NULL,
  `issue_status_history_serialized` text COLLATE utf8_unicode_ci,
  `issue_date_due_history_serialized` text COLLATE utf8_unicode_ci,
  `issue_comments_count` int(11) DEFAULT NULL,
  `issue_users_serialized` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`issue_id`),
  KEY `issue_adder_id` (`issue_adder_id`),
  KEY `issue_changer_id` (`issue_changer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `issue`
--


-- --------------------------------------------------------

--
-- Структура таблицы `issue_comment`
--

CREATE TABLE IF NOT EXISTS `issue_comment` (
  `ic_id` int(11) NOT NULL AUTO_INCREMENT,
  `ic_status` tinyint(1) NOT NULL DEFAULT '0',
  `ic_issue_id` int(11) NOT NULL,
  `ic_text` text COLLATE utf8_unicode_ci NOT NULL,
  `ic_ic_tree_id` int(11) DEFAULT NULL,
  `ic_date_added` timestamp NULL DEFAULT NULL,
  `ic_date_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ic_adder_id` int(11) DEFAULT NULL,
  `ic_changer_id` int(11) DEFAULT NULL,
  `ic_status_history` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`ic_id`),
  KEY `ic_adder_id` (`ic_adder_id`),
  KEY `ic_changer_id` (`ic_changer_id`),
  KEY `ic_ic_tree_id` (`ic_ic_tree_id`),
  KEY `ic_issue_id` (`ic_issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `issue_comment`
--


-- --------------------------------------------------------

--
-- Структура таблицы `issue_comment_tree`
--

CREATE TABLE IF NOT EXISTS `issue_comment_tree` (
  `ic_tree_id` int(11) NOT NULL AUTO_INCREMENT,
  `ic_tree_left` int(11) NOT NULL DEFAULT '0',
  `ic_tree_right` int(11) NOT NULL DEFAULT '0',
  `ic_tree_level` int(11) NOT NULL DEFAULT '0',
  `ic_tree_parent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ic_tree_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `issue_comment_tree`
--


-- --------------------------------------------------------

--
-- Структура таблицы `issue_user`
--

CREATE TABLE IF NOT EXISTS `issue_user` (
  `iu_id` int(11) NOT NULL AUTO_INCREMENT,
  `iu_issue_id` int(11) NOT NULL,
  `iu_user_id` int(11) NOT NULL,
  PRIMARY KEY (`iu_id`),
  UNIQUE KEY `iu_issue_id` (`iu_issue_id`,`iu_user_id`),
  KEY `iu_user_id` (`iu_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `issue_user`
--


-- --------------------------------------------------------

--
-- Структура таблицы `item`
--

CREATE TABLE IF NOT EXISTS `item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_seo_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_guid` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_category_guid` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_sku` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_sort` int(11) DEFAULT NULL,
  `item_status` tinyint(1) NOT NULL DEFAULT '0',
  `item_type` tinyint(1) NOT NULL DEFAULT '0',
  `item_price` double(12,2) DEFAULT NULL,
  `item_price2` double(12,2) DEFAULT NULL,
  `item_price3` double(12,2) DEFAULT NULL,
  `item_old_price` double(12,2) DEFAULT NULL,
  `item_stock_qty` double(12,2) DEFAULT NULL,
  `item_rc_id` int(11) DEFAULT NULL,
  `item_date_added` timestamp NULL DEFAULT NULL,
  `item_adder_id` int(11) DEFAULT NULL,
  `item_date_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `item_changer_id` int(11) DEFAULT NULL,
  `item_views` int(11) DEFAULT NULL,
  `item_votes` int(11) DEFAULT NULL,
  `item_rate` int(11) DEFAULT NULL,
  `item_ext_link` text COLLATE utf8_unicode_ci,
  `item_param1` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_param2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_param3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_model` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_site_ids` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_attributes_xml` text COLLATE utf8_unicode_ci,
  `item_images_xml` text COLLATE utf8_unicode_ci,
  `item_brules_xml` text COLLATE utf8_unicode_ci,
  `item_manufacturer_id` int(11) DEFAULT NULL,
  `item_is_new` smallint(1) DEFAULT NULL,
  `item_is_popular` smallint(1) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_seo_id` (`item_seo_id`),
  KEY `item_adder_id` (`item_adder_id`),
  KEY `item_changer_id` (`item_changer_id`),
  KEY `item_rc_id` (`item_rc_id`),
  KEY `item_sku` (`item_sku`),
  KEY `item_model` (`item_model`),
  KEY `item_guid` (`item_guid`),
  KEY `item_site_ids` (`item_site_ids`(255)),
  KEY `item_type` (`item_type`),
  KEY `item_manufacturer_id` (`item_manufacturer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=434 ;

--
-- Дамп данных таблицы `item`
--

INSERT INTO `item` (`item_id`, `item_seo_id`, `item_guid`, `item_category_guid`, `item_sku`, `item_code`, `item_sort`, `item_status`, `item_type`, `item_price`, `item_price2`, `item_price3`, `item_old_price`, `item_stock_qty`, `item_rc_id`, `item_date_added`, `item_adder_id`, `item_date_changed`, `item_changer_id`, `item_views`, `item_votes`, `item_rate`, `item_ext_link`, `item_param1`, `item_param2`, `item_param3`, `item_model`, `item_site_ids`, `item_attributes_xml`, `item_images_xml`, `item_brules_xml`, `item_manufacturer_id`, `item_is_new`, `item_is_popular`) VALUES
(432, 'tovar-1', NULL, NULL, '', '', 1, 1, 0, 123.00, 122.00, 121.00, NULL, 137.00, 1, '2010-09-01 16:14:13', 1, '2010-09-21 18:08:07', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'a:1:{i:0;s:1:"1";}', '<collection class="Catalog_Model_Collection_Attribute">\n<object class="Catalog_Model_Object_Attribute">\n<id><![CDATA[7]]></id>\n<status><![CDATA[1]]></status>\n<sort><![CDATA[0]]></sort>\n<code><![CDATA[size]]></code>\n<name><![CDATA[Размер]]></name>\n<brief><![CDATA[]]></brief>\n<date_adder><![CDATA[]]></date_adder>\n<date_changed><![CDATA[2010-09-17 20:12:37]]></date_changed>\n<adder_id><![CDATA[1]]></adder_id>\n<changer_id><![CDATA[1]]></changer_id>\n<type><![CDATA[variant]]></type>\n<variants class="Catalog_Model_Collection_AttributeVariant">\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[L]]></text>\n<value><![CDATA[L]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[XL]]></text>\n<value><![CDATA[XL]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[XXL]]></text>\n<value><![CDATA[XXL]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n</variants>\n<variants_xml><![CDATA[<collection class="Catalog_Model_Collection_AttributeVariant">\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]]]><![CDATA[></sort>\n<text><![CDATA[L]]]]><![CDATA[></text>\n<value><![CDATA[L]]]]><![CDATA[></value>\n<hash><![CDATA[]]]]><![CDATA[></hash>\n<param1><![CDATA[]]]]><![CDATA[></param1>\n<param2><![CDATA[]]]]><![CDATA[></param2>\n<param3><![CDATA[]]]]><![CDATA[></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]]]><![CDATA[></sort>\n<text><![CDATA[XL]]]]><![CDATA[></text>\n<value><![CDATA[XL]]]]><![CDATA[></value>\n<hash><![CDATA[]]]]><![CDATA[></hash>\n<param1><![CDATA[]]]]><![CDATA[></param1>\n<param2><![CDATA[]]]]><![CDATA[></param2>\n<param3><![CDATA[]]]]><![CDATA[></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]]]><![CDATA[></sort>\n<text><![CDATA[XXL]]]]><![CDATA[></text>\n<value><![CDATA[XXL]]]]><![CDATA[></value>\n<hash><![CDATA[]]]]><![CDATA[></hash>\n<param1><![CDATA[]]]]><![CDATA[></param1>\n<param2><![CDATA[]]]]><![CDATA[></param2>\n<param3><![CDATA[]]]]><![CDATA[></param3>\n</object>\n</collection>]]></variants_xml>\n<variants_text><![CDATA[]]></variants_text>\n<variants_array><![CDATA[]]></variants_array>\n<default_value><![CDATA[XXL]]></default_value>\n<current_value><![CDATA[XL]]></current_value>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n<hash><![CDATA[attribute_4c93937507e74]]></hash>\n<attribute_groups><![CDATA[a:1:{i:0;s:1:"1";}]]></attribute_groups>\n<description_language_ru_name><![CDATA[]]></description_language_ru_name>\n<description_language_en_name><![CDATA[]]></description_language_en_name>\n<description_language_2_name><![CDATA[Размер]]></description_language_2_name>\n<description_language_1_name><![CDATA[Size]]></description_language_1_name>\n</object>\n<object class="Catalog_Model_Object_Attribute">\n<id><![CDATA[1]]></id>\n<status><![CDATA[1]]></status>\n<sort><![CDATA[0]]></sort>\n<code><![CDATA[bend]]></code>\n<name><![CDATA[Загиб/хват]]></name>\n<brief><![CDATA[]]></brief>\n<date_adder><![CDATA[]]></date_adder>\n<date_changed><![CDATA[2010-04-29 11:06:59]]></date_changed>\n<adder_id><![CDATA[1]]></adder_id>\n<changer_id><![CDATA[1]]></changer_id>\n<type><![CDATA[variant]]></type>\n<variants class="Catalog_Model_Collection_AttributeVariant">\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[SAKIC L]]></text>\n<value><![CDATA[SAKIC_L]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[SAKIC R]]></text>\n<value><![CDATA[SAKIC_R]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n</variants>\n<variants_xml><![CDATA[<collection class="Catalog_Model_Collection_AttributeVariant">\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]]]><![CDATA[></sort>\n<text><![CDATA[SAKIC L]]]]><![CDATA[></text>\n<value><![CDATA[SAKIC_L]]]]><![CDATA[></value>\n<hash><![CDATA[]]]]><![CDATA[></hash>\n<param1><![CDATA[]]]]><![CDATA[></param1>\n<param2><![CDATA[]]]]><![CDATA[></param2>\n<param3><![CDATA[]]]]><![CDATA[></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]]]><![CDATA[></sort>\n<text><![CDATA[SAKIC R]]]]><![CDATA[></text>\n<value><![CDATA[SAKIC_R]]]]><![CDATA[></value>\n<hash><![CDATA[]]]]><![CDATA[></hash>\n<param1><![CDATA[]]]]><![CDATA[></param1>\n<param2><![CDATA[]]]]><![CDATA[></param2>\n<param3><![CDATA[]]]]><![CDATA[></param3>\n</object>\n</collection>]]></variants_xml>\n<variants_text><![CDATA[]]></variants_text>\n<variants_array><![CDATA[]]></variants_array>\n<default_value><![CDATA[]]></default_value>\n<current_value><![CDATA[]]></current_value>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n<hash><![CDATA[attribute_4c939361834a1]]></hash>\n<attribute_groups><![CDATA[]]></attribute_groups>\n<description_language_ru_name><![CDATA[]]></description_language_ru_name>\n<description_language_en_name><![CDATA[]]></description_language_en_name>\n<description_language_2_name><![CDATA[Загиб/хват]]></description_language_2_name>\n<description_language_1_name><![CDATA[Attribute1]]></description_language_1_name>\n</object>\n<object class="Catalog_Model_Object_Attribute">\n<id><![CDATA[5]]></id>\n<status><![CDATA[0]]></status>\n<sort><![CDATA[]]></sort>\n<code><![CDATA[color]]></code>\n<name><![CDATA[Цвет]]></name>\n<brief><![CDATA[]]></brief>\n<date_adder><![CDATA[]]></date_adder>\n<date_changed><![CDATA[2010-09-17 20:13:22]]></date_changed>\n<adder_id><![CDATA[1]]></adder_id>\n<changer_id><![CDATA[1]]></changer_id>\n<type><![CDATA[variant]]></type>\n<variants class="Catalog_Model_Collection_AttributeVariant">\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[белый]]></text>\n<value><![CDATA[белый]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]></sort>\n<text><![CDATA[черный]]></text>\n<value><![CDATA[черный]]></value>\n<hash><![CDATA[]]></hash>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n</object>\n</variants>\n<variants_xml><![CDATA[<collection class="Catalog_Model_Collection_AttributeVariant">\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]]]><![CDATA[></sort>\n<text><![CDATA[белый]]]]><![CDATA[></text>\n<value><![CDATA[белый]]]]><![CDATA[></value>\n<hash><![CDATA[]]]]><![CDATA[></hash>\n<param1><![CDATA[]]]]><![CDATA[></param1>\n<param2><![CDATA[]]]]><![CDATA[></param2>\n<param3><![CDATA[]]]]><![CDATA[></param3>\n</object>\n<object class="Catalog_Model_Object_AttributeVariant">\n<sort><![CDATA[]]]]><![CDATA[></sort>\n<text><![CDATA[черный]]]]><![CDATA[></text>\n<value><![CDATA[черный]]]]><![CDATA[></value>\n<hash><![CDATA[]]]]><![CDATA[></hash>\n<param1><![CDATA[]]]]><![CDATA[></param1>\n<param2><![CDATA[]]]]><![CDATA[></param2>\n<param3><![CDATA[]]]]><![CDATA[></param3>\n</object>\n</collection>]]></variants_xml>\n<variants_text><![CDATA[]]></variants_text>\n<variants_array><![CDATA[]]></variants_array>\n<default_value><![CDATA[черный]]></default_value>\n<current_value><![CDATA[белый]]></current_value>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n<hash><![CDATA[attribute_4c9393a24176b]]></hash>\n<attribute_groups><![CDATA[a:1:{i:0;s:1:"1";}]]></attribute_groups>\n<description_language_ru_name><![CDATA[]]></description_language_ru_name>\n<description_language_en_name><![CDATA[]]></description_language_en_name>\n<description_language_2_name><![CDATA[Цвет]]></description_language_2_name>\n<description_language_1_name><![CDATA[Colour]]></description_language_1_name>\n</object>\n</collection>', '<collection class="Catalog_Model_Collection_Image">\n</collection>', '<collection class="Catalog_Model_Collection_Brule">\n</collection>', NULL, 0, 0),
(433, 'dfghdfgh', NULL, NULL, '', '', 0, 1, 0, 123123.00, 0.00, 0.00, NULL, 1.00, NULL, '2010-09-14 14:02:24', 1, '2010-09-14 13:02:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'a:1:{i:0;s:1:"1";}', '<collection class="Catalog_Model_Collection_Attribute">\n</collection>', '<collection class="Catalog_Model_Collection_Image">\n</collection>', '<collection class="Catalog_Model_Collection_Brule">\n</collection>', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `item_bundle`
--

CREATE TABLE IF NOT EXISTS `item_bundle` (
  `bundle_id` int(11) NOT NULL AUTO_INCREMENT,
  `bundle_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bundle_item_id` int(11) NOT NULL,
  `bundle_status` tinyint(1) NOT NULL DEFAULT '1',
  `bundle_is_required` tinyint(1) NOT NULL DEFAULT '0',
  `bundle_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bundle_current_subitem_id` int(11) DEFAULT NULL,
  `bundle_current_subitem_price` decimal(12,2) DEFAULT NULL,
  `bundle_current_subitem_qty` decimal(12,2) DEFAULT NULL,
  `bundle_subitems_xml` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`bundle_id`),
  KEY `bundle_item_id` (`bundle_item_id`),
  KEY `bundle_code` (`bundle_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `item_bundle`
--


-- --------------------------------------------------------

--
-- Структура таблицы `item_bundle_subitem_ref`
--

CREATE TABLE IF NOT EXISTS `item_bundle_subitem_ref` (
  `bs_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `bs_ref_bundle_id` int(11) NOT NULL,
  `bs_ref_subitem_id` int(11) NOT NULL,
  `bs_ref_item_id` int(11) NOT NULL,
  PRIMARY KEY (`bs_ref_id`),
  KEY `bs_ref_bundle_id` (`bs_ref_bundle_id`,`bs_ref_subitem_id`),
  KEY `bs_ref_subitem_id` (`bs_ref_subitem_id`),
  KEY `bs_ref_subitem_id_2` (`bs_ref_subitem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `item_bundle_subitem_ref`
--


-- --------------------------------------------------------

--
-- Структура таблицы `item_description`
--

CREATE TABLE IF NOT EXISTS `item_description` (
  `item_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_desc_item_id` int(11) NOT NULL DEFAULT '0',
  `item_desc_language_id` int(11) NOT NULL DEFAULT '0',
  `item_desc_name` text COLLATE utf8_unicode_ci NOT NULL,
  `item_desc_manufacturer` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_desc_brief` text COLLATE utf8_unicode_ci,
  `item_desc_full` text COLLATE utf8_unicode_ci,
  `item_desc_more` text COLLATE utf8_unicode_ci,
  `item_desc_html_title` tinytext COLLATE utf8_unicode_ci,
  `item_desc_meta_keywords` text COLLATE utf8_unicode_ci,
  `item_desc_meta_description` text COLLATE utf8_unicode_ci,
  `item_desc_unit` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_desc_id`),
  UNIQUE KEY `item_desc_item_id` (`item_desc_item_id`,`item_desc_language_id`),
  KEY `item_desc_language_id` (`item_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1167 ;

--
-- Дамп данных таблицы `item_description`
--

INSERT INTO `item_description` (`item_desc_id`, `item_desc_item_id`, `item_desc_language_id`, `item_desc_name`, `item_desc_manufacturer`, `item_desc_brief`, `item_desc_full`, `item_desc_more`, `item_desc_html_title`, `item_desc_meta_keywords`, `item_desc_meta_description`, `item_desc_unit`) VALUES
(1163, 432, 2, 'Товар 1', NULL, '', '', '', '', '', '', ''),
(1164, 432, 1, 'Item 1', NULL, '', '', '', '', '', '', ''),
(1165, 433, 2, 'dfghdfgh', NULL, '', '', '', '', '', '', ''),
(1166, 433, 1, 'dfghdfgh', NULL, '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `item_site_ref`
--

CREATE TABLE IF NOT EXISTS `item_site_ref` (
  `is_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `is_ref_item_id` int(11) NOT NULL,
  `is_ref_site_id` int(11) NOT NULL,
  `is_ref_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`is_ref_id`),
  KEY `is_ref_item_id` (`is_ref_item_id`),
  KEY `is_ref_site_id` (`is_ref_site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

--
-- Дамп данных таблицы `item_site_ref`
--

INSERT INTO `item_site_ref` (`is_ref_id`, `is_ref_item_id`, `is_ref_site_id`, `is_ref_status`) VALUES
(12, 432, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `language_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_status` tinyint(1) NOT NULL DEFAULT '0',
  `language_sort` int(11) DEFAULT NULL,
  `language_is_default` tinyint(1) NOT NULL DEFAULT '0',
  `language_code2` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `language_code3` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `language_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`language_id`),
  UNIQUE KEY `language_code2` (`language_code2`),
  UNIQUE KEY `language_code3` (`language_code3`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `language`
--

INSERT INTO `language` (`language_id`, `language_status`, `language_sort`, `language_is_default`, `language_code2`, `language_code3`, `language_title`) VALUES
(1, 1, 3, 0, 'en', 'eng', 'English'),
(2, 1, 2, 1, 'ru', 'rus', 'Russian'),
(3, 0, 1, 0, 'ua', 'ukr', 'Ukrainian');

-- --------------------------------------------------------

--
-- Структура таблицы `mail`
--

CREATE TABLE IF NOT EXISTS `mail` (
  `mail_id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_date_sent` datetime NOT NULL,
  `mail_subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mail_body` text COLLATE utf8_unicode_ci,
  `mail_sender_id` int(11) NOT NULL,
  `mail_recipient_id` int(11) NOT NULL,
  `mail_status` tinyint(1) NOT NULL DEFAULT '0',
  `mail_talking` binary(32) DEFAULT NULL,
  `mail_parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`mail_id`),
  KEY `mail_recipient_id` (`mail_recipient_id`),
  KEY `mail_sender_id` (`mail_sender_id`),
  KEY `mail_status` (`mail_status`),
  KEY `mail_talking` (`mail_talking`),
  KEY `mail_parent_id` (`mail_parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `mail`
--

INSERT INTO `mail` (`mail_id`, `mail_date_sent`, `mail_subject`, `mail_body`, `mail_sender_id`, `mail_recipient_id`, `mail_status`, `mail_talking`, `mail_parent_id`) VALUES
(1, '0000-00-00 00:00:00', 'sadfsdgdsfg', 'dsfgdsfgdsfg', 1, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `manufacturer`
--

CREATE TABLE IF NOT EXISTS `manufacturer` (
  `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturer_adder_id` int(11) DEFAULT NULL,
  `manufacturer_changer_id` int(11) DEFAULT NULL,
  `manufacturer_date_added` timestamp NULL DEFAULT NULL,
  `manufacturer_date_changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`manufacturer_id`),
  KEY `manufacturer_adder_id` (`manufacturer_adder_id`),
  KEY `manufacturer_changer_id` (`manufacturer_changer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `manufacturer`
--


-- --------------------------------------------------------

--
-- Структура таблицы `manufacturer_description`
--

CREATE TABLE IF NOT EXISTS `manufacturer_description` (
  `manufacturer_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturer_desc_manufacturer_id` int(11) NOT NULL,
  `manufacturer_desc_language_id` int(11) NOT NULL,
  `manufacturer_desc_name` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manufacturer_desc_brief` varchar(10000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manufacturer_desc_full` text COLLATE utf8_unicode_ci,
  `manufacturer_desc_html_title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manufacturer_desc_meta_keywords` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manufacturer_desc_meta_description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`manufacturer_desc_id`),
  UNIQUE KEY `manufacturer_desc_manufacturer_id` (`manufacturer_desc_manufacturer_id`,`manufacturer_desc_language_id`),
  KEY `manufacturer_desc_language_id` (`manufacturer_desc_language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `manufacturer_description`
--


-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_seo_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `news_status` tinyint(1) NOT NULL,
  `news_ntopic_id` int(11) DEFAULT NULL,
  `news_rc_id` int(11) DEFAULT NULL,
  `news_date_publish` timestamp NULL DEFAULT NULL,
  `news_date_added` timestamp NULL DEFAULT NULL,
  `news_adder_id` int(11) DEFAULT NULL,
  `news_date_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `news_changer_id` int(11) DEFAULT NULL,
  `news_site_ids` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`news_id`),
  KEY `news_adder_id` (`news_adder_id`),
  KEY `news_changer_id` (`news_changer_id`),
  KEY `news_rc_id` (`news_rc_id`),
  KEY `news_seo_id` (`news_seo_id`),
  KEY `news_ntopic_id` (`news_ntopic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `news`
--


-- --------------------------------------------------------

--
-- Структура таблицы `news_description`
--

CREATE TABLE IF NOT EXISTS `news_description` (
  `news_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_desc_news_id` int(11) NOT NULL,
  `news_desc_language_id` int(11) NOT NULL,
  `news_desc_title` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `news_desc_announce` text COLLATE utf8_unicode_ci,
  `news_desc_full` text COLLATE utf8_unicode_ci,
  `news_desc_html_title` tinytext COLLATE utf8_unicode_ci,
  `news_desc_meta_keywords` text COLLATE utf8_unicode_ci,
  `news_desc_meta_description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`news_desc_id`),
  UNIQUE KEY `news_desc_news_id` (`news_desc_news_id`,`news_desc_language_id`),
  KEY `news_desc_language_id` (`news_desc_language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `news_description`
--


-- --------------------------------------------------------

--
-- Структура таблицы `news_site_ref`
--

CREATE TABLE IF NOT EXISTS `news_site_ref` (
  `ns_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `ns_ref_news_id` int(11) NOT NULL,
  `ns_ref_site_id` int(11) NOT NULL,
  `ns_ref_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ns_ref_id`),
  KEY `ns_ref_news_id` (`ns_ref_news_id`),
  KEY `ns_ref_site_id` (`ns_ref_site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `news_site_ref`
--


-- --------------------------------------------------------

--
-- Структура таблицы `news_topic`
--

CREATE TABLE IF NOT EXISTS `news_topic` (
  `ntopic_id` int(11) NOT NULL AUTO_INCREMENT,
  `ntopic_status` tinyint(1) NOT NULL,
  `ntopic_sort` int(11) DEFAULT NULL,
  `ntopic_date_added` timestamp NULL DEFAULT NULL,
  `ntopic_date_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ntopic_adder_id` int(11) DEFAULT NULL,
  `ntopic_changer_id` int(11) DEFAULT NULL,
  `ntopic_site_ids` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ntopic_id`),
  KEY `ntopic_adder_id` (`ntopic_adder_id`),
  KEY `ntopic_changer_id` (`ntopic_changer_id`),
  KEY `ntopic_sort` (`ntopic_sort`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `news_topic`
--

INSERT INTO `news_topic` (`ntopic_id`, `ntopic_status`, `ntopic_sort`, `ntopic_date_added`, `ntopic_date_changed`, `ntopic_adder_id`, `ntopic_changer_id`, `ntopic_site_ids`) VALUES
(1, 1, NULL, '2010-08-31 05:52:37', '2010-08-31 04:52:37', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `news_topic_description`
--

CREATE TABLE IF NOT EXISTS `news_topic_description` (
  `ntopic_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `ntopic_desc_ntopic_id` int(11) NOT NULL,
  `ntopic_desc_language_id` int(11) NOT NULL,
  `ntopic_desc_name` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `ntopic_desc_brief` text COLLATE utf8_unicode_ci NOT NULL,
  `ntopic_desc_full` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ntopic_desc_id`),
  UNIQUE KEY `ntopic_desc_ntopic_id` (`ntopic_desc_ntopic_id`,`ntopic_desc_language_id`),
  KEY `ntopic_desc_language_id` (`ntopic_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `news_topic_description`
--

INSERT INTO `news_topic_description` (`ntopic_desc_id`, `ntopic_desc_ntopic_id`, `ntopic_desc_language_id`, `ntopic_desc_name`, `ntopic_desc_brief`, `ntopic_desc_full`) VALUES
(1, 1, 2, 'sdfgsdfgsdfg', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `news_topic_site_ref`
--

CREATE TABLE IF NOT EXISTS `news_topic_site_ref` (
  `ns_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `ns_ref_ntopic_id` int(11) NOT NULL,
  `ns_ref_site_id` int(11) NOT NULL,
  `ns_ref_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ns_ref_id`),
  KEY `ns_ref_ntopic_id` (`ns_ref_ntopic_id`),
  KEY `ns_ref_site_id` (`ns_ref_site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `news_topic_site_ref`
--

INSERT INTO `news_topic_site_ref` (`ns_ref_id`, `ns_ref_ntopic_id`, `ns_ref_site_id`, `ns_ref_status`) VALUES
(1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `order`
--

CREATE TABLE IF NOT EXISTS `order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_status` tinyint(1) DEFAULT NULL,
  `order_adder_id` int(11) DEFAULT NULL,
  `order_changer_id` int(11) DEFAULT NULL,
  `order_date_added` timestamp NULL DEFAULT NULL,
  `order_date_changed` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `order_client_id` int(11) DEFAULT NULL,
  `order_currency` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_items_xml` text COLLATE utf8_unicode_ci,
  `order_brules_xml` text COLLATE utf8_unicode_ci,
  `order_shipment_xml` text COLLATE utf8_unicode_ci,
  `order_payment_xml` text COLLATE utf8_unicode_ci,
  `order_total` decimal(12,2) DEFAULT NULL,
  `order_site_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `order_adder_id` (`order_adder_id`),
  KEY `order_changer_id` (`order_changer_id`),
  KEY `order_client_id` (`order_client_id`),
  KEY `order_currency` (`order_currency`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `order`
--

INSERT INTO `order` (`order_id`, `order_status`, `order_adder_id`, `order_changer_id`, `order_date_added`, `order_date_changed`, `order_client_id`, `order_currency`, `order_items_xml`, `order_brules_xml`, `order_shipment_xml`, `order_payment_xml`, `order_total`, `order_site_id`) VALUES
(1, 2, 1, 1, '2010-09-10 05:44:58', '2010-09-10 05:44:59', 1, 'RUB', '<collection class="Checkout_Model_Collection_CartItem">\n<object class="Checkout_Model_Object_CartItem">\n<id><![CDATA[]]></id>\n<seo_id><![CDATA[tovar-1]]></seo_id>\n<hash><![CDATA[CartItem4c898d284a704]]></hash>\n<sku><![CDATA[]]></sku>\n<code><![CDATA[]]></code>\n<unit><![CDATA[]]></unit>\n<name><![CDATA[Товар 1]]></name>\n<brief><![CDATA[]]></brief>\n<full><![CDATA[]]></full>\n<date_added><![CDATA[2010-09-01 16:14:13]]></date_added>\n<date_changed><![CDATA[2010-09-10 05:42:39]]></date_changed>\n<rc_id><![CDATA[]]></rc_id>\n<rc_id_filename><![CDATA[]]></rc_id_filename>\n<rc_id_preview><![CDATA[]]></rc_id_preview>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n<attributes class="array"><![CDATA[a:2:{s:5:"color";s:10:"белый";s:4:"size";s:1:"0";}]]></attributes>\n<attributes_html><![CDATA[<strong>Размер</strong> : 0]]></attributes_html>\n<attributes_text><![CDATA[Размер : 0]]></attributes_text>\n<bundles class="array"><![CDATA[a:0:{}]]></bundles>\n<bundles_html><![CDATA[]]></bundles_html>\n<bundles_text><![CDATA[]]></bundles_text>\n<price><![CDATA[123]]></price>\n<qty><![CDATA[3]]></qty>\n<catalog_item_id><![CDATA[432]]></catalog_item_id>\n</object>\n</collection>', '<collection class="Checkout_Model_Collection_Brule">\n</collection>', '<object class="Checkout_Model_Object_Shipment">\n<method><![CDATA[]]></method>\n<is_shipped><![CDATA[]]></is_shipped>\n<date_shipped><![CDATA[]]></date_shipped>\n<reciever_name><![CDATA[]]></reciever_name>\n<reciever_address><![CDATA[]]></reciever_address>\n<reciever_requisite><![CDATA[]]></reciever_requisite>\n<addon_requisites><![CDATA[]]></addon_requisites>\n</object>\n', '<object class="Checkout_Model_Object_Payment">\n<method><![CDATA[]]></method>\n<is_payed><![CDATA[]]></is_payed>\n<date_payed><![CDATA[]]></date_payed>\n<payer_name><![CDATA[]]></payer_name>\n<payer_address><![CDATA[]]></payer_address>\n<payer_requisite><![CDATA[]]></payer_requisite>\n<addon_requisites><![CDATA[]]></addon_requisites>\n</object>\n', 369.00, NULL),
(2, 2, 1, 1, '2010-09-10 06:08:32', '2010-09-10 06:08:32', 1, 'RUB', '<collection class="Checkout_Model_Collection_CartItem">\n<object class="Checkout_Model_Object_CartItem">\n<id><![CDATA[]]></id>\n<seo_id><![CDATA[tovar-1]]></seo_id>\n<hash><![CDATA[CartItem4c899314506f6]]></hash>\n<sku><![CDATA[]]></sku>\n<code><![CDATA[]]></code>\n<unit><![CDATA[]]></unit>\n<name><![CDATA[Товар 1]]></name>\n<brief><![CDATA[]]></brief>\n<full><![CDATA[]]></full>\n<date_added><![CDATA[2010-09-01 16:14:13]]></date_added>\n<date_changed><![CDATA[2010-09-10 06:08:08]]></date_changed>\n<rc_id><![CDATA[]]></rc_id>\n<rc_id_filename><![CDATA[]]></rc_id_filename>\n<rc_id_preview><![CDATA[]]></rc_id_preview>\n<param1><![CDATA[]]></param1>\n<param2><![CDATA[]]></param2>\n<param3><![CDATA[]]></param3>\n<attributes class="array"><![CDATA[a:2:{s:5:"color";s:10:"белый";s:4:"size";s:2:"12";}]]></attributes>\n<attributes_html><![CDATA[<strong>Размер</strong> : 12]]></attributes_html>\n<attributes_text><![CDATA[Размер : 12]]></attributes_text>\n<bundles class="array"><![CDATA[a:0:{}]]></bundles>\n<bundles_html><![CDATA[]]></bundles_html>\n<bundles_text><![CDATA[]]></bundles_text>\n<price><![CDATA[123]]></price>\n<qty><![CDATA[1]]></qty>\n<catalog_item_id><![CDATA[432]]></catalog_item_id>\n</object>\n</collection>', '<collection class="Checkout_Model_Collection_Brule">\n</collection>', '<object class="Checkout_Model_Object_Shipment">\n<method><![CDATA[]]></method>\n<is_shipped><![CDATA[]]></is_shipped>\n<date_shipped><![CDATA[]]></date_shipped>\n<reciever_name><![CDATA[]]></reciever_name>\n<reciever_address><![CDATA[]]></reciever_address>\n<reciever_requisite><![CDATA[]]></reciever_requisite>\n<addon_requisites><![CDATA[]]></addon_requisites>\n</object>\n', '<object class="Checkout_Model_Object_Payment">\n<method><![CDATA[]]></method>\n<is_payed><![CDATA[]]></is_payed>\n<date_payed><![CDATA[]]></date_payed>\n<payer_name><![CDATA[]]></payer_name>\n<payer_address><![CDATA[]]></payer_address>\n<payer_requisite><![CDATA[]]></payer_requisite>\n<addon_requisites><![CDATA[]]></addon_requisites>\n</object>\n', 123.00, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `order_history`
--

CREATE TABLE IF NOT EXISTS `order_history` (
  `oh_id` int(11) NOT NULL AUTO_INCREMENT,
  `oh_order_id` int(11) NOT NULL,
  `oh_order_status` tinyint(1) DEFAULT NULL,
  `oh_order_adder_id` int(11) DEFAULT NULL,
  `oh_order_changer_id` int(11) DEFAULT NULL,
  `oh_order_date_added` timestamp NULL DEFAULT NULL,
  `oh_order_date_changed` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `oh_order_client_id` int(11) DEFAULT NULL,
  `oh_order_currency` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `oh_order_items_xml` text COLLATE utf8_unicode_ci,
  `oh_order_brules_xml` text COLLATE utf8_unicode_ci,
  `oh_order_shipment_xml` text COLLATE utf8_unicode_ci,
  `oh_order_payment_xml` text COLLATE utf8_unicode_ci,
  `oh_order_total` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`oh_id`),
  KEY `oh_order_adder_id` (`oh_order_adder_id`),
  KEY `oh_order_changer_id` (`oh_order_changer_id`),
  KEY `oh_order_client_id` (`oh_order_client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `order_history`
--


-- --------------------------------------------------------

--
-- Структура таблицы `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_seo_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_status` tinyint(1) NOT NULL,
  `page_sort` int(11) DEFAULT NULL,
  `page_driver` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_rc_id` int(11) DEFAULT NULL,
  `page_date_added` timestamp NULL DEFAULT NULL,
  `page_date_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `page_adder_id` int(11) DEFAULT NULL,
  `page_changer_id` int(11) DEFAULT NULL,
  `page_double_column` tinyint(1) DEFAULT '0',
  `page_flag1` tinyint(1) DEFAULT '0',
  `page_flag2` smallint(1) DEFAULT '0',
  `page_site_ids` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`page_id`),
  KEY `page_seo_id` (`page_seo_id`),
  KEY `page_adder_id` (`page_adder_id`,`page_changer_id`),
  KEY `page_changer_id` (`page_changer_id`),
  KEY `page_sort` (`page_sort`),
  KEY `page_code` (`page_code`),
  KEY `page_flag1` (`page_flag1`),
  KEY `page_rc_id` (`page_rc_id`),
  KEY `page_flag2` (`page_flag2`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

--
-- Дамп данных таблицы `page`
--

INSERT INTO `page` (`page_id`, `page_code`, `page_seo_id`, `page_status`, `page_sort`, `page_driver`, `page_rc_id`, `page_date_added`, `page_date_changed`, `page_adder_id`, `page_changer_id`, `page_double_column`, `page_flag1`, `page_flag2`, `page_site_ids`) VALUES
(1, NULL, 'terms-of-use', 1, 6, '0', NULL, '2009-09-22 11:30:03', '2010-08-17 13:54:31', NULL, NULL, 0, 0, 0, NULL),
(2, NULL, 'privacy', 1, 6, '0', NULL, '2009-09-22 12:17:07', '2010-08-17 13:54:31', NULL, 1, 0, 0, 0, NULL),
(4, 'about', 'about', 1, NULL, '0', NULL, '2010-05-10 13:48:53', '2010-08-30 00:40:02', 1, 1, 0, 0, 0, 'a:1:{i:0;s:1:"1";}'),
(5, 'contacts', 'conact-us', 1, NULL, '0', NULL, NULL, '2010-08-18 06:56:50', 1, 1, 0, 0, 1, NULL),
(6, 'delivery', 'delivery', 1, NULL, '0', NULL, NULL, '2010-08-18 06:57:03', 1, 1, 0, 0, 1, NULL),
(7, 'other-projects', 'drugie-nashi-proekty', 0, 7, '0', NULL, NULL, '2010-08-17 14:44:43', 1, 1, 0, 0, 0, NULL),
(8, 'vending-info', 'vending-info', 0, 8, '0', NULL, NULL, '2010-08-17 14:39:52', 1, 1, 0, 0, 0, NULL),
(10, '', 'free-shipping', 1, 0, '0', NULL, NULL, '2010-08-17 14:45:17', 1, 1, 0, 1, 0, NULL),
(11, '', 'official-distributors', 1, 1, '0', NULL, NULL, '2010-08-17 14:45:48', 1, 1, 0, 1, 0, NULL),
(12, '', 'nalichie-zapchastej-na-sklade', 1, 2, '0', NULL, NULL, '2010-08-17 14:46:00', 1, 1, 0, 1, 0, NULL),
(13, '', 'bystraja-otgruzka-gruza-do-tr.kompanii', 1, 3, '0', NULL, NULL, '2010-08-17 14:46:00', 1, 1, 0, 1, 0, NULL),
(14, '', 'online-help', 1, 4, '0', NULL, NULL, '2010-08-17 14:45:57', 1, 1, 0, 1, 0, NULL),
(15, '', 'online-service', 1, 5, '0', NULL, NULL, '2010-08-17 14:45:54', 1, 1, 0, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `page_description`
--

CREATE TABLE IF NOT EXISTS `page_description` (
  `page_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_desc_page_id` int(11) NOT NULL,
  `page_desc_language_id` int(11) NOT NULL,
  `page_desc_title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_desc_brief` text COLLATE utf8_unicode_ci,
  `page_desc_full` text COLLATE utf8_unicode_ci,
  `page_desc_full2` text COLLATE utf8_unicode_ci,
  `page_desc_html_title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_desc_meta_description` text COLLATE utf8_unicode_ci,
  `page_desc_meta_keywords` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`page_desc_id`),
  UNIQUE KEY `page_desc_page_id` (`page_desc_page_id`,`page_desc_language_id`),
  KEY `page_desc_language_id` (`page_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

--
-- Дамп данных таблицы `page_description`
--

INSERT INTO `page_description` (`page_desc_id`, `page_desc_page_id`, `page_desc_language_id`, `page_desc_title`, `page_desc_brief`, `page_desc_full`, `page_desc_full2`, `page_desc_html_title`, `page_desc_meta_description`, `page_desc_meta_keywords`) VALUES
(1, 1, 1, 'Terms of use', '', '', NULL, '', '', ''),
(2, 2, 1, 'Privacy', '', '', NULL, '', '', ''),
(3, 1, 2, 'Условия', '', '', NULL, '', '', ''),
(4, 2, 2, 'Приватность', '', '<p>\r\n	&nbsp;</p>\r\n<p>\r\n	wertwertwertцукецукеуцкеуцкеуцке<img alt="" src="/uploads/ckfinder/images/ajax-loader(2).gif" style="width: 32px; height: 32px;" /></p>\r\n', NULL, NULL, NULL, NULL),
(5, 4, 2, 'Скидки', '', '<p>\r\n	Постоянным клиентам предоставляются скидки</p>\r\n<p>\r\n	Предлагаем &nbsp;гибкую систему скидок</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	А) Базовая цена, действует при покупке до 5 автоматов, до 10.000 рублей наполнителя</p>\r\n<p>\r\n	Б) Оптовые цены действуют при покупке больше 5 автоматов и выше 10.000 рублей по&nbsp; наполнителю</p>\r\n<p>\r\n	В)&nbsp;Дистрибьютерские цены действуют, если заключен договор на покупку определенного количества автоматов в течение определенного времени</p>\r\n<p>\r\n	Г)&nbsp;Дополнительные скидки могут предоставляться,&nbsp;если наши цены окажутся ниже конкурента на аналогичный товар</p>\r\n<p>\r\n	Д) Скидки могут быть во время акции, сообщаются отдельно на веб представительстве или если вы подписались в системе</p>\r\n<p>\r\n	Е) Дополнительную скидку вы можете получить,&nbsp;если ответите на некоторые вопросы</p>\r\n', NULL, NULL, NULL, NULL),
(6, 5, 2, 'Контакты', '', '<p>\r\n	Наши контакты:</p>\r\n<p>\r\n	&nbsp;</p>\r\n<div>\r\n	127238, Москва, Ильменский проезд, дом 5, офис 201</div>\r\n<div>\r\n	<span>Тел/факс:</span> (495) 921-38-05 &nbsp; <span>E-mail:</span> info@gumballs.ru</div>\r\n', NULL, NULL, NULL, NULL),
(7, 6, 2, 'Доставка', '', '', NULL, NULL, NULL, NULL),
(8, 7, 2, 'Другие наши проекты', '', '<div class="proj-item">\r\n	<div class="image-link">\r\n		<a href="http://rusgum.ru"><img border="0" height="39" src="/skins/infosite/images/body/projects-images/num_1.gif" width="48" /></a></div>\r\n	<div class="url-link">\r\n		<a class="site" href="http://rusgum.ru">rusgum.ru</a></div>\r\n</div>\r\n<div class="proj-item">\r\n	<div class="image-link">\r\n		<a href="http://condom.ru"><img border="0" height="39" src="/skins/infosite/images/body/projects-images/num_2.gif" width="40" /></a></div>\r\n	<div class="url-link">\r\n		<a href="http://condom.ru">condom.ru</a></div>\r\n</div>\r\n<div class="proj-item">\r\n	<div class="image-link">\r\n		<a href="http://stickershop.ru"><img border="0" height="39" src="/skins/infosite/images/body/projects-images/num_3.gif" width="39" /></a></div>\r\n	<div class="url-link">\r\n		<a href="http://stickershop.ru">stickershop.ru</a></div>\r\n</div>\r\n<div class="proj-item">\r\n	<div class="image-link">\r\n		<a href="http://capsule.ru"><img border="0" height="39" src="/skins/infosite/images/body/projects-images/num_4.gif" width="48" /></a></div>\r\n	<div class="url-link">\r\n		<a href="http://capsule.ru">capsule.ru</a></div>\r\n</div>\r\n<div class="proj-item">\r\n	<div class="image-link">\r\n		<a href="http://toys4u.ru"><img border="0" height="39" src="/skins/infosite/images/body/projects-images/num_5.gif" width="38" /></a></div>\r\n	<div class="url-link">\r\n		<a href="http://toys4u.ru">toys4u.ru</a></div>\r\n</div>\r\n<div class="proj-item">\r\n	<div class="image-link">\r\n		<a href="http://vendingbook.ru"><img border="0" height="39" src="/skins/infosite/images/body/projects-images/num_6.gif" width="39" /></a></div>\r\n	<div class="url-link">\r\n		<a href="http://vendingbook.ru">vendingbook.ru</a></div>\r\n</div>\r\n<div class="proj-item-last">\r\n	<div class="image-link">\r\n		<a href="http://beaver.ru"><img border="0" height="39" src="/skins/infosite/images/body/projects-images/num_7.gif" width="39" /></a></div>\r\n	<div class="url-link">\r\n		<a href="http://beaver.ru">beaver.ru</a></div>\r\n</div>\r\n', NULL, NULL, NULL, NULL),
(9, 8, 2, 'О вендинге', '', '<p>\r\n	<span class="blau-big"><strong><span class="blau-big">Вендинг-бизнес</span></strong></span> (Vending англ. &mdash; продажа товаров через торговые автоматы) &mdash; это одно из молодых и несомненно перспективных направлений бизнеса, которое приносит стабильно высокую прибыль вендинг-операторам, при этом не требуя больших капиталовложений.</p>\r\n<p>\r\n	Наполнители у торговых автоматов могут быть самыми разными: жевательная резинка, конфеты, мячи прыгуны, игрушки или бахилы в капсулах и т.д. Практичность, привлекательность и работоспособность торговых автоматов залог эффективного, прибыльного вендинг-бизнеса.</p>\r\n<p>\r\n	Мы предлагаем только качественные торговые автоматы и комплектующие, зарекомендовавшие себя, как машины способные проработать десятки лет, а значит, и окупить себя десятки раз.</p>\r\n<p>\r\n	Установкой и продажей торговых автоматов наша компания занимается с 1998 года. За это время мы осуществили множество идей и сделали своеобразный прорыв в вендинг-бизнесе в России. Одними из первых предложили автоматы в аренду на условиях последующего выкупа</p>\r\n', NULL, NULL, NULL, NULL),
(11, 10, 2, 'Бесплатная доставка по Москве', 'Заказав товары на сумму не менее 25.000 руб.', '', NULL, NULL, NULL, NULL),
(12, 11, 2, 'Мы официальные дистрибьюторы', 'Без посредников - производитель поставщик оператор', '', NULL, NULL, NULL, NULL),
(13, 12, 2, 'Наличие запчастей на складе', 'Если нет на складе, быстро доставим от производителя', '', NULL, NULL, NULL, NULL),
(14, 13, 2, 'Быстрая отгрузка груза до тр.компании', 'Ближайшие транспортные компании к складу', '', NULL, NULL, NULL, NULL),
(15, 14, 2, 'Онлайн помощь начинающим операторам', 'Бесплатные консультации от наших экспертов', '', NULL, NULL, NULL, NULL),
(16, 15, 2, 'Онлайн Сервис', 'Вы можете наблюдать за статусом отправки заказанного груза 24 часа в сутки', '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `page_site_ref`
--

CREATE TABLE IF NOT EXISTS `page_site_ref` (
  `ps_ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `ps_ref_page_id` int(11) NOT NULL,
  `ps_ref_site_id` int(11) NOT NULL,
  `ps_ref_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ps_ref_id`),
  KEY `ps_ref_page_id` (`ps_ref_page_id`),
  KEY `ps_ref_site_id` (`ps_ref_site_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `page_site_ref`
--

INSERT INTO `page_site_ref` (`ps_ref_id`, `ps_ref_page_id`, `ps_ref_site_id`, `ps_ref_status`) VALUES
(3, 4, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `preorder`
--

CREATE TABLE IF NOT EXISTS `preorder` (
  `preorder_id` int(11) NOT NULL AUTO_INCREMENT,
  `preorder_status` tinyint(1) DEFAULT NULL,
  `preorder_adder_id` int(11) DEFAULT NULL,
  `preorder_changer_id` int(11) DEFAULT NULL,
  `preorder_date_added` timestamp NULL DEFAULT NULL,
  `preorder_date_changed` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `preorder_client_id` int(11) DEFAULT NULL,
  `preorder_currency` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `preorder_items_xml` text COLLATE utf8_unicode_ci,
  `preorder_brules_xml` text COLLATE utf8_unicode_ci,
  `preorder_shipment_xml` text COLLATE utf8_unicode_ci,
  `preorder_payment_xml` text COLLATE utf8_unicode_ci,
  `preorder_total` decimal(12,2) DEFAULT NULL,
  `preorder_site_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`preorder_id`),
  KEY `preorder_adder_id` (`preorder_adder_id`),
  KEY `preorder_changer_id` (`preorder_changer_id`),
  KEY `preorder_client_id` (`preorder_client_id`),
  KEY `preorder_currency` (`preorder_currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `preorder`
--


-- --------------------------------------------------------

--
-- Структура таблицы `resource`
--

CREATE TABLE IF NOT EXISTS `resource` (
  `rc_id` int(11) NOT NULL AUTO_INCREMENT,
  `rc_filename` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `rc_source_filename` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rc_source_url` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rc_preview` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rc_mime` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `rc_width` smallint(6) DEFAULT NULL,
  `rc_height` smallint(6) DEFAULT NULL,
  `rc_prv_width` smallint(6) DEFAULT NULL,
  `rc_prv_height` smallint(6) DEFAULT NULL,
  `rc_preview2` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rc_prv2_width` smallint(6) DEFAULT NULL,
  `rc_prv2_height` smallint(6) DEFAULT NULL,
  `rc_preview3` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rc_prv3_width` smallint(6) DEFAULT NULL,
  `rc_prv3_height` smallint(6) DEFAULT NULL,
  `rc_preview4` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rc_prv4_width` smallint(6) DEFAULT NULL,
  `rc_prv4_height` smallint(6) DEFAULT NULL,
  `rc_size` bigint(20) DEFAULT NULL,
  `rc_date_added` timestamp NULL DEFAULT NULL,
  `rc_adder_id` int(11) DEFAULT NULL,
  `rc_date_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rc_changer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`rc_id`),
  KEY `rc_adder_id` (`rc_adder_id`),
  KEY `rc_changer_id` (`rc_changer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `resource`
--

INSERT INTO `resource` (`rc_id`, `rc_filename`, `rc_source_filename`, `rc_source_url`, `rc_preview`, `rc_mime`, `rc_width`, `rc_height`, `rc_prv_width`, `rc_prv_height`, `rc_preview2`, `rc_prv2_width`, `rc_prv2_height`, `rc_preview3`, `rc_prv3_width`, `rc_prv3_height`, `rc_preview4`, `rc_prv4_width`, `rc_prv4_height`, `rc_size`, `rc_date_added`, `rc_adder_id`, `rc_date_changed`, `rc_changer_id`) VALUES
(1, 'ca12a8a17587d5ddd5b5f84452a78f49.jpg', 'img_details1.jpg', NULL, 'prv_ca12a8a17587d5ddd5b5f84452a78f49.jpg', 'application/octet-stream', 380, 326, 60, 51, 'prv2_ca12a8a17587d5ddd5b5f84452a78f49.jpg', 200, 172, 'prv3_ca12a8a17587d5ddd5b5f84452a78f49.jpg', 120, 103, 'prv4_ca12a8a17587d5ddd5b5f84452a78f49.jpg', 110, 94, 80372, NULL, NULL, '2010-09-14 13:08:04', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_status` tinyint(1) NOT NULL DEFAULT '0',
  `role_sort` int(11) DEFAULT NULL,
  `role_rc_id` int(11) DEFAULT NULL,
  `role_acl_role` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role_param1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  KEY `role_acl_role` (`role_acl_role`),
  KEY `role_sort` (`role_sort`),
  KEY `role_rc_id` (`role_rc_id`),
  KEY `role_param1` (`role_param1`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `role`
--

INSERT INTO `role` (`role_id`, `role_status`, `role_sort`, `role_rc_id`, `role_acl_role`, `role_param1`) VALUES
(1, 1, 5, 25, 'admin', NULL),
(4, 1, 0, NULL, 'client', NULL),
(5, 1, 1, NULL, 'manager', NULL),
(6, 1, 2, NULL, 'keeper', NULL),
(7, 1, 3, NULL, 'editor', NULL),
(8, 1, 4, NULL, 'director', NULL),
(10, 1, NULL, NULL, 'dealer', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `role_description`
--

CREATE TABLE IF NOT EXISTS `role_description` (
  `role_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_desc_role_id` int(11) NOT NULL,
  `role_desc_language_id` int(11) NOT NULL,
  `role_desc_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role_desc_brief` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`role_desc_id`),
  UNIQUE KEY `role_desc_role_id_2` (`role_desc_role_id`,`role_desc_language_id`),
  KEY `role_desc_role_id` (`role_desc_role_id`),
  KEY `role_desc_language_id` (`role_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;

--
-- Дамп данных таблицы `role_description`
--

INSERT INTO `role_description` (`role_desc_id`, `role_desc_role_id`, `role_desc_language_id`, `role_desc_name`, `role_desc_brief`) VALUES
(1, 1, 1, 'Admin', ''),
(2, 1, 2, 'Администратор', ''),
(7, 4, 1, 'Client', ''),
(8, 4, 2, 'Клиент', ''),
(9, 5, 1, 'Manager', ''),
(10, 5, 2, 'Менеджер', ''),
(11, 6, 1, 'Keeper', ''),
(12, 6, 2, 'Кладовщик', ''),
(13, 7, 1, 'Editor', ''),
(14, 7, 2, 'Редактор', ''),
(15, 8, 1, 'Director', ''),
(16, 8, 2, 'Директор', ''),
(18, 10, 2, 'Дилер', '');

-- --------------------------------------------------------

--
-- Структура таблицы `site`
--

CREATE TABLE IF NOT EXISTS `site` (
  `site_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_status` tinyint(1) NOT NULL,
  `site_host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `site_base_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `site_vertical_id` int(11) DEFAULT NULL,
  `site_date_added` timestamp NULL DEFAULT NULL,
  `site_date_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `site_is_linked_by_default` tinyint(1) DEFAULT NULL,
  `site_default_language_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`site_id`),
  KEY `site_vertical_id` (`site_vertical_id`),
  KEY `site_default_language_id` (`site_default_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `site`
--

INSERT INTO `site` (`site_id`, `site_status`, `site_host`, `site_base_url`, `site_vertical_id`, `site_date_added`, `site_date_changed`, `site_is_linked_by_default`, `site_default_language_id`) VALUES
(1, 1, 'cutecms.job', '', 1, '2010-09-23 20:21:37', '2010-09-23 20:24:46', 1, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `site_description`
--

CREATE TABLE IF NOT EXISTS `site_description` (
  `site_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_desc_site_id` int(11) NOT NULL,
  `site_desc_language_id` int(11) NOT NULL,
  `site_desc_title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `site_desc_brief` text COLLATE utf8_unicode_ci,
  `site_desc_full` text COLLATE utf8_unicode_ci,
  `site_desc_html_title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `site_desc_meta_description` text COLLATE utf8_unicode_ci,
  `site_desc_meta_keywords` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`site_desc_id`),
  UNIQUE KEY `site_desc_site_id` (`site_desc_site_id`,`site_desc_language_id`),
  KEY `site_desc_language_id` (`site_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `site_description`
--

INSERT INTO `site_description` (`site_desc_id`, `site_desc_site_id`, `site_desc_language_id`, `site_desc_title`, `site_desc_brief`, `site_desc_full`, `site_desc_html_title`, `site_desc_meta_description`, `site_desc_meta_keywords`) VALUES
(1, 1, 1, 'Shop', NULL, NULL, NULL, NULL, NULL),
(4, 1, 2, 'Магазин', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `tip`
--

CREATE TABLE IF NOT EXISTS `tip` (
  `tip_id` int(11) NOT NULL AUTO_INCREMENT,
  `tip_status` tinyint(1) DEFAULT '1',
  `tip_destination` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tip_role` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tip_adder_id` int(11) DEFAULT NULL,
  `tip_date_added` timestamp NULL DEFAULT NULL,
  `tip_changer_id` int(11) DEFAULT NULL,
  `tip_date_changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tip_id`),
  KEY `tip_changer_id` (`tip_changer_id`),
  KEY `tip_adder_id` (`tip_adder_id`),
  KEY `tip_roles` (`tip_role`),
  KEY `tip_destination` (`tip_destination`),
  KEY `tip_destination_2` (`tip_destination`,`tip_role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `tip`
--

INSERT INTO `tip` (`tip_id`, `tip_status`, `tip_destination`, `tip_role`, `tip_adder_id`, `tip_date_added`, `tip_changer_id`, `tip_date_changed`) VALUES
(2, 0, 'tips', 'director', 1, '2010-03-03 13:33:23', 1, '2010-03-03 14:13:36'),
(3, 1, 'tips', 'director', 1, '2010-03-03 14:14:13', 1, '2010-03-03 14:15:38');

-- --------------------------------------------------------

--
-- Структура таблицы `tip_description`
--

CREATE TABLE IF NOT EXISTS `tip_description` (
  `tip_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `tip_desc_tip_id` int(11) NOT NULL,
  `tip_desc_language_id` int(11) NOT NULL,
  `tip_desc_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tip_desc_text` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`tip_desc_id`),
  UNIQUE KEY `tip_desc_tip_id_2` (`tip_desc_tip_id`,`tip_desc_language_id`),
  KEY `tip_desc_tip_id` (`tip_desc_tip_id`),
  KEY `tip_desc_language_id` (`tip_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `tip_description`
--

INSERT INTO `tip_description` (`tip_desc_id`, `tip_desc_tip_id`, `tip_desc_language_id`, `tip_desc_title`, `tip_desc_text`) VALUES
(2, 2, 2, 'Это раздел подсказок', 'Здесь можно написать подсказки для всех разделов  всех групп пользователей'),
(3, 3, 2, 'Новая', 'пывапывапывапывап \r\nвап\r\nрва\r\nпр\r\nвапр\r\nва\r\nпрвапрп');

-- --------------------------------------------------------

--
-- Структура таблицы `transport_price`
--

CREATE TABLE IF NOT EXISTS `transport_price` (
  `tp_id` int(11) NOT NULL AUTO_INCREMENT,
  `tp_city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tp_city_normalized` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tp_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tp_phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tp_transport_guid` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tp_transport_id` int(11) DEFAULT NULL,
  `tp_min_price` float(12,4) DEFAULT NULL,
  `tp_price1` float(12,4) DEFAULT NULL,
  `tp_price2` float(12,4) DEFAULT NULL,
  `tp_price3` float(12,4) DEFAULT NULL,
  `tp_price4` float(12,4) DEFAULT NULL,
  `tp_price5` float(12,4) DEFAULT NULL,
  `tp_weight1` float(12,4) DEFAULT NULL,
  `tp_weight2` float(12,4) DEFAULT NULL,
  `tp_weight3` float(12,4) DEFAULT NULL,
  `tp_weight4` float(12,4) DEFAULT NULL,
  `tp_weight5` float(12,4) DEFAULT NULL,
  PRIMARY KEY (`tp_id`),
  KEY `tp_city_normalized` (`tp_city_normalized`,`tp_transport_id`),
  KEY `tp_transport_id` (`tp_transport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `transport_price`
--


-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_status` tinyint(1) NOT NULL DEFAULT '0',
  `user_sort` int(11) DEFAULT NULL,
  `user_export` tinyint(1) NOT NULL DEFAULT '1',
  `user_login` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_binding` binary(32) DEFAULT NULL,
  `user_password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_dob` timestamp NULL DEFAULT NULL,
  `user_rc_id` int(11) DEFAULT NULL,
  `user_role_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_last_login` timestamp NULL DEFAULT NULL,
  `user_login_count` int(11) DEFAULT '0',
  `user_date_added` timestamp NULL DEFAULT NULL,
  `user_date_changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_adder_id` int(11) DEFAULT NULL,
  `user_changer_id` int(11) DEFAULT NULL,
  `user_rows_per_page` text COLLATE utf8_unicode_ci,
  `user_binded_count` int(11) DEFAULT NULL,
  `user_tel` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_address` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_login` (`user_login`),
  KEY `user_changer_id` (`user_changer_id`),
  KEY `user_adder_id` (`user_adder_id`),
  KEY `user_role_id` (`user_role_id`),
  KEY `user_rc_id` (`user_rc_id`),
  KEY `user_email` (`user_email`),
  KEY `user_binding` (`user_binding`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`user_id`, `user_status`, `user_sort`, `user_export`, `user_login`, `user_binding`, `user_password`, `user_email`, `user_dob`, `user_rc_id`, `user_role_id`, `user_name`, `user_last_login`, `user_login_count`, `user_date_added`, `user_date_changed`, `user_adder_id`, `user_changer_id`, `user_rows_per_page`, `user_binded_count`, `user_tel`, `user_address`) VALUES
(1, 1, 0, 1, 'admin', '123123\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 'e10adc3949ba59abbe56e057f20f883e', 'support@cutecms.ru', '2010-12-21 09:12:42', NULL, 1, 'Admin', '2010-09-23 20:22:34', 467, NULL, '2009-09-21 01:39:02', NULL, NULL, 'a:6:{s:28:"tickets_admin-document_index";s:2:"50";s:24:"tickets_admin-user_index";s:2:"50";s:24:"tickets_stats_user-login";s:2:"20";s:26:"tickets_admin-document_job";s:2:"20";s:22:"tickets_stats_shipment";s:2:"50";s:25:"tickets_stats_weight-size";s:2:"10";}', 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `user_discount_fromform`
--

CREATE TABLE IF NOT EXISTS `user_discount_fromform` (
  `udff_id` int(11) NOT NULL AUTO_INCREMENT,
  `udff_user_id` int(11) NOT NULL,
  `udff_percent` float(12,2) DEFAULT NULL,
  `udff_data` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`udff_id`),
  KEY `udff_user_id` (`udff_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `user_discount_fromform`
--


-- --------------------------------------------------------

--
-- Структура таблицы `user_news_subscription`
--

CREATE TABLE IF NOT EXISTS `user_news_subscription` (
  `uns_id` int(11) NOT NULL AUTO_INCREMENT,
  `uns_user_id` int(11) NOT NULL,
  `uns_ntopic_id` int(11) NOT NULL,
  PRIMARY KEY (`uns_id`),
  KEY `uns_user_id` (`uns_user_id`),
  KEY `uns_ntopic_id` (`uns_ntopic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `user_news_subscription`
--


-- --------------------------------------------------------

--
-- Структура таблицы `user_news_subscription_log`
--

CREATE TABLE IF NOT EXISTS `user_news_subscription_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_user_id` int(11) NOT NULL,
  `log_ntopic_id` int(11) NOT NULL,
  `log_action` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `log_user_id` (`log_user_id`),
  KEY `log_ntopic_id` (`log_ntopic_id`),
  KEY `log_action` (`log_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `user_news_subscription_log`
--


-- --------------------------------------------------------

--
-- Структура таблицы `user_role`
--

CREATE TABLE IF NOT EXISTS `user_role` (
  `ur_id` int(11) NOT NULL AUTO_INCREMENT,
  `ur_user_id` int(11) NOT NULL,
  `ur_role_id` int(11) NOT NULL,
  PRIMARY KEY (`ur_id`),
  KEY `ur_user_id` (`ur_user_id`),
  KEY `ur_role_id` (`ur_role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=41 ;

--
-- Дамп данных таблицы `user_role`
--

INSERT INTO `user_role` (`ur_id`, `ur_user_id`, `ur_role_id`) VALUES
(36, 1, 4),
(37, 1, 5),
(38, 1, 6),
(39, 1, 7),
(40, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `user_stats`
--

CREATE TABLE IF NOT EXISTS `user_stats` (
  `stats_id` int(11) NOT NULL AUTO_INCREMENT,
  `stats_user_id` int(11) NOT NULL,
  `stats_action` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stats_date_added` datetime DEFAULT NULL,
  PRIMARY KEY (`stats_id`),
  KEY `stats_user_id` (`stats_user_id`),
  KEY `stats_action` (`stats_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `user_stats`
--


-- --------------------------------------------------------

--
-- Структура таблицы `vertical`
--

CREATE TABLE IF NOT EXISTS `vertical` (
  `vertical_id` int(1) NOT NULL AUTO_INCREMENT,
  `vertical_status` tinyint(1) NOT NULL,
  `vertical_skin` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vertical_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vertical_date_added` timestamp NULL DEFAULT NULL,
  `vertical_date_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vertical_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `vertical`
--

INSERT INTO `vertical` (`vertical_id`, `vertical_status`, `vertical_skin`, `vertical_title`, `vertical_date_added`, `vertical_date_changed`) VALUES
(1, 1, 'shop', 'vertical1', '2010-08-19 16:27:34', '2010-08-19 16:27:34'),
(2, 1, 'infosite', 'vertical2', '2010-06-02 12:28:05', '2010-06-02 12:28:05'),
(3, 1, 'shop', 'vertical3', '2010-06-02 12:28:37', '2010-06-02 12:28:37'),
(4, 1, 'toys4u', 'vertical4', '2010-08-10 16:18:25', '2010-08-10 16:18:25');

-- --------------------------------------------------------

--
-- Структура таблицы `video`
--

CREATE TABLE IF NOT EXISTS `video` (
  `video_id` int(11) NOT NULL AUTO_INCREMENT,
  `video_status` tinyint(1) DEFAULT NULL,
  `video_sort` int(11) DEFAULT '0',
  `video_seo_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `video_code` text COLLATE utf8_unicode_ci,
  `video_rc_id` int(11) DEFAULT NULL,
  `video_source_url` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `video_date_added` timestamp NULL DEFAULT NULL,
  `video_date_changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `video_adder_id` int(11) DEFAULT NULL,
  `video_changer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`video_id`),
  KEY `video_seo_id` (`video_seo_id`),
  KEY `video_adder_id` (`video_adder_id`),
  KEY `video_changer_id` (`video_changer_id`),
  KEY `video_rc_id` (`video_rc_id`),
  KEY `video_sort` (`video_sort`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Дамп данных таблицы `video`
--

INSERT INTO `video` (`video_id`, `video_status`, `video_sort`, `video_seo_id`, `video_code`, `video_rc_id`, `video_source_url`, `video_date_added`, `video_date_changed`, `video_adder_id`, `video_changer_id`) VALUES
(1, 1, 2, 'semi-precious-', '<object  width="380" height="278"><param name="movie" value="http://www.youtube.com/v/EGh9zlN6eLo?fs=1&amp;hl=ru_RU"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/EGh9zlN6eLo?fs=1&amp;hl=ru_RU" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="380" height="278"></embed></object>', NULL, 'http://www.youtube.com/watch?v=EGh9zlN6eLo&playnext=1&videos=qqTl42BaIsQ&feature=featured', '2010-08-17 08:49:31', '2010-08-17 10:55:17', 1, 1),
(4, 1, 3, 'outside-lands-2010-wolfmother-rock-out', '<object  width="380" height="278"><param name="movie" value="http://www.youtube.com/v/gSq-hdNYXNg?fs=1&amp;hl=ru_RU"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/gSq-hdNYXNg?fs=1&amp;hl=ru_RU" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="380" height="278"></embed></object>', NULL, '', '2010-08-17 09:08:05', '2010-08-17 10:55:29', 1, 1),
(10, 1, 1, 'dizzee-rascal-performs-bonkers-at-glastonbury-2010-', '<object  width="380" height="278"><param name="movie" value="http://www.youtube.com/v/Ln-qWQcgE9M?fs=1&amp;hl=ru_RU"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/Ln-qWQcgE9M?fs=1&amp;hl=ru_RU" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="380" height="278"></embed></object>', NULL, '', '2010-08-17 10:10:36', '2010-08-17 10:54:15', 1, 1),
(11, 1, 0, 'newport-folk-festival-2010-andrew-bird-and-calexico-', '<object width="380" height="278"><param name="movie" value="http://www.youtube.com/v/kk3TIio1-Uw?fs=1&amp;hl=ru_RU"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/kk3TIio1-Uw?fs=1&amp;hl=ru_RU" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="380" height="278"></embed></object>', NULL, '', '2010-08-17 10:11:05', '2010-08-17 10:54:04', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `video_description`
--

CREATE TABLE IF NOT EXISTS `video_description` (
  `video_desc_id` int(11) NOT NULL AUTO_INCREMENT,
  `video_desc_video_id` int(11) NOT NULL,
  `video_desc_language_id` int(11) NOT NULL,
  `video_desc_title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `video_desc_brief` text COLLATE utf8_unicode_ci,
  `video_desc_full` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`video_desc_id`),
  UNIQUE KEY `video_desc_video_id` (`video_desc_video_id`,`video_desc_language_id`),
  KEY `video_desc_language_id` (`video_desc_language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `video_description`
--

INSERT INTO `video_description` (`video_desc_id`, `video_desc_video_id`, `video_desc_language_id`, `video_desc_title`, `video_desc_brief`, `video_desc_full`) VALUES
(1, 1, 2, 'Semi Precious', '', ''),
(3, 4, 2, 'Outside Lands 2010 - Wolfmother Rock Out', '', ''),
(9, 10, 2, 'Dizzee Rascal performs Bonkers at Glastonbury 2010', '', ''),
(10, 11, 2, 'Newport Folk Festival 2010: Andrew Bird and Calexico', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `white_ip`
--

CREATE TABLE IF NOT EXISTS `white_ip` (
  `wip_id` int(11) NOT NULL AUTO_INCREMENT,
  `wip_ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `wip_provider` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`wip_id`),
  KEY `wip_ip` (`wip_ip`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `white_ip`
--

INSERT INTO `white_ip` (`wip_id`, `wip_ip`, `wip_provider`) VALUES
(4, '127.0.0.1', 'localhost');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_3` FOREIGN KEY (`article_rc_id`) REFERENCES `resource` (`rc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `article_ibfk_4` FOREIGN KEY (`article_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `article_ibfk_5` FOREIGN KEY (`article_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `article_description`
--
ALTER TABLE `article_description`
  ADD CONSTRAINT `article_description_ibfk_1` FOREIGN KEY (`article_desc_article_id`) REFERENCES `article` (`article_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_description_ibfk_2` FOREIGN KEY (`article_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `article_site_ref`
--
ALTER TABLE `article_site_ref`
  ADD CONSTRAINT `article_site_ref_ibfk_1` FOREIGN KEY (`as_ref_article_id`) REFERENCES `article` (`article_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_site_ref_ibfk_2` FOREIGN KEY (`as_ref_site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `article_topic`
--
ALTER TABLE `article_topic`
  ADD CONSTRAINT `article_topic_ibfk_1` FOREIGN KEY (`topic_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `article_topic_ibfk_2` FOREIGN KEY (`topic_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `article_topic_description`
--
ALTER TABLE `article_topic_description`
  ADD CONSTRAINT `article_topic_description_ibfk_1` FOREIGN KEY (`topic_desc_topic_id`) REFERENCES `article_topic` (`topic_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_topic_description_ibfk_2` FOREIGN KEY (`topic_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `article_topic_ref`
--
ALTER TABLE `article_topic_ref`
  ADD CONSTRAINT `article_topic_ref_ibfk_1` FOREIGN KEY (`at_ref_article_id`) REFERENCES `article` (`article_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_topic_ref_ibfk_2` FOREIGN KEY (`at_ref_topic_id`) REFERENCES `article_topic` (`topic_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `article_topic_site_ref`
--
ALTER TABLE `article_topic_site_ref`
  ADD CONSTRAINT `article_topic_site_ref_ibfk_1` FOREIGN KEY (`ts_ref_topic_id`) REFERENCES `article_topic` (`topic_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_topic_site_ref_ibfk_2` FOREIGN KEY (`ts_ref_site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `attribute_description`
--
ALTER TABLE `attribute_description`
  ADD CONSTRAINT `attribute_description_ibfk_3` FOREIGN KEY (`attr_desc_attr_id`) REFERENCES `attribute` (`attr_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attribute_description_ibfk_4` FOREIGN KEY (`attr_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `attribute_group`
--
ALTER TABLE `attribute_group`
  ADD CONSTRAINT `attribute_group_ibfk_3` FOREIGN KEY (`ag_rc_id`) REFERENCES `resource` (`rc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attribute_group_ibfk_4` FOREIGN KEY (`ag_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attribute_group_ibfk_5` FOREIGN KEY (`ag_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `attribute_group_description`
--
ALTER TABLE `attribute_group_description`
  ADD CONSTRAINT `attribute_group_description_ibfk_1` FOREIGN KEY (`ag_desc_ag_id`) REFERENCES `attribute_group` (`ag_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attribute_group_description_ibfk_2` FOREIGN KEY (`ag_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `banner`
--
ALTER TABLE `banner`
  ADD CONSTRAINT `banner_ibfk_1` FOREIGN KEY (`banner_image_id`) REFERENCES `resource` (`rc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `banner_ibfk_2` FOREIGN KEY (`banner_adder_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `banner_ibfk_3` FOREIGN KEY (`banner_changer_id`) REFERENCES `user` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `banner_description`
--
ALTER TABLE `banner_description`
  ADD CONSTRAINT `banner_description_ibfk_3` FOREIGN KEY (`banner_desc_banner_id`) REFERENCES `banner` (`banner_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `banner_site_ref`
--
ALTER TABLE `banner_site_ref`
  ADD CONSTRAINT `banner_site_ref_ibfk_1` FOREIGN KEY (`bs_ref_banner_id`) REFERENCES `banner` (`banner_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `banner_site_ref_ibfk_2` FOREIGN KEY (`bs_ref_site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `category_ibfk_10` FOREIGN KEY (`category_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `category_ibfk_11` FOREIGN KEY (`category_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `category_ibfk_8` FOREIGN KEY (`category_rc_id`) REFERENCES `resource` (`rc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `category_ibfk_9` FOREIGN KEY (`category_category_tree_id`) REFERENCES `category_tree` (`category_tree_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `category_description`
--
ALTER TABLE `category_description`
  ADD CONSTRAINT `category_description_ibfk_1` FOREIGN KEY (`category_desc_category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_description_ibfk_2` FOREIGN KEY (`category_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `category_item_ref`
--
ALTER TABLE `category_item_ref`
  ADD CONSTRAINT `category_item_ref_ibfk_1` FOREIGN KEY (`ci_ref_category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_item_ref_ibfk_2` FOREIGN KEY (`ci_ref_item_id`) REFERENCES `item` (`item_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `category_site_ref`
--
ALTER TABLE `category_site_ref`
  ADD CONSTRAINT `category_site_ref_ibfk_1` FOREIGN KEY (`cs_ref_category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_site_ref_ibfk_2` FOREIGN KEY (`cs_ref_site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`comment_rc_id`) REFERENCES `resource` (`rc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`comment_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `comment_ibfk_3` FOREIGN KEY (`comment_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `comment_ref`
--
ALTER TABLE `comment_ref`
  ADD CONSTRAINT `comment_ref_ibfk_1` FOREIGN KEY (`comment_ref_comment_id`) REFERENCES `comment` (`comment_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `faq`
--
ALTER TABLE `faq`
  ADD CONSTRAINT `faq_ibfk_1` FOREIGN KEY (`faq_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `faq_ibfk_2` FOREIGN KEY (`faq_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `faq_description`
--
ALTER TABLE `faq_description`
  ADD CONSTRAINT `faq_description_ibfk_2` FOREIGN KEY (`faq_desc_faq_id`) REFERENCES `faq` (`faq_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faq_description_ibfk_3` FOREIGN KEY (`faq_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `faq_site_ref`
--
ALTER TABLE `faq_site_ref`
  ADD CONSTRAINT `faq_site_ref_ibfk_1` FOREIGN KEY (`fs_ref_faq_id`) REFERENCES `faq` (`faq_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faq_site_ref_ibfk_2` FOREIGN KEY (`fs_ref_site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `gallery_item`
--
ALTER TABLE `gallery_item`
  ADD CONSTRAINT `gallery_item_ibfk_1` FOREIGN KEY (`gallery_rc_id`) REFERENCES `resource` (`rc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `gallery_item_ibfk_2` FOREIGN KEY (`gallery_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `gallery_item_ibfk_3` FOREIGN KEY (`gallery_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `gallery_item_description`
--
ALTER TABLE `gallery_item_description`
  ADD CONSTRAINT `gallery_item_description_ibfk_1` FOREIGN KEY (`gallery_desc_gallery_id`) REFERENCES `gallery_item` (`gallery_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gallery_item_description_ibfk_2` FOREIGN KEY (`gallery_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `gallery_item_ref`
--
ALTER TABLE `gallery_item_ref`
  ADD CONSTRAINT `gallery_item_ref_ibfk_1` FOREIGN KEY (`gi_ref_gallery_id`) REFERENCES `gallery_item` (`gallery_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `issue`
--
ALTER TABLE `issue`
  ADD CONSTRAINT `issue_ibfk_1` FOREIGN KEY (`issue_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `issue_ibfk_2` FOREIGN KEY (`issue_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `issue_comment`
--
ALTER TABLE `issue_comment`
  ADD CONSTRAINT `issue_comment_ibfk_4` FOREIGN KEY (`ic_issue_id`) REFERENCES `issue` (`issue_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_comment_ibfk_5` FOREIGN KEY (`ic_ic_tree_id`) REFERENCES `issue_comment_tree` (`ic_tree_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `issue_comment_ibfk_6` FOREIGN KEY (`ic_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `issue_comment_ibfk_7` FOREIGN KEY (`ic_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `issue_user`
--
ALTER TABLE `issue_user`
  ADD CONSTRAINT `issue_user_ibfk_1` FOREIGN KEY (`iu_issue_id`) REFERENCES `issue` (`issue_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issue_user_ibfk_2` FOREIGN KEY (`iu_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_24` FOREIGN KEY (`item_rc_id`) REFERENCES `resource` (`rc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `item_ibfk_25` FOREIGN KEY (`item_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `item_ibfk_26` FOREIGN KEY (`item_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `item_ibfk_27` FOREIGN KEY (`item_manufacturer_id`) REFERENCES `manufacturer` (`manufacturer_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `item_bundle`
--
ALTER TABLE `item_bundle`
  ADD CONSTRAINT `item_bundle_ibfk_1` FOREIGN KEY (`bundle_item_id`) REFERENCES `item` (`item_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `item_bundle_subitem_ref`
--
ALTER TABLE `item_bundle_subitem_ref`
  ADD CONSTRAINT `item_bundle_subitem_ref_ibfk_1` FOREIGN KEY (`bs_ref_bundle_id`) REFERENCES `item_bundle` (`bundle_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_bundle_subitem_ref_ibfk_2` FOREIGN KEY (`bs_ref_subitem_id`) REFERENCES `item` (`item_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `item_description`
--
ALTER TABLE `item_description`
  ADD CONSTRAINT `item_description_ibfk_1` FOREIGN KEY (`item_desc_item_id`) REFERENCES `item` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_description_ibfk_2` FOREIGN KEY (`item_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `item_site_ref`
--
ALTER TABLE `item_site_ref`
  ADD CONSTRAINT `item_site_ref_ibfk_1` FOREIGN KEY (`is_ref_item_id`) REFERENCES `item` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_site_ref_ibfk_2` FOREIGN KEY (`is_ref_site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `mail`
--
ALTER TABLE `mail`
  ADD CONSTRAINT `mail_ibfk_3` FOREIGN KEY (`mail_sender_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mail_ibfk_4` FOREIGN KEY (`mail_recipient_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mail_ibfk_5` FOREIGN KEY (`mail_parent_id`) REFERENCES `mail` (`mail_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `manufacturer`
--
ALTER TABLE `manufacturer`
  ADD CONSTRAINT `manufacturer_ibfk_1` FOREIGN KEY (`manufacturer_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `manufacturer_ibfk_2` FOREIGN KEY (`manufacturer_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `manufacturer_description`
--
ALTER TABLE `manufacturer_description`
  ADD CONSTRAINT `manufacturer_description_ibfk_1` FOREIGN KEY (`manufacturer_desc_manufacturer_id`) REFERENCES `manufacturer` (`manufacturer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `manufacturer_description_ibfk_2` FOREIGN KEY (`manufacturer_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_10` FOREIGN KEY (`news_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `news_ibfk_11` FOREIGN KEY (`news_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `news_ibfk_8` FOREIGN KEY (`news_ntopic_id`) REFERENCES `news_topic` (`ntopic_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `news_ibfk_9` FOREIGN KEY (`news_rc_id`) REFERENCES `resource` (`rc_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `news_description`
--
ALTER TABLE `news_description`
  ADD CONSTRAINT `news_description_ibfk_1` FOREIGN KEY (`news_desc_news_id`) REFERENCES `news` (`news_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_description_ibfk_2` FOREIGN KEY (`news_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `news_site_ref`
--
ALTER TABLE `news_site_ref`
  ADD CONSTRAINT `news_site_ref_ibfk_1` FOREIGN KEY (`ns_ref_news_id`) REFERENCES `news` (`news_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_site_ref_ibfk_2` FOREIGN KEY (`ns_ref_site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `news_topic`
--
ALTER TABLE `news_topic`
  ADD CONSTRAINT `ntopic_ibfk_1` FOREIGN KEY (`ntopic_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ntopic_ibfk_2` FOREIGN KEY (`ntopic_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `news_topic_description`
--
ALTER TABLE `news_topic_description`
  ADD CONSTRAINT `ntopic_description_ibfk_1` FOREIGN KEY (`ntopic_desc_ntopic_id`) REFERENCES `news_topic` (`ntopic_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ntopic_description_ibfk_2` FOREIGN KEY (`ntopic_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `news_topic_site_ref`
--
ALTER TABLE `news_topic_site_ref`
  ADD CONSTRAINT `news_topic_site_ref_ibfk_1` FOREIGN KEY (`ns_ref_ntopic_id`) REFERENCES `news_topic` (`ntopic_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_topic_site_ref_ibfk_2` FOREIGN KEY (`ns_ref_site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `page`
--
ALTER TABLE `page`
  ADD CONSTRAINT `page_ibfk_3` FOREIGN KEY (`page_rc_id`) REFERENCES `resource` (`rc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `page_ibfk_4` FOREIGN KEY (`page_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `page_ibfk_5` FOREIGN KEY (`page_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `page_description`
--
ALTER TABLE `page_description`
  ADD CONSTRAINT `page_description_ibfk_1` FOREIGN KEY (`page_desc_page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `page_description_ibfk_2` FOREIGN KEY (`page_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `page_site_ref`
--
ALTER TABLE `page_site_ref`
  ADD CONSTRAINT `page_site_ref_ibfk_1` FOREIGN KEY (`ps_ref_page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `page_site_ref_ibfk_2` FOREIGN KEY (`ps_ref_site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `resource`
--
ALTER TABLE `resource`
  ADD CONSTRAINT `resource_ibfk_1` FOREIGN KEY (`rc_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `resource_ibfk_2` FOREIGN KEY (`rc_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `role_description`
--
ALTER TABLE `role_description`
  ADD CONSTRAINT `role_description_ibfk_1` FOREIGN KEY (`role_desc_role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_description_ibfk_2` FOREIGN KEY (`role_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `site`
--
ALTER TABLE `site`
  ADD CONSTRAINT `site_ibfk_2` FOREIGN KEY (`site_default_language_id`) REFERENCES `language` (`language_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `site_ibfk_1` FOREIGN KEY (`site_vertical_id`) REFERENCES `vertical` (`vertical_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `site_description`
--
ALTER TABLE `site_description`
  ADD CONSTRAINT `site_description_ibfk_1` FOREIGN KEY (`site_desc_site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `site_description_ibfk_2` FOREIGN KEY (`site_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `tip`
--
ALTER TABLE `tip`
  ADD CONSTRAINT `tip_ibfk_1` FOREIGN KEY (`tip_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tip_ibfk_2` FOREIGN KEY (`tip_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `tip_description`
--
ALTER TABLE `tip_description`
  ADD CONSTRAINT `tip_description_ibfk_1` FOREIGN KEY (`tip_desc_tip_id`) REFERENCES `tip` (`tip_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tip_description_ibfk_2` FOREIGN KEY (`tip_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `transport_price`
--
ALTER TABLE `transport_price`
  ADD CONSTRAINT `transport_price_ibfk_1` FOREIGN KEY (`tp_transport_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_6` FOREIGN KEY (`user_rc_id`) REFERENCES `resource` (`rc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_ibfk_7` FOREIGN KEY (`user_role_id`) REFERENCES `role` (`role_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_ibfk_8` FOREIGN KEY (`user_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_ibfk_9` FOREIGN KEY (`user_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `user_news_subscription`
--
ALTER TABLE `user_news_subscription`
  ADD CONSTRAINT `user_news_subscription_ibfk_1` FOREIGN KEY (`uns_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_news_subscription_ibfk_2` FOREIGN KEY (`uns_ntopic_id`) REFERENCES `news_topic` (`ntopic_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_news_subscription_log`
--
ALTER TABLE `user_news_subscription_log`
  ADD CONSTRAINT `user_news_subscription_log_ibfk_1` FOREIGN KEY (`log_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_news_subscription_log_ibfk_2` FOREIGN KEY (`log_ntopic_id`) REFERENCES `news_topic` (`ntopic_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`ur_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`ur_role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_stats`
--
ALTER TABLE `user_stats`
  ADD CONSTRAINT `user_stats_ibfk_1` FOREIGN KEY (`stats_user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `video_ibfk_1` FOREIGN KEY (`video_rc_id`) REFERENCES `resource` (`rc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `video_ibfk_2` FOREIGN KEY (`video_adder_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `video_ibfk_3` FOREIGN KEY (`video_changer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `video_description`
--
ALTER TABLE `video_description`
  ADD CONSTRAINT `video_description_ibfk_1` FOREIGN KEY (`video_desc_video_id`) REFERENCES `video` (`video_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `video_description_ibfk_2` FOREIGN KEY (`video_desc_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE;
