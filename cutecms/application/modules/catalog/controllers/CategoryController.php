<?php

class Catalog_CategoryController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('Controller__init', array($this, 'layout-category'))->dispatch();
    }

    public function indexAction()
    {
        $service = Model_Service::factory('catalog/category');
        $seo_id = $this->_getParam('seo_id');
        if ( ! empty($seo_id)) {
            $parent = $service->getComplexBySeoId($seo_id);
            $parentId = $parent->id;
            $this->view->category = $parent;            
        }
        else {
            $parentId = NULL;
            $this->view->category = NULL;
        }

        $this->view->categories = $service->getAllByParent($parentId);
        $this->view->items = Model_Service::factory('catalog/item')->paginatorGetAllByCategory(
            $parentId,
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );

        $this->getFrontController()->getRouter()->setGlobalParam('seo_id', $seo_id);
        
        if ($seo_id) {
            $this->getHelper('ViewRenderer')->setScriptAction('catalog/category.phtml');
        }
    }

    
    
}
