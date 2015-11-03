<?php

class View_Helper_Box_Copyright extends Zend_View_Helper_Abstract
{

    public function box_Copyright()
    {
        return $this->view->partial('box/copyright.phtml');
    }

}