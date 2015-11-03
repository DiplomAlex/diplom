<?php

class App_Controller_Action_Helper_ReturnUrl extends Zend_Controller_Action_Helper_Abstract
{

    protected static $_sessionNamespace = NULL;

    protected static $_validated = FALSE;

    /**
     * return url in session start
     */

    protected function _session()
    {
        if (self::$_sessionNamespace === NULL) {
            self::$_sessionNamespace = new Zend_Session_Namespace(__CLASS__);
        }
        $this->_validate();
        self::$_sessionNamespace->controllerClass = get_class($this->getActionController());
        return self::$_sessionNamespace;
    }

    protected function _validate()
    {
        if (self::$_validated === TRUE) {
            return $this;
        }
        self::$_validated = TRUE;
        if (self::$_sessionNamespace === NULL) {
            return $this;
        }
        if (self::$_sessionNamespace->controllerClass != get_class($this->getActionController())) {
            self::$_sessionNamespace->returnUrl = NULL;
        }
        return $this;
    }


    public function validate()
    {
        $this->_session();
        return $this;
    }

    /**
     * @return $this
     */
    public function remember()
    {
        $this->_session()->returnUrl = $this->getActionController()->view->serverUrl(TRUE);
        return $this;
    }

    /**
     * @return string
     */
    public function get()
    {
        $url = $this->_session()->returnUrl;
        if ( ! $url) {
            return FALSE;
        }
        return $url;
    }

    /**
     * return url in session end
     */

}