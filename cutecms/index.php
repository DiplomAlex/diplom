<?php

// Define path to current directory
defined('APPLICATION_PUBLIC')
    || define('APPLICATION_PUBLIC', realpath(dirname(__FILE__)));


// Define path to application directory of current frontend
defined('FRONT_APPLICATION_PATH')
    || define('FRONT_APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(FRONT_APPLICATION_PATH.'/../../cutecms/application'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/library'),
    get_include_path(),
)));


if (PHP_SAPI == 'cli') {
    $_SERVER['REQUEST_URI'] = $argv[1];
}

require 'App/PreBoot.php';

App_PreBoot::processMagicQuotesGPC();

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', App_PreBoot::isInstalled()?'development':'installer');

// Define application base
defined('APPLICATION_BASE')
    || define('APPLICATION_BASE', App_PreBoot::getApplicationBase(NULL, (PHP_SAPI=='cli'?$_SERVER['argv'][1]:NULL)));

/** Zend_Application */
require 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    App_PreBoot::getApplicationIniConfig()
);

App_PreBoot::initFrontModule();

$application->bootstrap()->run();
