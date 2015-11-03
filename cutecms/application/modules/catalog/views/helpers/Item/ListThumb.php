<?php

class Catalog_View_Helper_Item_ListThumb extends Zend_View_Helper_Abstract
{

    public function item_ListThumb(Model_Object_Interface $item)
    {
        $this->view->item  = $item;
        return $this->view->render('item/list-thumb.phtml');
    }

}