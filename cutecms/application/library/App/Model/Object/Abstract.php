<?php

require_once 'App/DIContainer.php';
require_once 'App/Model/Object/Interface.php';
require_once 'App/Model/Object/Exception.php';

class Model_Object_Abstract implements Model_Object_Interface
{

	/**
	 * @var array(Model_Object_Interface)
	 */
	protected $_subobjects = array();

	/**
	 * @var App_DIContainer
	 */
	protected $_injector = NULL;

	/**
	 * @var Array
	 */
	protected $_defaultInjections = array(
	);


	/**
	 * mapping styleof object (STYLE_SIMPLE, STYLE_COMPLEX, STYLE_CUSTOM)
	 * @var bool
	 */
	protected $_mappingStyle;


	/**
	 * TODO   possible should be refactored
	 */
	protected $_deniedGetters = array(
		'value', 'element', 'elements', 'injector', 'mappingStyle', 'subObjects',
	);
	protected $_deniedSetters = array(
		'value', 'subObject', 'mappingStyle', 'isComplex',
	);



    /**
     * @var array ('name'=>'value', ...)
     */
    protected $_elements = array();

    /**
     * options of elements
     * @var array ('elem_name' => array(options), ...)
     */
    protected $_options = array();

    /**
     * filters of elements
     * @var array ('elem_name' => array(filterName=>Zend_Filter_Interface, ...), ...)
     */
    protected $_filters = array();

    /**
     * filters of elements
     * @var array ('elem_name' => array(validatorName=>Zend_Validate_Interface, ...), ...)
     */
    protected $_validators = array();



    /**
     * constructor
     */
	public function __construct()
	{
        $this->setMappingStyle(Model_Object_Interface::STYLE_SIMPLE);
		$this->_injectDefaults();
        $this->init();
	}

    public function __wakeup()
    {
        $this->_injectDefaults();
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        foreach ($this->_subobjects as $sub) {
            $sub->__destruct();
            unset($sub);
        }
        unset($this->_injector);
        /**
         * __destruct is called before(!) serialize that's why we cannot delete elements here
         *
         */
        /*unset($this->_elements);*/
    }

    public function destroy()
    {
        foreach ($this->_subobjects as $sub) {
            $sub->destroy();
            unset($sub);
        }
        unset($this->_injector);
        unset($this->_elements);
        unset($this->_filters);
        unset($this->_validators);
    }

	/**
	 * inject dependecies
	 */
	protected function _injectDefaults()
	{
		foreach ($this->_defaultInjections as $interface=>$class) {
			$this->getInjector()->inject($interface, $class);
		}
	}

	/**
	 * injector lazy init
	 */
	public function getInjector()
	{
		if ($this->_injector === NULL) {
			$this->_injector = new App_DIContainer($this);
		}
		return $this->_injector;
	}




	/**
	 * init object elements
	 */
	public function init()
	{

	}

	/**
	 * @param string element name
     * @param array options array('required' => , 'filters' => , 'validators' => , 'default' => , ...)
	 */
	public function addElement($name, $options = array())
	{
        if (isset($options['default'])) {
            $this->_elements[$name] = $options['default'];
        }
        else {
            $this->_elements[$name] = NULL;
        }
        if (isset($options['filters'])) {
            $this->_filters[$name] = $options['filters'];
        }
        else {
            $this->_filters[$name] = array();
        }
        if (isset($options['validators'])) {
            $this->_validators[$name] = $options['validators'];
        }
        else {
            $this->_validators[$name] = array();
        }
		return $this;
	}

    /**
     * adds array of elements
     */
    public function addElements(array $elements)
    {
        foreach ($elements as $name => $options) {
            if (is_numeric($name)) {
                $name = $options;
                $options = array();
            }
            $this->addElement($name, $options);
        }
    }

	/**
	 * proxie to _form addElement
	 */
    public function getElements()
	{
        return $this->_elements;
	}


    public function getElement($name)
    {
        return $this->_elements[$name];
    }


	/**
	 * returns raw value of field (without getter)
	 */
	public function getValue($name)
	{
        if ( ! $this->hasElement($name)) {
            $this->_throwException('undefined object field "'.$name.'" when trying to getValue');
        }
        return $this->_elements[$name];
	}

	/**
	 * sets element value
	 * @param string elem name
	 * @param mixed value
	 * @return Model_Object_Interface $this
	 */
	public function setValue($name, $value)
	{
        if ( ! $this->hasElement($name)) {
            $this->_throwException('undefined object field "'.$name.'" when trying to setValue');
        }
        $this->_elements[$name] = $value;
		return $this;
	}

	/**
	 * proxie to _form hasElement
	 */
	public function hasElement($name)
	{
        return array_key_exists($name, $this->_elements);
	}


	/**
	 * proxie to _form populate
	 */
	public function populate(array $values)
	{
			/*
        foreach ($this->_elements as $name=>$val) {
            if (array_key_exists($name, $values)) {
            	$this->_elements[$name] = $values[$name];
            }
        }
        */
        foreach ($values as $name=>$val) {
            if (array_key_exists($name, $this->_elements)) {
            	$this->_elements[$name] = $val;
            }
        }
		return $this;
	}

	/**
	 * alias to addSubForm
	 * @param Model_Object_Interface
	 * @param string
	 * @return Model_Object_Interface $this
	 */
	public function addSubObject(Model_Object_Interface $object, $name)
	{
		$this->_subobjects[$name] = $object;
		return $this;
	}

	/**
	 * checks subobject presence
	 * @param string
	 * @return bool
	 */
	public function hasSubObject($name)
	{
		return isset($this->_subobjects[$name]);
	}

	/**
	 * sets subobject
	 * @param Model_Object_Interface
	 * @param string
	 * @return Model_Object_Interface $this
	 */
	public function setSubObject($object, $name)
	{
		$this->_subobjects[$name] = $object;
		return $this;
	}

	/**
	 * gets all subobjects
	 * @return array('Name'=>Model_Object_Interface, ...)
	 */
	public function getSubObjects()
	{
		return $this->_subobjects;
	}

    /**
     * checks if object is valid or not
     * @return bool
     */
    public function isValid()
    {
        $valid = TRUE;
        foreach ($this->getElements() as $el => $value) {
            if ( ! $this->isValidElement($el)) {
                $valid = FALSE;
                break;
            }
        }
        if ($valid === TRUE) {
            foreach ($this->getSubObjects() as $sub) {
                if ( ! $sub->isValid()) {
                    $valid = FALSE;
                    break;
                }
            }
        }
        return $valid;
    }

    /**
     * validates one element
     * @param string element name
     * @return bool
     */
    public function isValidElement($name)
    {
        $result = TRUE;
        if (isset($this->_validators[$name])) {
            foreach ($this->_validators[$name] as $validator) {
                if ( ! $validator->isValid($this->getElement($name))) {
                    $result = FALSE;
                    break;
                }
            }
        }
        return FALSE;
    }




	/**
	 * if getter present - use it, else
	 * if element presents - get its value, otherwise - try to get subform
	 * @return mixed
	 */
	public function __get($var)
	{
		$getter = 'get'.ucfirst($var);
		if (method_exists($this, $getter) AND ( ! in_array($var, $this->_deniedGetters))) {
			return $this->$getter();
		}
        else if ($this->hasElement($var)) {
            return $this->getValue($var);
        }
        else if ($this->hasSubObject($var)) {
            return $this->_subobjects[$var];
        }
        else {
        	$this->_throwException('no such field - '.$var);
        }
	}

	/**
	 * if setter present - use it
	 * if element present - set its value otherwise try to set subform
	 * @return Model_Object_Interface $this
	 */
	public function __set($var, $value)
	{
		$setter = 'set'.ucfirst($var);
		if (method_exists($this, $setter) AND ( ! in_array($var, $this->_deniedSetters))) {
			$this->$setter($value);
		}
        else if ($this->hasElement($var)) {
            /*$this->setValue($var, $value);*/
        	$this->_elements[$var] = $value;
		}
        else if ($value instanceof Model_Object_Interface) {
        	if ($this->hasSubObject($var)) {
        		$this->setSubObject($value, $var);
        	}
        	else {
                $this->_throwException('no subobject '.$var);
        	}
        }
        else {
        	$this->_throwException('cannot set value ('.$value.') for field '.$var.'. (usually field is absent in objects init() or trying to set NULL when field is required)');
        }

	}

    protected function _throwException($message)
    {
        throw new Model_Object_Exception(get_class($this).' throws: '.$message);
    }


	/**
	 * magical transformation to array
	 * @return array
	 */
	public function __toArray() {
		return $this->toArray();
	}

	/**
	 * make an array from values of elements
	 * @return array
	 */
	public function toArray() {
        $arr = array();
		$elems = $this->getElements() ;
        foreach ($elems as $name=>$value) {
            $arr[$name] = $this->$name;
        }
        $subs = $this->getSubObjects();
        foreach ($subs as $objName=>$obj) {
            if (($obj instanceof Model_Object_Interface) OR ($obj instanceof Model_Collection_Interface)) {
                $arr[$objName] = $obj->toArray();
            }
        }
		return $arr;
	}

	/**
	 * trigger event
	 * @param string event name
	 * @return Model_Object_Interface $this
	 */
	public function trigger($event)
	{
        $method = 'on'.ucfirst($event);
        $event = App_Event::factory('Model_Object__trigger__'.$method, array($this))->dispatch();
        $event->__destruct();
        $event = App_Event::factory(get_class($this).'__trigger__'.$method, array($this))->dispatch();
        $event->__destruct();
		if (method_exists($this, $method)) {
			$this->$method();
		}

		return $this;
	}





	/**
	 * @return bool
	 */
	public function isComplex()
	{
		return (bool) ($this->getMappingStyle() === Model_Object_Interface::STYLE_COMPLEX);
	}

    /**
     * @return bool
     */
    public function isSimple()
    {
        return (bool) ($this->getMappingStyle() === Model_Object_Interface::STYLE_SIMPLE);
    }

    /**
     * @return bool
     */
    public function isCustom()
    {
        return (bool) ($this->getMappingStyle() === Model_Object_Interface::STYLE_CUSTOM);
    }

	/**
	 * @return Model_Object_Interface $this
	 */
	public function setIsComplex()
	{
        $this->setMappingStyle(Model_Object_Interface::STYLE_COMPLEX);
		return $this;
	}


    /**
     * @return int current mapping style
     */
    public function getMappingStyle()
    {
        return $this->_mappingStyle;
    }

    /**
     * sets current mapping style of object
     *
     * @param int
     * @return Model_Object_Interface $this
     */
    public function setMappingStyle($style)
    {
        $this->_mappingStyle = $style;
        return $this;
    }





    /** ArrayAccess implementation start  */

    public function offsetSet($offset, $value) {
        $this->{$offset} = $value;
    }


    public function offsetExists($offset) {
        return ($this->hasElement($offset) OR ($this->hasSubObject($offset)));
    }


    public function offsetUnset($offset) {
        return FALSE;
    }


    public function offsetGet($offset) {
        return $this->{$offset};
    }

    /** ArrayAccess implementation end  */

    /**
     * magic for isset
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }


}

