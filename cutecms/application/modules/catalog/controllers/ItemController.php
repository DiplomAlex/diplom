<?php

class Catalog_ItemController extends Zend_Controller_Action
{

    protected $_itemService = NULL;
    protected $_categoryService = NULL;
    protected $_session = NULL;

    protected function _session()
    {
    	if ($this->_session === NULL) {
    		$this->_session = new Zend_Session_Namespace(__CLASS__);
    	}
    	return $this->_session;
    }

    protected function _getItemService()
    {
        if ($this->_itemService === NULL) {
            $this->_itemService = Model_Service::factory('catalog/item');
            $site = Model_Service::factory('site')->getCurrent();
            $this->_itemService->getHelper('Multisite')->setCurrentSiteId($site->id);
            if (empty($this->_session()->sortingMode)) {
                $this->_session()->sortingMode = 'name';
            }
            $sortingHelper = $this->_itemService->getHelper('Sorting');
            $sortingHelper->setCurrentSortingMode($this->_session()->sortingMode, $this->_session()->sortingDirection);
            if (empty($this->_session()->sortingDirection)) {
                $this->_session()->sortingDirection = $sortingHelper->getSortingDirection($this->_session()->sortingMode);
            }
            if (empty($this->_session()->sortingModesOrder)) {
                $this->_session()->sortingModesOrder = $sortingHelper->getSortingModesOrder();
            }
            else {
                $sortingHelper->setSortingModesOrder($this->_session()->sortingModesOrder);
            }
        }
        return $this->_itemService;
    }

    protected function _getCategoryService()
    {
        if ($this->_categoryService === NULL) {
            $this->_categoryService = Model_Service::factory('catalog/category');
            $site = Model_Service::factory('site')->getCurrent();
            $this->_categoryService->getHelper('Multisite')->setCurrentSiteId($site->id);
        }
        return $this->_categoryService;
    }


    public function init()
    {
        App_Event::factory('Controller__init', array($this, $this->_getParam('layoutName', NULL)))->dispatch();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        }
    }


    public function setSortingModeAction()
    {
        $this->_session()->sortingMode = $this->_getParam('mode');
        $this->_session()->sortingDirection = $this->_getParam('direction', 'ASC');

        $newOrder = array($this->_session()->sortingMode => $this->_session()->sortingDirection);
        foreach ($this->_session()->sortingModesOrder as $modeName=>$modeDirection) {
            if ($modeName != $this->_session()->sortingMode) {
                $newOrder [$modeName] = $modeDirection;
            }
        }
        $this->_session()->sortingModesOrder = $newOrder;

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
            $this->getHelper('ViewRenderer')->setNoRender();
        }
        else {
            $this->getHelper('Redirector')->gotoUrlAndExit($this->_getParam('redirect'));
        }
    }


    /**
     * list of items
     */
    public function indexAction()
    {
        $catService = $this->_getCategoryService();
        $itemService = $this->_getItemService();
        $seoId = $this->_getParam('seo_id');
        if ($seoId) {
            $category = $catService->getComplexBySeoId($seoId);
            $categoryId = $category->id;
            $this->view->currentCategory = $category;
        }
        else {
            $categoryId = NULL;
        }
        $itemSeoId = $this->_getParam('item_seo_id');
        if ($itemSeoId) {
            $this->view->item = $itemService->getComplexBySeoId($itemSeoId);
            $itemService->getMapper()->getPlugin('Resource')->reprocessPreviews($this->view->item);
            $this->getHelper('ViewRenderer')->setScriptAction('catalog/item.phtml');
        }
        else {
            $this->view->items = $itemService->paginatorGetAllByCategory(
                $categoryId,
                /*$this->_getRowsPerPage()*/15,
                $this->_getParam('page'),
                TRUE
            );
            foreach ($this->view->items as $item) {
                $itemService->getMapper()->getPlugin('Resource')->reprocessPreviews($item);
            }
            $this->view->lastDateAdded = $itemService->getLastDateAddedInCategory($categoryId, TRUE);
        }
        $this->view->sortingMode = $this->_session()->sortingMode;
        $this->view->sortingDirection = $this->_session()->sortingDirection;

        $this->view->urlParams = array(
            'seo_id' => $this->_getParam('seo_id'),
        	'item_seo_id' => $this->_getParam('item_seo_id'),
			'page' => $this->_getParam('page'),        
        );
        
        if ($script = $this->_getParam('renderScript', FALSE)) {
            $this->renderScript($script);
        }
    }

    /**
     * item detailed page
     */
    public function detailedAction()
    {
        $catService = $this->_getCategoryService();
        $itemService =  $this->_getItemService();
        $itemSeoId = $this->_getParam('seo_id');
        $catId = $this->_getParam('cat_id');
        if ($catId) {
            $category = $catService->getComplex($catId);
            $this->view->currentCategory = $category;
        }
        $items = $itemService->getCurrentPreviousNext($itemSeoId, $catId);
        $item = $items['current'];
        $this->view->item = $item;
        //$this->view->itemNext = $items['next'];
        //$this->view->itemPrev = $items['previous'];
        $itemService->getMapper()->getPlugin('Resource')->reprocessPreviews($item);
        if ($item->is_configurable) {
            /*$this->view->configuratorHtml = $this->view->renderForm($this->_getConfiguratorForm($item),$this->_getConfiguratorScript($item));*/
            $this->view->bundles = Model_Service::factory('catalog/item-bundle')->getBundlesForItem($item);
            $this->getHelper('ViewRenderer')->setScriptAction('item-configurable');
        }
        $this->view->gallery = $itemService->getHelper('Gallery')->getLinkedToContent($item->id);
        $galService = Model_Service::factory('gallery');
        foreach ($this->view->gallery as $galItem) {
            $galService->getMapper()->getPlugin('Resource')->reprocessPreviews($galItem);
        }
        if ($script = $this->_getParam('renderScript', FALSE)) {
            $this->renderScript($script);
        }
    }

    public function addToCartAction()
    {
        $this->getHelper('ViewRenderer')->setNoRender();
        $itemService = $this->_getItemService();
        $bundleService = Model_Service::factory('catalog/item-bundle');
        $values = $this->getRequest()->getParams();
        $item = $itemService->createFromArray($values);
        $item->current_bundles = $bundleService->mergeAllWithCurrentsFromArray($item, $values);
        $itemService->addToShoppingCart($item);
        if ( ! $this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->url(array(), 'shopping_cart'));
        }
        echo 'ok';


    }

    public function ajaxRecalculatePriceAction()
    {
        $this->getHelper('ViewRenderer')->setNoRender();
        $itemService = $this->_getItemService();
        $bundleService = Model_Service::factory('catalog/item-bundle');
        $values = $this->getRequest()->getParams();
        $item = $itemService->createFromArray($values);
        $item->current_bundles = $bundleService->mergeAllWithCurrentsFromArray($item, $values);
        $result = $itemService->calculatePrice($item) * $values['qty'];
        echo $result;
    }

    public function searchAction()
    {
        $q = $this->_getParam('q');
        $by = $this->_getParam('by');
        $this->getRequest()->setParam($by, $q);
        $this->view->items = $this->_getItemService()->paginatorGetAll(
            $this->_getRowsPerPage(),
            $this->_getParam('page')
        );
        if ( ! empty($by)) {
            $this->view->by = $this->view->translate($by);
        }
        $this->view->q = $q;
        $this->view->urlParams = array('by'=>$by, 'q'=>$q,);
        if ($script = $this->_getParam('renderScript')) {
            $this->_helper->ViewRenderer->renderScript($script);
        }
    }

    public function searchAttrAction()
    {
        $service = $this->_getItemService();
        $q = $this->_getParam('q');
        $by = $this->_getParam('by');
        if (substr($by, 0, 5) == 'attr_') {
            $attrParts = explode('_', $by);
            array_shift($attrParts);
            $attrType = array_pop($attrParts);
            $attrCode = implode('_', $attrParts);
            $service->setFilterParams('attribute', array($attrCode, $attrType));
            $this->getRequest()->setParam('filter_attribute', $q);
        }
        else {
            $this->getRequest()->setParam('filter_'.$by, $q);
        }
        $this->view->items = $service->paginatorGetAll(
            $this->_getRowsPerPage(),
            $this->_getParam('page')
        );
        $this->view->q = $q;
        $this->view->urlParams = array('by'=>$by, 'q'=>$q,);
        if ($script = $this->_getParam('renderScript')) {
            $this->_helper->ViewRenderer->renderScript($script);
        }
        //print_r($this->view->urlParams);
    }

    public function priceListXlsAction()
    {
        $this->view->layout()->disableLayout();
        $this->view->items =  $this->_getItemService()->getAllWithCategory();
        $this->getResponse()->setHeader('Pragma', 'public')
                            ->setHeader('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT')
                            ->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT')
                            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
                            ->setHeader('Cache-Control', 'pre-check=0, post-check=0, max-age=0')
                            ->setHeader('Pragma', 'no-cache')
                            ->setHeader('Expires', '0')
                            ->setHeader('Content-Transfer-Encoding', 'UTF-8')
                            ->setHeader('Content-Type', 'application/vnd.ms-excel')
                            ->setHeader('Content-type', 'application/x-msexcel')
                            ;
        if ($script = $this->_getParam('renderScript')) {
            $this->_helper->ViewRenderer->renderScript($script);
        }

    }
    
    
    public function manufacturerAction()
    {
        $id = $this->_getParam('id');
        $manuf = Model_Service::factory('catalog/manufacturer')->getComplex($id);
        $this->view->manufacturer = $manuf;
        $this->view->items = $this->_getItemService()->paginatorGetByManufacturer(
            $manuf->id,
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        if ($script = $this->_getParam('renderScript')) {
            $this->_helper->ViewRenderer->renderScript($script);
        }
        
    }

    public function manufacturerAllAction()
    {
        $this->view->manufacturers = Model_Service::factory('catalog/manufacturer')->paginatorGetAll(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        if ($script = $this->_getParam('renderScript')) {
            $this->_helper->ViewRenderer->renderScript($script);
        }
        
    }
    
    public function voteAction()
    {
        $rate = intval($this->_getParam('value'));
        $itemId = $this->_getParam('id');
        $service = $this->_getItemService();
        $item = $service->get($itemId);
        $item->votes ++;
        $item->rate += $rate;
        $service->save($item);
        $this->view->layout()->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        echo Zend_Json::encode(array('rate'=>$item->rate, 'votes'=>$item->votes, 'result'=>ceil($item->rate/$item->votes)));
    }
    
        

    protected function _getRowsPerPage()
    {
        return $this->_getParam('rowsPerPage', $this->getHelper('RowsPerPage')->getValue());
    }
    


}
