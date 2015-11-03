<?php

class Shop_View_Helper_Item_BundleDraw extends Catalog_View_Helper_Bundle_Draw
{
    
    protected $_scriptInputRequired = 'catalog/item/bundle/input-required.phtml';
    protected $_scriptInputNotRequired = 'catalog/item/bundle/input-not-required.phtml';
    
    public function item_BundleDraw(Model_Object_Interface $bundle = NULL)
    {
        return $this->bundle_Draw($bundle); 
    }
    
    
} 