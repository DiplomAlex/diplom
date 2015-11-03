<?php

class Shop_SearchController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('Shop_Controller__init', array($this))->dispatch();
    }

    public function indexAction()
    {         
		if( $this->getRequest()->isGet() ){
        
            $stripTags = new Zend_Filter_StripTags();
            $param = $stripTags->filter($this->_getParam('text'));
            
            $htmlEntities = new Zend_Filter_HtmlEntities();            
            $param = $htmlEntities->filter($param);
            
            $param = mb_strtolower($param, 'UTF-8');

            if ( $param && mb_strlen($param, 'UTF-8') > 2 ){
                $items = array();
                $items_count = Model_Service::factory('catalog/item')->getTotalCountByText($param);  
                
                foreach(Model_Service::factory('catalog/item')->getByText($param, 1, 56) as $item) {
                    $full = $item->full;//strip_tags($item->full);
                    $full = preg_replace('/\&[^;]*;/', '', $full);
                    
                    $full = mb_substr_count(mb_strtolower($full, 'UTF-8'), $param);             
                    $name = mb_substr_count(mb_strtolower($item->name, 'UTF-8'), $param);
                    $price = mb_substr_count(mb_strtolower($item->price, 'UTF-8'), $param);
                    $sku = mb_substr_count(mb_strtolower($item->sku, 'UTF-8'), $param);
                    
                    if ( $full || $name || $price || $sku){                        
                        $items[] = $item;
                    } else {
                        $items_count--;
                    }
                }
                
                if( !count($items) )
                    $items_count = 0;
                    
                foreach ($items as $item){
                    $galleries[$item->id] = Model_Service::factory('catalog/item')->getHelper('Gallery')->getGalleryWithActiveImage($item['id']);
                }
                
                $this->view->galleries = $galleries;   
                $this->view->items = $items;
                $this->view->items_count = $items_count;
                
                $pages = array();
                $quotes = array();
                
                foreach(Model_Service::factory('page')->getByText($param) as $page) {
                    $text = strip_tags($page->full);
                    $text = preg_replace('/\&[^;]*;/', '', $text);
                    $pos = mb_strpos( mb_strtolower($text, 'UTF-8'), $param, 0, 'UTF-8');
                    if ( $pos || mb_substr_count(mb_strtolower($page->title, 'UTF-8'), $param)){
                        $pages[] = $page;
                        $pos_v = $pos + 100;
                        $pos_n = $pos - 30;
                        if ( $pos_n < 0 ) $pos_n = 0;
                        $quote = mb_substr($text, $pos_n, $pos_v, 'UTF-8');
                        $quotes[$page->id] = ( $pos_n != 0 ) ? '...'.$quote.'...' : $quote.'...';
                    }
                }
                $this->view->pages = $pages;
                $this->view->pages_count = count($pages);
                $this->view->total_count = $this->view->pages_count + $this->view->items_count;
                $this->view->quotes = $quotes;                
                $this->view->search_params = $param;   
            } else {
                $this->view->total_count = 0;
            }            
            $this->view->search_params = htmlspecialchars($this->_getParam('text'));
		} else {
			$this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('reset'=>TRUE), 'index', 'index'));
		}
    }
}
