<?php

class Catalog_View_Helper_Item_IsInStock extends Zend_View_Helper_Abstract
{

    public function item_IsInStock(Model_Object_Interface $item)
    {
        if ($item->stock_qty > 0) {
            $result = $this->view->translate('Есть на складе');
        }
        else {
            $result = $this->view->translate('Временно отсутствует');
        }
        return $result;
    }

}