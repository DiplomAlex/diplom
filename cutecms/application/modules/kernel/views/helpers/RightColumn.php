<?php

class View_Helper_RightColumn extends Zend_View_Helper_Abstract
{

    public function rightColumn()
    {
        return  $this->view->render('right_column.phtml');
    }

}

