<?php

class Catalog_View_Helper_Attribute_CodeByAlias extends Zend_View_Helper_Abstract
{
    
    public function attribute_CodeByAlias($alias)
    {
        return Model_Service::factory('catalog/attribute')->getCodeByAlias($alias);
    }
    
}
