<?php

class IndexController extends Zend_Controller_Action
{


    public function init()
    {
        App_Event::factory('Controller__init', array($this, 'layout-index'))->dispatch();
    }


    public function indexAction()
    {
    	$this->view->site = Model_Service::factory('site')->getCurrent();
    }

    public function developingAction()
    {
    }

}

