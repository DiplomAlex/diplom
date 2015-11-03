<?php

class Shop_View_Helper_Box_ShoppingCart extends Checkout_View_Helper_Box_ShoppingCart
{

    public function box_ShoppingCart($template = 'box/shopping-cart.phtml')
    {
        $attrHref = 'href'; 
        $classBasketWrap = 'shopping-cart-wrap';
        $alertBoxId = 'alert_cart_updated';
        
        $service = Model_Service::factory('checkout/cart');
        
        $this->view->totalQty = $service->calculateTotalQty();
        
        $this->view->items = Model_Service::factory('checkout/cart')->getAll();
        
        $galleries = array();
        foreach ($this->view->items as $item){
            $item = Model_Service::factory('catalog/item')->getComplexBySeoId($item->seo_id);
            $galleries[$item->seo_id] = Model_Service::factory('catalog/item')->getHelper('Gallery')->getLinkedToContent($item->id);
        }
        $this->view->galleries = $galleries;
        $html = $this->view->render($template);

        return $html;
    }
  
}
