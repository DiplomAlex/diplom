<?php

class Shop_View_Helper_Item_BundleDrawPrice extends Zend_View_Helper_Abstract 
{
    
    public function item_BundleDrawPrice(Model_Object_Interface $bundle = NULL)
    {
        if ($bundle === NULL) {
            return $this;
        }
        
        $this->view->bundle = $bundle;
        
        $minPrice = -1;
        $maxPrice = 0;
        foreach ($bundle->subitems as $sub) {
            if ($minPrice == -1) {
                $minPrice = (float) $sub->price;
            }
            else {
                $minPrice = min( (float) $sub->price, $minPrice);
            }
            $maxPrice = max( (float) $sub->price, $maxPrice);
        }
        $this->view->minPrice = $minPrice;
        $this->view->maxPrice = $maxPrice;
        
        return $this->view->render('catalog/item/bundle/front-price.phtml');
    } 
    
}
