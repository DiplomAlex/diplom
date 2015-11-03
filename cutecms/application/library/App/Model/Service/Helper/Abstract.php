<?php

class Model_Service_Helper_Abstract implements Model_Service_Helper_Interface
{

    /**
     * @var Model_Service_Interface
     */
    protected $_service = NULL;

    /**
     * @param Model_Service_Interface
     */
    public function __construct(Model_Service_Interface $service = NULL, array $options = NULL)
    {
        if ($service !== NULL) {
            $this->setService($service);
        }
        if ($options !== NULL) {
            $this->setOptions($options);
        }
        $this->init();        
    }
    
    /**
     * some initialization 
     */
    public function init()
    {
        
    }


    /**
     * @param string
     */
    protected function _throwException($message)
    {
        throw new Model_Service_Helper_Exception('helper '.get_class($this).' binded to service '.get_class($this->getService()).' says: ' . $message);
    }

    /**
     * bind service
     */
    public function setService(Model_Service_Interface $service)
    {
        $this->_service = $service;
        return $this;
    }

    /**
     * return binded service
     */
    public function getService()
    {
        return $this->_service;
    }
    
    /**
     * set options for class, i.e. sets values of correspondent protected properties
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key=>$val) {
            $property = '_'.$key;
            if (property_exists($this, $property)) {
                $this->{$property} = $val;
            }
            else {
                $this->_throwException('unknown option: '.$key);
            }
        }
        return $this;
    }
    
    /**
     * gets the value of single option
     * @param string $optionName
     * @return mixed
     */
    protected function _getOption($option)
    {
        $property = '_'.$option;
        if (property_exists($this, $property)) {
            $result = $this->{$property};
        }
        else {
            $this->_throwException('unknown option: '.$option);
        }
        return $result;
    }
    

}