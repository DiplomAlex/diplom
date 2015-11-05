<?php

class Lab_View_Helper_Box_Footer extends Zend_View_Helper_Abstract
{

    public function box_Footer()
    {
        return $this->view->render('box/footer.phtml');
    }

}