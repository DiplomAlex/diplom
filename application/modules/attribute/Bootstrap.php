<?php

class Attribute_Bootstrap extends Zend_Application_Module_Bootstrap
{

    /*protected function _initAttributes()
    {
        require_once(realpath(dirname(__FILE__) . '/lib/attribute/include.php'));
    }

    protected function _initAcl()
    {
        $acl = Zend_Registry::get('Zend_Acl');
        $aclIni = new Zend_Config_Ini(FRONT_APPLICATION_PATH . '/modules/' . strtolower($this->getModuleName()) . '/configs/acl.ini');
        $acl->addResources($aclIni->resources);
        $acl->addAllows($aclIni->allow);
        $acl->addDenies($aclIni->deny);
    }

    protected function _initEvents()
    {
        $this->bootstrap('FrontController');
        $events = array();
        $xml = new Zend_Config_Xml(FRONT_APPLICATION_PATH . '/modules/' . strtolower($this->getModuleName()) . '/configs/events.xml');
        if ($xml->events) {
            foreach ($xml->events as $eventName => $event) {
                $events[$eventName] = $event->observers;
            }
        }
        Zend_Registry::set('events_' . strtolower($this->getModuleName()), $events);
        $kernelEvents = Zend_Registry::get('events');
        Zend_Registry::set('events', array_merge($kernelEvents, $events));

        return $events;
    }

    protected function _initControllers()
    {
        $module = $this->getModuleName();
        Zend_Controller_Action_HelperBroker::addPath(
            FRONT_APPLICATION_PATH . '/modules/' . strtolower($module) . '/controllers/Helper',
            ucfirst($module) . '_Controller_Action_Helper'
        );
        $this->getResourceLoader()->addResourceTypes(array(
            'controllerhelpers' => array(
                'namespace' => 'Controller_Action_Helper',
                'path' => 'controllers/Helper',
            ),
            'controllerplugins' => array(
                'namespace' => 'Controller_Plugin',
                'path' => 'controllers/Plugin',
            ),
        ));
    }*/

}