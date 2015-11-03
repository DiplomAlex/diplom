<?php

class FaqController extends Zend_Controller_Action
{

    protected $_service = NULL;
    
    protected function _getService()
    {
        if ($this->_service === NULL) {
            $this->_service = Model_Service::factory('faq');
            $site = Model_Service::factory('site')->getCurrent();
            $this->_service->getHelper('Multisite')->setCurrentSiteId($site->id);
        }
        return $this->_service;
    } 
    
    public function init()
    {
        App_Event::factory('Controller__init', array($this, 'layout-faq'))->dispatch();
    }
    
    public function indexAction()
    {
        $this->view->faqs = $this->_getService()->paginatorGetAllActive(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
    }

    public function detailedAction()
    {
        $faq = $this->_getService()->get($this->_getParam('seo_id'));
        $this->view->faq = $faq;
    }


}

