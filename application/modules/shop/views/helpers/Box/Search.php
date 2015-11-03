<?php

class Shop_View_Helper_Box_Search extends Zend_View_Helper_Abstract
{

    public function box_Search()
    {
        return $this->view->render('box/search.phtml');
    }

}