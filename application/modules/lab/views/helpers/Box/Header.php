<?php

class Lab_View_Helper_Box_Header extends Zend_View_Helper_Abstract
{

    public function box_Header()
    {
        $this->view->isAuthorized = Model_Service::factory('user')->isAuthorized();
        $this->view->user = Model_Service::factory('user')->getCurrent();
        
       return $this->view->render('box/header.phtml');
    }

}