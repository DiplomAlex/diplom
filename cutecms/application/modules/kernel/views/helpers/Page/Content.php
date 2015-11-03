<?php

class View_Helper_Page_Content extends Zend_View_Helper_Abstract
{
    public function page_Content($seoId, $innerHtmlOnly = FALSE)
    {
        $page = Model_Service::factory('page')->get($seoId);
        if ($innerHtmlOnly === TRUE) {
            $html = $page->full;
        }
        else {
            $this->view->page = $page;
            $html = $this->view->render('page/detailed.phtml');
        }
        return $html;
    }
    
}