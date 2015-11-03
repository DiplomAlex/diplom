<?php

class Catalog_View_Helper_Box_NewItems extends Zend_View_Helper_Abstract
{

    public function box_NewItems()
    {
        $this->view->items = Model_Service::factory('catalog/item')->getNew();
        $html = $this->view->render('box/new-items.phtml');
        return $html;
    }

}