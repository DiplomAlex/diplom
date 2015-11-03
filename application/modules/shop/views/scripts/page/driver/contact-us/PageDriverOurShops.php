<?php

class Controller_Action_Helper_PageDriverOurShops extends Controller_Action_Helper_PageDriver_Abstract
{ 
    protected $_options = array(
        'viewScript' => 'page/driver/our-shops/action.phtml',
        'ajaxScript' => 'page/driver/our-shops/action.phtml',
    );
    
    public function isAjaxAccepted()
    {
        return TRUE;
    }
    
    public function ajaxAction()
    {
        $controller = $this->getActionController();
        $response = $controller->view->render($this->getOption('ajaxScript'));
        return $response;
    }
    
    public function action()
    {
        $controller = $this->getActionController();
        $response = $controller->view->render($this->getOption('viewScript'));
        return $response;
    }
}

