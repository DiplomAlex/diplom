<?php

class Issues_Bootstrap extends Zend_Application_Module_Bootstrap
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
        $aclIni = Model_Service::factory('config')->read(strtolower($this->getModuleName()).'/acl.ini');
        $acl->addResources($aclIni->resources);
        $acl->addAllows($aclIni->allow);
        $acl->addDenies($aclIni->deny);
    }

    protected function _initEvents()
    {
        $this->bootstrap('FrontController');
        $events = array();
        $xml = Model_Service::factory('config')->read(strtolower($this->getModuleName()).'/events.xml'); 
        if ($xml->events) foreach ($xml->events as $eventName => $event) {
            $events[$eventName] = $event->observers;
        }
        Zend_Registry::set('events_'.strtolower($this->getModuleName()), $events);
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

    protected function _initAutoload()
    {
        $this->getResourceLoader()->removeResourceType('mappers');
    }

}

