<?php

class Shop_View_Helper_Catalog_Filter extends Zend_View_Helper_Abstract
{
    
    public function catalog_Filter()
    {
        /*$html = $this->view->render('catalog/index-filter.phtml');*/
        $html = $this->view->layout()->catalog_Filter;
        return $html;
    }
    
}