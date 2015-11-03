<?php

class Issues_View_Helper_Issue_StatusHistoryBox extends Zend_View_Helper_Abstract
{

    public function issue_StatusHistoryBox(array $history)
    {
        $xhtml = $this->view->partial('box/status-history.phtml', array('history'=>$history));
        return $xhtml;
    }

}