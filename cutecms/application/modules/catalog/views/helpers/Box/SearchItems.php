<?php

class Catalog_View_Helper_Box_SearchItems extends Zend_View_Helper_Abstract
{

    public function box_SearchItems()
    {
        $html = $this->view->render('box/search-items.phtml');
        return $html;
    }

}