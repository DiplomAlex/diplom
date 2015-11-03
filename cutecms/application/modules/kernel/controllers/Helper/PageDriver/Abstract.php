<?php

class Controller_Action_Helper_PageDriver_Abstract extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * session of linked controller
     * @var Zend_Session_Namespace
     */
    protected $_session = NULL;
    
    /**
     * dependency injections container
     * @var App_DIContainer
     */
    protected $_injector = NULL;

    /**
     * @var array
     */
    protected $_defaultInjections = array();

    /**
     * inner options of page driver
     * @var array
     */
    protected $_options = array();
    
    
    /**
     * session getter
     */
    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace($this->_getSessionNamespaceName());
        }
        return $this->_session;
    }
    
    /**
     * injector getter
     */
    public function getInjector()
    {
        if ($this->_injector === NULL) {
            $this->_injector = new App_DIContainer;
        }
        if (( ! $this->_injector->count()) AND count($this->_defaultInjections)) {
            foreach ($this->_defaultInjections as $iface=>$class) {
                $this->_injector->inject($iface, $class);
            }
        }
        return $this->_injector;
    }

    /**
     * throw exception in this class style
     * @param string $message
     * @throws Zend_Controller_Action_Exception
     */
    protected function _throwException($message)
    {
        $message = 'class '.get_class($this).' says: '.$message;
        throw new Zend_Controller_Action_Exception($message);
    }

    /**
     * setter for al options
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key=>$value) {
            $this->setOption($key, $value);
        }
        return $this;
    }
    
    /**
     * setter for one option
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setOption($key, $value)
    {
        if (array_key_exists($key, $this->_options)) {
            $this->_options[$key] = $value;
        }
        else {
            $this->_throwException('Trying to set value ('.$value.') to unknown option - "'.$key.'"');
        }
        return $this;
    }
    
    /**
     * return all current options
     */
    protected function getOptions()
    {
        return $this->_options;
    }

    /**
     * get one option
     * @param string $key
     * @return mixed
     */
    public function getOption($key)
    {
        if ( ! array_key_exists($key, $this->_options)) {
            $this->_throwException('Trying to get value of unknown option - "'.$key.'"');
        }
        $method = 'get'.ucfirst($key);
        if (method_exists($this, $method)) {
            $result = $this->{$method}();
        }
        else {
            $result = $this->_options[$key];
        }
        return $result;
    }
    
    /**
     * function called when _helper->PageDriverHelper is resuested in controller
     * @param array $options
     */
    public function direct(array $options)
    {
        $this->setOptions($options);
        return $this;
    }
    
    /**
     * process xml http request (ajax)
     * @return string response
     */
    public function ajaxAction()
    {
    }
    
    /**
     * process usual http request
     * @return string response
     */
    public function action()
    {
    }
    
    /**
     * checks if helper can process current ajax request
     * @return bool
     */
    public function isAjaxAccepted()
    {
        return FALSE;
    }

}