<?php

class View_Helper_Page_ByCode extends Zend_View_Helper_Abstract
{

    public function page_ByCode($code)
    {
        $page = Model_Service::factory('page')->getByCode($code);
        return $page;
    }

}