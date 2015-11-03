<?php

class View_Helper_Banner extends Zend_View_Helper_Abstract
{

    protected static $_scriptPrepared = FALSE;
    
    protected $_service = NULL;

    public function banner($place)
    {
        if ($banner = $this->_getService()->getRandomByPlace($place)) {
            $html = $this->view->partial('banner/main.phtml', array('banner'=>$banner, 'scriptPrepared'=>self::$_scriptPrepared));
            self::$_scriptPrepared = TRUE;
        }
        else {
            $html = '';
        }
        return $html;
    }
    
    protected function _getService()
    {
        if ($this->_service === NULL) {
            $this->_service = Model_Service::factory('banner');
            $site = Model_Service::factory('site')->getCurrent();
            $this->_service->getHelper('Multisite')->setCurrentSiteId($site->id);
        }
        return $this->_service;
    }

}