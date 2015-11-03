<?php

class View_Helper_AdminBox_Copyright extends Zend_View_Helper_Abstract
{

    public function adminBox_Copyright()
    {
        return $this->view->partial('admin-box/copyright.phtml');
    }

}