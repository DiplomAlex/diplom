<?php

class Catalog_View_Helper_Item_IsConfigurable extends Zend_View_Helper_Abstract
{

    public function item_IsConfigurable(Model_Object_Interface $item)
    {
        if ($item->is_configurable) {
            $result = $this->view->translate('да');
        }
        else {
            $result = '';
        }
        return $result;
    }
    
}