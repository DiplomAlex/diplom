<?php

class Lab_IndexController extends Zend_Controller_Action
{
    protected $_itemService = NULL;

    public function init()
    {
        App_Event::factory('Lab_Controller__init', array($this))->dispatch();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        }
		$this->view->headTitle('Diplom');
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout('layout-index');
    }


}