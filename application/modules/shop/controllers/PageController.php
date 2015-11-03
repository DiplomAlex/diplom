<?php

class Shop_PageController extends Zend_Controller_Action
{
    protected $_service = NULL;
    
    protected function _getService()
    {
        if ($this->_service === NULL) {
            $this->_service = Model_Service::factory('page');
            // $site = Model_Service::factory('site')->getCurrent();
            // $this->_service->getHelper('Multisite')->setCurrentSiteId($site->id);
        }
        return $this->_service;
    } 
    
    public function init()
    {
        App_Event::factory('Shop_Controller__init', array($this))->dispatch();
    }
	
	public function detailedAction()
    {
        $service = $this->_getService();
        $page = $service->get($this->_getParam('seo_id'));
        $this->view->headTitle($page['title']);
        $this->view->headMeta($page['meta_keywords']);
        $this->view->headMeta($page['meta_description']);
        $this->view->headdescription = $page->meta_description;
        $this->view->headkeywords = $page->meta_keywords;
        $this->view->page = $page;
        if ($service->isDriverEnabled($page->driver)) {
            $driver = $this->getHelper('PageDriver_'.$page->driver);
            if ($this->getRequest()->isXmlHttpRequest() AND $driver->isAjaxAccepted()) {
                $this->view->layout()->disableLayout();
                $this->getHelper('ViewRenderer')->setNoRender();
                $response = $driver->ajaxAction();
                echo $response;
            }
            else if ( ! $this->getRequest()->isXmlHttpRequest()) {                
                $response = $driver->action();
                $this->view->driverHtml = $response;
            }
        }
    }

	
}

