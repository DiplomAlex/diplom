<?php

class View_Helper_Site_List extends Zend_View_Helper_Abstract
{ 
    
    public function site_List($withAll = FALSE)
    {
        $siteService = Model_Service::factory('site');
        $sites = $siteService->getAll();
        if ($withAll) {
            $list = array('0'=>$this->view->translate('All'));
        }
        else {
            $list = array();
        }
        foreach ($sites as $site) {
            $list[$site->id] = $site->specification;
        }
        return $list;
    }
    
}