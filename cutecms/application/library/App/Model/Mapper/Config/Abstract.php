<?php

class Model_Mapper_Config_Abstract implements Model_Mapper_Config_Interface
{


    /**
     * shpritz
     * @var @App_DIContainer
     */
    protected $_injector = NULL;

    protected $_defaultInjections = array(
        'Model_Object_Interface',
        'Model_Collection_Interface',
    );

    public function __construct()
    {
        $this->injectDefaults();
        $this->init();
    }

    public function init()
    {

    }


    /**
     * @return $this
     */
    public function injectDefaults()
    {
        foreach ($this->_defaultInjections as $interface=>$class) {
            $this->getInjector()->inject($interface, $class);
        }
    }


    /**
     * @return App_DIContainer
     */
    public function getInjector()
    {
        if ($this->_injector === NULL) {
            $this->_injector = new App_DIContainer($this);
        }
        return $this->_injector;
    }


    public function makeSimpleObject(Zend_Config $conf)
    {
        $obj = $this->getInjector()->getObject('Model_Object_Interface');
        foreach ($conf as $key=>$row) {
            $obj->$key = $row;
        }
        return $obj;
    }

    public function makeComplexObject(Zend_Config $conf)
    {
        return $this->makeSimpleObject($conf);
    }

    public function makeCustomObject(Zend_Config $conf)
    {
        return $this->makeSimpleObject($conf);
    }

    public function makeSimpleCollection(Zend_Config $conf)
    {
        $coll = $this->getInjector()->getObject('Model_Collection_Interface');
        foreach ($conf as $key=>$row) {
            $coll->add($this->makeSimpleObject($row));
        }
        return $coll;
    }

    public function makeComplexCollection(Zend_Config $conf)
    {
        $coll = $this->getInjector()->getObject('Model_Collection_Interface');
        foreach ($conf as $key=>$row) {
            $coll->add($this->makeComplexObject($row));
        }
        return $coll;
    }

    public function makeCustomCollection(Zend_Config $conf)
    {
        $coll = $this->getInjector()->getObject('Model_Collection_Interface');
        foreach ($conf as $key=>$row) {
            $coll->add($this->makeCustomObject($row));
        }
        return $coll;
    }

}