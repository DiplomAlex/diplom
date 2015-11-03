<?php

class Api_Bootstrap extends Zend_Application_Module_Bootstrap
{

    /**
     * Инициализация конфига
     */
    protected function _initConfig()
    {
        $module = strtolower($this->getModuleName());
        Zend_Registry::set($module . '_config', Model_Service::factory('config')->read($module . '/config.xml'));
    }

    /**
     * Инициализация загрузчика
     */
    protected function _initAutoload()
    {
        $this->getResourceLoader()->removeResourceType('mappers');
    }

    /**
     * Инициализация обработчика ошибок
     */
    protected function _initErrorHandler()
    {
        $router = new Zend_Controller_Router_Rewrite();
        $request =  new Zend_Controller_Request_Http();

        if (strtolower($router->route($request)->getModuleName()) == strtolower($this->getModuleName())) {
            Zend_Controller_Front::getInstance()->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
                'module' => 'api', 'controller' => 'error', 'action' => 'error'
            )));
        }
    }

}