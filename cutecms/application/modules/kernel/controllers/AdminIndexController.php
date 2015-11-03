<?php

class AdminIndexController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('AdminController__init', array($this, 'admin'))->dispatch();
    }

    public function indexAction()
    {
    }

    public function errorAction()
    {
        $this->getResponse()->setHeader('404', 'HTTP/1.1 404 Not Found');
    }

    public function errorAclAction()
    {
        $this->getResponse()->setHeader('404', 'HTTP/1.1 404 Not Found');
    }

    public function developingAction()
    {
    }


}

