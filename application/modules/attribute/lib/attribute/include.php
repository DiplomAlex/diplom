<?php

require_once(realpath(($dir = dirname(__FILE__)).'/lib/Attr.php'));
require_once(realpath($dir.'/lib/AttrController.php'));

//для теста
//require_once(realpath($dir.'/mysqli_dev.php'));

$ini = parse_ini_file(realpath($dir.'/config/config.ini'), TRUE);
// убрать поднятие линка, если планируется использовать в Zend - передать вместо $link ссылку на MySQLi соединение из адаптера
/*$link = new mysqli_dev(
            $ini['mysqli']['host'],
            $ini['mysqli']['user'],
            $ini['mysqli']['pass'],
            $ini['mysqli']['db']
);
if (!$link) { 
   throw new AttrException("Can't connect to MySQL: ".mysqli_connect_error());
}
$link->set_charset("utf8");*/
Attr::init(Zend_Db_Table::getDefaultAdapter()->getConnection(), $ini['attr']['prefix']);
/*$controller = new AttrController();*/
/*$controller->dispatch(@$_REQUEST['action'], @$_REQUEST);*/