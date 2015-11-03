<?php

class Catalog_View_Helper_Box_AlternateItems extends Zend_View_Helper_Abstract
{

    public function box_AlternateItems(Model_Object_Interface $item)
    {
        $limit = Zend_Registry::get('catalog_config')->box->alternateItems->limit;
        $this->view->items = Model_Service::factory('catalog/item')->getAlternates($item, $limit);
        $html = $this->view->render('box/alternate-items.phtml');
        return $html;
    }

}