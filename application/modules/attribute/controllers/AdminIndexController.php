<?php

class Attribute_AdminIndexController extends Zend_Controller_Action
{
    protected $_session = NULL;

    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
            $this->getHelper('ViewRenderer')->setNoRender();
        }
    }

    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace(__CLASS__);
        }
        return $this->_session;
    }
    
    public function apiAction()
    {
        $controller = new AttrController();
        $controller->dispatch(@$_REQUEST['action'], @$_REQUEST);
        die();
    }
    
    public function indexAction()
    {
        //die($this->view->layout()->getLayout());
        //$this->view->layout()->disableLayout();
    }

}

