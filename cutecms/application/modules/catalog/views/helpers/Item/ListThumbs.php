<?php

class Catalog_View_Helper_Item_ListThumbs extends Zend_View_Helper_Abstract
{

    /**
     * @param mixed  -  Model_Collection_Interface | Zend_Paginator
     */
    public function item_ListThumbs(/*Model_Collection_Interface*/ $items)
    {
        $this->view->items = $items;
        return $this->view->render('item/list-thumbs.phtml');//, array('items'=>$items));
    }

}