<?php

class Lab_View_Helper_Box_Header extends Zend_View_Helper_Abstract
{

    public function box_Header()
    {
        $this->view->isAuthorized = Model_Service::factory('user')->isAuthorized();
        $this->view->user = Model_Service::factory('user')->getCurrent();
        
        //$this->view->categories = Model_Service::factory('catalog/category')->getAllActiveByParent(0);
	$this->view->categories = Model_Service::factory('catalog/category')->getAll();
        $this->view->collection_categories = Model_Service::factory('catalog/category')->getAllActiveByParent('collections');
        $this->view->kamni = Model_Service::factory('catalog/category')->getAllActiveByParent('kamni');
        return $this->view->render('box/header.phtml');
    }

}