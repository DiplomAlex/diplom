<?php

class View_Helper_Box_Language extends Zend_View_Helper_Abstract
{
    
    public function box_Language()
    {
        $service = Model_Service::factory('language');
        $this->view->languages = $service->getAllActive();
        $this->view->currentLanguage = $service->getCurrent();
        return $this->view->render('box/language.phtml');
    }
    
}