<?php

class View_Helper_Page_SeoIdByCode extends Zend_View_Helper_Abstract
{

    public function page_SeoIdByCode($code)
    {
        $page = Model_Service::factory('page')->getByCode($code);
        return $page->seo_id;
    }

}