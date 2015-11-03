<?php

class Catalog_View_Helper_Bundle_CodeByAlias extends Zend_View_Helper_Abstract
{
    
    public function bundle_CodeByAlias($alias)
    {
        return Model_Service::factory('catalog/item-bundle')->getCodeByAlias($alias);
    }
    
}