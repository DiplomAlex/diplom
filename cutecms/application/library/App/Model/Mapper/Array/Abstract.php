<?php

class Model_Mapper_Array_Abstract implements Model_Mapper_Array_Interface
{

    protected $_defaultInjections = array(
        'Model_Object_Interface',
        'Model_Collection_Interface',
    );
    protected $_injector = NULL;

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


    public function makeCustomObject(array $values)
    {
        return $this->makeSimpleObject($values);
    }

    public function makeCustomCollection(array $values)
    {
        return $this->makeSimpleCollection($values);
    }

    public function makeComplexObject(array $values)
    {
        return $this->makeSimpleObject($values);
    }

    public function makeComplexCollection(array $values)
    {
        return $this->makeSimpleCollection($values);
    }

    public function makeSimpleObject(array $values)
    {
        $obj = $this->getInjector()->getObject('Model_Object_Interface');
        $obj = $this->mapSimpleObject($obj, $values);
        return $obj;
    }

    public function mapSimpleObject(Model_Object_Interface $obj, array $values)
    {
        foreach ($values as $key=>$value) {
            if ($obj->hasElement($key)) {
                $obj->{$key} = $value;
            }
        }
        return $obj;
    }

    public function makeSimpleCollection(array $values)
    {
        $coll = $this->getInjector()->getObject('Model_Collection_Interface');
        foreach ($values as $arr) {
            $coll->add($this->makeSimpleObject($arr));
        }
        return $coll;
    }


}