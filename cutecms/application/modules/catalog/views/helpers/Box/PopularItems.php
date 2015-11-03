<?php

class Catalog_View_Helper_Box_PopularItems extends Zend_View_Helper_Abstract
{

    public function box_PopularItems()
    {
        $this->view->items = Model_Service::factory('catalog/item')->getPopular();
        $html = $this->view->render('box/popular-items.phtml');
        return $html;
    }

}