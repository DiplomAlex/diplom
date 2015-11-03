<?php

class Catalog_View_Helper_Item_AttributeCodeByAlias extends Zend_View_Helper_Abstract
{
    
    public function item_AttributeCodeByAlias($alias)
    {
        return Model_Service::factory('catalog/attribute')->getCodeByAlias($alias);
    }
    
}
