<?php

class Catalog_View_Helper_Attribute_Type extends Zend_View_Helper_Abstract
{

    public function attribute_Type($typeId, $translated = TRUE)
    {
        $types = Model_Service::factory('catalog/attribute')->getAllTypes($translated);
        return $types[$typeId];
    }

}