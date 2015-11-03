<?php

class AdminMultisiteController extends Zend_Controller_Action
{ 
    
    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }
    
    public function setSiteIdAction()
    {
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $this->view->layout()->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        if ( (int) $siteId) {
            $site = Model_Service::factory('site')->get($siteId);
            echo Zend_Json::encode($site->toArray());
        }
    }
    
}
