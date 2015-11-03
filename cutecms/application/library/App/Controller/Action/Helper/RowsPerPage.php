<?php

/**
 * remembers number of rows per page for controllers list
 * for direct method - if number was remembered - it is returned,
 * otherwise - if value in config exists - it is returned
 * otherwise - self constant value is returned
 */


class App_Controller_Action_Helper_RowsPerPage extends Zend_Controller_Action_Helper_Abstract
{

    const DEFAULT_VALUE = 20;

    protected $_sessionNamespace = NULL;

    protected function _session()
    {
        if ($this->_sessionNamespace === NULL) {
            $this->_sessionNamespace = new Zend_Session_Namespace(__CLASS__);
            $this->_sessionNamespace->values = array();
            if (Model_Service::factory('user')->isAuthorized()) {
                $this->_sessionNamespace->values = unserialize(Model_Service::factory('user')->getCurrent()->rows_per_page);
            }
        }
        return $this->_sessionNamespace;
    }

    public function direct()
    {
        return $this->getValue();
    }

    public function getValue()
    {
        $key = $this->_getKey();
        if (PHP_SAPI == 'cli') {
            $value = $this->_getFromConfig();
        }
        else if (isset($this->_session()->values[$key])) {
            $value = $this->_session()->values[$key];
        }
        else {
            $value = $this->_getFromConfig();
        }
        return $value;
    }

    protected function _getFromConfig()
    {
        if (Zend_Registry::isRegistered('config') AND isset(Zend_Registry::get('config')->default->paginator->rowsPerPage)) {
            $value = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        else {
            $value = self::DEFAULT_VALUE;
        }
        return $value;
    }

    protected function _getKey()
    {
        return $this->getRequest()->getModuleName() . '_' . $this->getRequest()->getControllerName() . '_' . $this->getRequest()->getActionName();
    }

    public function setValue($value)
    {
        if ( (int) $value > 0) {
            $key = $this->_getKey();
            $this->_session()->values[$key] = $value;
        }
        return $this;
    }

    public function saveValue($value = NULL)
    {
        if (($value !== NULL) OR ($value = $this->getRequest()->getParam('rows_per_page'))) {
            $this->setValue($value);
            Model_Service::factory('user')->saveRowsPerPage($this->_session()->values);
        }
        return $this;
    }

}