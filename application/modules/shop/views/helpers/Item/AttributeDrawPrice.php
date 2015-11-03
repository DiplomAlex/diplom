<?php

class Shop_View_Helper_Item_AttributeDrawPrice extends Zend_View_Helper_Abstract 
{
    
    public function item_AttributeDrawPrice(Model_Object_Interface $attr = NULL)
    {
        if ($attr === NULL) {
            return $this;
        }
        
        $this->view->attr = $attr;
        return $this->view->render('catalog/item/attribute/front-price.phtml');
    } 
    
}
