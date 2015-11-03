<?php

class Shop_View_Helper_Box_Items extends Zend_View_Helper_Abstract
{
    public function box_Items($page, $param, $page_type)
    {
        if ($page_type == 'search'){
            $stripTags = new Zend_Filter_StripTags();
            $param = $stripTags->filter($param);

            
            $htmlEntities = new Zend_Filter_HtmlEntities();            
            $param = $htmlEntities->filter($param);
            
            $param = mb_strtolower($param, 'UTF-8');
            
            $items = array();
            foreach(Model_Service::factory('catalog/item')->getByText($param, $page, 56) as $item) {
                $full = strip_tags($item->full);
                $full = preg_replace('/\&[^;]*;/', '', $full);
               
                $full = mb_substr_count(mb_strtolower($full, 'UTF-8'), $param);  
                $name = mb_substr_count(mb_strtolower($item->name, 'UTF-8'), $param);
                $price = mb_substr_count(mb_strtolower($item->price, 'UTF-8'), $param);
                
                if ( $full || $name || $price){                        
                    $items[] = $item;
                }
            }
            
            $galleries = array();
            foreach ($items as $item){
                $galleries[$item->id] = Model_Service::factory('catalog/item')->getHelper('Gallery')->getGalleryWithActiveImage($item['id']);
            }
            
            $this->view->galleries = $galleries;   
            $this->view->items = $items;
        } elseif($page_type == 'category'){

            $category = Model_Service::factory('catalog/category')->get($param);
			
            $galleries = array();
			$items = array();			
            foreach (Model_Service::factory('catalog/item')->getAllActiveByCategory($param, $page, 56, true) as $item){
				$item->category_seo_id = $category->seo_id;
				$items[] = $item;
                $galleries[$item->id] = Model_Service::factory('catalog/item')->getHelper('Gallery')->getGalleryWithActiveImage($item['id']);
            }
			
			$this->view->items = $items;
            $this->view->galleries = $galleries;     
        }
        return $this->view->render('box/items.phtml');
    }

}