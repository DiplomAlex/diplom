<?php

class Checkout_Bootstrap extends Zend_Application_Module_Bootstrap
{


    protected function _initConfig()
    {
        $module = strtolower($this->getModuleName());
        $service = Model_Service::factory('config');
        $filename = $service->getConfigFilename($module.'/config');
        if ( ! file_exists($filename)) {
            $filename = $service->getConfigFilename('kernel/config');
            $merge = FALSE;
        }
        else {
            $merge = TRUE;
        }
        $moduleConfig = $service->read($module.'/config');
        if ($moduleConfig->images AND $merge) {
            Zend_Registry::get('config')->images->merge($moduleConfig->images);
        }
        Zend_Registry::set($module.'_config', $moduleConfig);
    }

    protected function _initAcl()
    {
        $this->bootstrap('Config');
        $acl = Zend_Registry::get('Zend_Acl');
        $aclIni = new Zend_Config_Ini(APPLICATION_PATH . '/modules/'.strtolower($this->getModuleName()).'/configs/acl.ini');
        $acl->addResources($aclIni->resources);
        $acl->addAllows($aclIni->allow);
        $acl->addDenies($aclIni->deny);
    }

    protected function _initEvents()
    {
        $this->bootstrap('FrontController');
        $events = array();
        $xml = new Zend_Config_Xml(APPLICATION_PATH . '/modules/'.strtolower($this->getModuleName()).'/configs/events.xml');
        if ($xml->events) foreach ($xml->events as $eventName => $event) {
            $events[$eventName] = $event->observers;
        }
        Zend_Registry::set('events_'.strtolower($this->getModuleName()), $events);
        $kernelEvents = Zend_Registry::get('events');
        Zend_Registry::set('events', array_merge($kernelEvents, $events));
        return $events;
    }


    protected function _initObservers()
    {
        $this->bootstrap('Events');
        $this->getResourceLoader()->addResourceTypes(array(
            'observers' => array(
                'namespace' => 'Observer',
                'path'      => 'controllers/Observer',
            ),
        ));
    }

    protected function _initControllers()
    {
        Zend_Controller_Action_HelperBroker::addPath(
                APPLICATION_PATH . '/modules/'.strtolower($this->getModuleName()).'/controllers/Helper',
                ucfirst($this->getModuleName()).'_Controller_Action_Helper'
        );
        $this->getResourceLoader()->addResourceTypes(array(
            'controllerhelpers' => array(
                'namespace' => 'Controller_Action_Helper',
                'path'      => 'controllers/Helper',
            ),
            'controllerplugins' => array(
                'namespace' => 'Controller_Plugin',
                'path'      => 'controllers/Plugin',
            ),
        ));
    }


    protected function _initAutoload()
    {
        $this->getResourceLoader()->removeResourceType('mappers');
    }

}

