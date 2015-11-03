<?php

class Catalog_Controller_Action_Helper_AdminItem extends Zend_Controller_Action_Helper_Abstract
{
    
    const SESSION_NAMESPACE = 'Catalog_AdminItem_Controller';
    
    protected $_session = NULL;

    protected static $_inited = array();
    
    public function direct(array $defaultInjections = NULL)
    {
        $controller = $this->getActionController();
        $controller->getHelper('Injector')->direct($defaultInjections);
        $class = get_class($controller);
        if ( ! array_key_exists($class, self::$_inited) OR ! self::$_inited[$class]) {        
            App_Event::factory('AdminController__init', array($controller))->dispatch();
            if ($controller->getRequest()->isXmlHttpRequest()) {
                $controller->view->layout()->disableLayout();
                $controller->getHelper('ViewRenderer')->setNoRender();
            }
            self::$_inited[$class] = TRUE;        
        }
    }
    
    public function session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
        }
        return $this->_session;
    }
    
    
    
}