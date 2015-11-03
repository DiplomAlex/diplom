<?php

class Shop_IndexController extends Zend_Controller_Action
{
    protected $_itemService = NULL;

    protected function _getItemService()
    {
        if ($this->_itemService === NULL) {
            $this->_itemService = Model_Service::factory('catalog/item');
            $site = Model_Service::factory('site')->getCurrent();
            $this->_itemService->getHelper('Multisite')->setCurrentSiteId($site->id);
        }
        return $this->_itemService;
    }

    public function init()
    {
        App_Event::factory('Shop_Controller__init', array($this))->dispatch();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        }
		$this->view->headTitle('Магазин Ювелирного дома SA&GA. Каталог ювелирных украшений с ценами');
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout('layout-index');
			
		$this->view->slider = Model_Service::factory('banner')->getAllByPlace('home_slider');
        $banner = Model_Service::factory('banner')->getAllByPlace('home_banner');	
        $this->view->main_banner = $banner[0];

        /** @var $itemService Catalog_Model_Service_Item */
		$itemService = Model_Service::factory('catalog/item');
		$this->view->our_collection_items = $itemService->getHomeOurCollectionsItems();
		$this->view->slider_items = $itemService->getHomeSliderItems();
        
        $galleries = array();
		foreach ($this->view->our_collection_items as $item)
            $galleries[$item->id] = $itemService->getHelper('Gallery')->getGalleryWithActiveImage($item['id']);

		foreach ($this->view->slider_items as $item)
            $galleries[$item->id] = $itemService->getHelper('Gallery')->getGalleryWithActiveImage($item['id']);

        $this->view->galleries = $galleries;
        
        $our_collections = Model_Service::factory('banner')->getAllByPlace('home_our_collections');	
        $this->view->our_collections = $our_collections[0];
    }
    
    public function sitemapAction()
    {		
        $this->view->pages = Model_Service::factory('page')->getAll();
        $this->view->categories = Model_Service::factory('catalog/category')->getAll();
    }
    
	public function ajaxAddSubscribersAction()
    {		
        $params = $this->getRequest()->getParams();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
        $validate = new Zend_Validate_EmailAddress();
        
        if( $this->getRequest()->isPost() ){
            if  ( $validate->isValid($params['email']) && !$isSubscribed = Model_Service::factory('news-topic')->isSubscribedEmail($params['email']) ){
                try{     
                    $success = Model_Service::factory('news-topic')->addSubscribeEmail($params['email']);
                } catch(Exeption $e){
                    $success = FALSE;
                }                
                die( Zend_Json::encode( array('success' => $success) ) );
            } elseif( $isSubscribed ) {
                die( Zend_Json::encode( array('success' => FALSE, 'email_is_subscribed' => TRUE) ) );
            } else {
                die( Zend_Json::encode( array('success' => FALSE, 'email_error' => TRUE) ) );
            }
		}
		die( Zend_Json::encode( array('success' => FALSE) ) );
    }
    
	public function serverAction()
    {	
		$this->_helper->layout->disableLayout();
		$param = $this->getRequest()->getParams();
		//App_debug::log($param); 
		$service = new App_Service_IShopServerWSService_Server($param);
	}

}