<?php

class View_Helper_Page_HrefByCode extends Zend_View_Helper_Abstract
{

    public function page_HrefByCode($code, $fullLink = FALSE, $attribs = '')
    {
        try {
            $page = Model_Service::factory('page')->getByCode($code);
        }
        catch (Exception $e) {
            $page = FALSE;
        }
        if ($page) {
            $href = $this->view->url(array('seo_id'=>$page->seo_id), 'page');
        }
        else {
            $href = '';
        }
        if (($href != '') AND ($fullLink)) {
            $result = '<a href="'.$href.'" '.$attribs.'>'.$page->title.'</a>';
        }
        else {
            $result = $href;
        }
        return $result;
    }

}