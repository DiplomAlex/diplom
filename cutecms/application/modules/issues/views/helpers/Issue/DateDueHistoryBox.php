<?php

class Issues_View_Helper_Issue_DateDueHistoryBox extends Zend_View_Helper_Abstract
{

    public function issue_DateDueHistoryBox(array $history)
    {
        $xhtml = $this->view->partial('box/date-due-history.phtml', array('history'=>$history));
        return $xhtml;
    }

}