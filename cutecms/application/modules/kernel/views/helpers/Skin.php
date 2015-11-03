<?php

class View_Helper_Skin extends Zend_View_Helper_Abstract
{

    /**
     * @var string skin name
     */
    protected static $_skin = NULL;

    /**
     * main helper function returns $this
     *
     * @param string set skin to value
     */
    public function skin($value = NULL)
    {
        if ($value !== NULL) {
            self::$_skin = $value;
        }
        return $this;
    }

    /**
     * lazy init skin
     * @return string
     */
    protected function _getSkin()
    {
        if (self::$_skin === NULL) {
            if (Zend_Registry::isRegistered('skin')) {
                $skin = Zend_Registry::get('skin');
            }
            else {
                $skin = Model_Service::factory('site')->getCurrent()->skin;
            }            
            self::$_skin = $this->_addLanguageToSkin($skin);
        }
        return self::$_skin;
    }
        
    protected function _addLanguageToSkin($skin)
    {
        $langService = Model_Service::factory('language');
        $curr = $langService->getCurrent();
        if ($curr->id != $langService->getDefault()->id) {
            $skin .= '_'.$curr->code2;
        } 
        return $skin;
    }
    
    

    /**
     * return skin name when trying to render
     * @return string
     */
    public function __toString()
    {
        return $this->_getSkin();
    }

    /**
     * return url for skin
     * @return string
     */
    public function url()
    {
        if (substr(APPLICATION_BASE, -1, 1) !== '/') {
            $slash = '/';
        }
        else {
            $slash = '';
        }
        $url = $this->view->serverUrl(APPLICATION_BASE . $slash . 'skins/'.$this->_getSkin()).'/';
        return $url;
    }

}