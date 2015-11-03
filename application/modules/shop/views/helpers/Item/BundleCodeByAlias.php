<?php

class Shop_View_Helper_Item_BundleCodeByAlias extends Zend_View_Helper_Abstract
{
    
    public function item_BundleCodeByAlias($alias)
    {
        return Model_Service::factory('catalog/item-bundle')->getCodeByAlias($alias);
    }
    
}
