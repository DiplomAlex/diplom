<?php


require_once 'App/Model/Mapper/Db/Interface.php';
require_once 'App/Model/Mapper/Db/Exception.php';
require_once 'App/DIContainer.php';
require_once 'App/Model/Object/Interface.php';



class Model_Mapper_XML_Abstract implements Model_Mapper_Interface
{

	/**
     * shpritz
	 * @var @App_DIContainer
	 */
	protected $_injector = NULL;

	/**
     * substances injected by default
	 * @var array(Interface => Class)
	 */
	protected $_defaultInjections = array(
		'Model_Object_Interface',
	    'Model_Collection_Interface',
	);

	/**
	 * current model name
	 * @var string
	 */
	protected $_modelName = NULL;

	/**
	 * self subclass name (Page/User/etc. when class name is Model_Mapper_Db_Page/Model_Mapper_Db_User/...)
	 * @var string
	 */
	protected $_subclassName = NULL;



	/**
	 * @var array(name => plugin, ...)
	 */
	protected $_plugins = array();



	public function __construct()
	{
		$this->injectDefaults();
		$this->init();
	}

	public function init()
	{

	}

    protected function _throwException($message)
    {
        throw new Model_Mapper_XML_Exception(get_class($this).' throws: '.$message);
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
	 * add plugin
	 * @param Model_Mapper_Db_Plugin_Interface
	 * @return $this
	 */
	public function addPlugin($name, Model_Mapper_Db_Plugin_Interface $plugin)
	{
        $plugin->setMapper($this);
		$this->_plugins[$name] = $plugin;
		return $this;
	}

    /**
     * remove plugin
     * @param string
     * @return $this
     */
    public function removePlugin($name)
    {
        $this->_plugins[$name] = NULL;
        unset($this->_plugins[$name]);
        return $this;
    }

	/**
	 * get plugin
	 * @param string
	 * @return Model_Mapper_Db_Plugin_Interface
	 */
	public function getPlugin($name)
	{
		if ( ! isset($this->_plugins[$name])) {
			$this->_throwException('no plugin '.$name.' installed for class '.get_class($this));
		}
		return $this->_plugins[$name];
	}

    /**
     * checks for plugin presence
     * @param string
     * @return bool
     */
    public function hasPlugin($name)
    {
        return isset($this->_plugins[$name]);
    }

    /**
	 * triggers plugins event
	 * @param string
	 * @param array
	 * @return mixed
	 */
	public function triggerPlugins($event, array $params = NULL)
	{
        $result = $params[0];
		foreach ($this->_plugins as $plugin) {
			$result = $plugin->triggerEvent($event, $params);
		}
		return $result;
	}

	/**
	 * lazy init model name
	 * @return string
	 */
	public function getModelName()
	{
		if ($this->_modelName === NULL) {
			$arr = explode('_', get_class($this));
			$this->_modelName = array_pop($arr);
		}
		return $this->_modelName;
	}


	/**
	 * lazy init mapper of model (current model is default)
	 * @param string - model name
	 */
	public function getMapper($name)
	{
        $nameArr = explode('/', $name);
        if (count($nameArr) > 1) {
            $prefix = ucfirst($nameArr[0]).'_';
            $name = $nameArr[1];
        }
        else {
            $prefix = '';
        }
        /**
         * folder1_folder2_class-smth-else   should became   Folder1_Folder2_ClassSmthElse
         */
        $name = str_replace(' ', '', ucwords(str_replace('-',' ', $name)));
        $name = str_replace(' ', '_', ucwords(str_replace('_',' ', $name)));
        $className = $prefix.'Model_Mapper_XML_'.$name;
        if (empty($name) OR ($className == get_class($this))) {
            $this->_throwException('you trying to call getMapper() for itself - use simply $this instead of $this->getMapper() in that case or set another mapper name');
        }
		return $this->getInjector()->getObject($className);
	}


    /**
     * make array from object
     * @param Model_Object_Interface
     * @return array
     */
    public function unmapSimpleObject(Model_Object_Interface $object)
    {
        $values = array();
        foreach ($object->getElements() as $elem=>$value) {
            $values[$elem] = $value;
        }
        $this->triggerPlugins(Model_Mapper_Interface::EVENT_UNMAP_OBJECT, array($object, &$values));
        return $values;
    }


    public function unmapCollectionToXML(Model_Collection_Interface $coll, $withRoot = TRUE)
    {
        if ($withRoot === TRUE) {
            $xml = "<collection class=\"".get_class($coll)."\">\n";
        }
        else {
            $xml = '';
        }
        foreach ($coll as $obj) {
            $xml .= "<object class=\"".get_class($obj)."\">\n";
            foreach ($obj->getElements() as $field=>$value) {
                if ($value instanceof Model_Collection_Interface) {
                    $xml .= "<$field class=\"".get_class($value)."\">\n".$this->unmapCollectionToXML($value, FALSE)."</$field>\n";
                }
                else if (is_array($value)) {
                    $value = str_replace(']]>', ']]]]><![CDATA[>', serialize($value));
                    $xml .= "<$field class=\"array\"><![CDATA[$value]]></$field>\n";
                }
                else {
                    $value = str_replace(']]>', ']]]]><![CDATA[>', $value);
                    $xml .= "<$field><![CDATA[$value]]></$field>\n";
                }
            }
            $xml .= "</object>\n";
        }
        if ($withRoot === TRUE) {
            $xml .= "</collection>";
        }
        return $xml;
    }

    public function unmapObjectToXML(Model_Object_Interface $obj)
    {
        $xml = "<object class=\"".get_class($obj)."\">\n";
        foreach ($obj->getElements() as $field=>$value) {
            if ($value instanceof Model_Collection_Interface) {
                $xml .= "<$field class=\"".get_class($value)."\">\n".$this->unmapCollectionToXML($value, FALSE)."</$field>\n";
            }
            else if (is_array($value)) {
                $value = str_replace(']]>', ']]]]><![CDATA[>', serialize($value));
                $xml .= "<$field class=\"array\"><![CDATA[$value]]></$field>\n";
            }
            else {
                $value = str_replace(']]>', ']]]]><![CDATA[>', $value);
                $xml .= "<$field><![CDATA[$value]]></$field>\n";
            }
        }
        $xml .= "</object>\n";
        return $xml;
    }




    /**
     * @param string suffix for make method
     * @param mixed string|array|SimpleXMLElement
     * @return Model_Collection_Interface
     */
    protected function _makeCollection($style, $xml)
    {
        /** this method can recieve empty data and create empty collection
         *
        if (empty($data)) {
            throw new Model_Mapper_Db_Exception('_makeCollection recieved empty $data - check for previous select query ');
        }
        */
        if (empty($xml) OR ( ! $attrs = $xml->attributes()) OR ( ! $collClass = $attrs['class']) OR empty($collClass)) {
            $collClass = 'Model_Collection_Interface';
        }
        $coll = $this->getInjector()->getObject($this->getInjector()->getInjectionKey($collClass));
        if ( ! empty($xml)) {
            foreach ($xml->children() as $row) {
                $coll->add($this->{'make'.ucfirst($style).'Object'}($row));
            }
        }
        return $coll;
    }


    /**
     * builds objects from array
     * @param array( row1 => array(), array())
     * @return Model_Collection_Interface
     */
    public function makeComplexCollection($xml)
    {
        return $this->_makeCollection('complex', $xml);
    }

    /**
     * builds objects from array
     * @param array( row1 => array(), array())
     * @return Model_Collection_Interface
     */
    public function makeSimpleCollection($xml)
    {
        return $this->_makeCollection('simple', $xml);
    }

    /**
     * builds objects from array
     * @param array( row1 => array(), array())
     * @return Model_Collection_Interface
     */
    public function makeCustomCollection($xml)
    {
        return $this->_makeCollection('custom', $xml);
    }


    /**
     * mapper from xml or array row:
     * creates object and populates it with values
     * mapped automatically as $objFieldName => $values[$objFieldName]
     *
     * @param mixed array|SimpleXMLElement $xml
     * @return Model_Object_Interface
     */
    public function makeSimpleObject($xml, $addedPrefix = TRUE)
    {
        $attrs = $xml->attributes();
        if (( ! $objClass = $attrs['class']) OR empty($objClass)) {
            $objClass = 'Model_Object_Interface';
        }
        
        $object = $this->getInjector()->getObject($this->getInjector()->getInjectionKey($objClass));
        foreach ($object->getElements() as $name=>$val) {
            if (($xml->{$name}) AND (count($xml->{$name}->children()))) {
                if ($this->_isCollection($xml->{$name})) {
                    $object->{$name} = $this->makeSimpleCollection($xml->{$name});
                }
                else if ($this->_isArray($xml->{$name})) {
                    $object->{$name} = unserialize($xml->{$name});
                }
                else {
                    $object->{$name} = $this->makeSimpleObject($xml->{$name});
                }
            }
            else {
                $object->{$name} = (string) $xml->{$name};
            }
        }
        return $object;
    }

    protected function _isCollection($xml)
    {
        $result = FALSE;
        if ( ! empty($xml) AND ($attrs = $xml->attributes()) AND ($class = $attrs['class']) AND (strstr($class, 'Model_Collection'))) {
            $result = TRUE;
        }
        return $result;
    }


    protected function _isArray($xml)
    {
        $result = FALSE;
        if ( ! empty($xml) AND ($attrs = $xml->attributes()) AND ($class = $attrs['class']) AND ($class=='array')) {
            $result = TRUE;
        }
        return $result;
    }


    /*
     * build object with subobjects
     * @param string xml
     * @return Model_Object_Interface
     */
    public function makeComplexObject($xml, $addedPrefix = TRUE)
    {
        $object = $this->makeSimpleObject($xml, $addedPrefix);

        $object->setIsComplex();

        $debug_objectClass = get_class($object);
        $debug_object = $object;

        /*$object = $this->triggerPlugins(Model_Mapper_XML_Plugin_Interface::EVENT_BUILD_COMPLEX, array($object, $values, $addedPrefix));*/


        if ($object === NULL) {
            $errorText = 'error in '.get_class($this).'::'.__FUNCTION__.' - object of '.$debug_objectClass.' became NULL after triggerPlugins(buildComplex,...) '
                     .'it looks like one of plugins('.implode(',', array_keys($this->_plugins)).') has wrong trigger onBuildComplex (it ought to return Model_Object_Interface)';
            if (APPLICATION_ENV == 'development') {
                Zend_Debug::dump($errorText);
                debug_print_backtrace();
                exit;
            }
            $this->_throwException($errorText);
        }


        $object = $this->_onBuildComplexObject($object, $xml, $addedPrefix);

        return $object;
    }

    public function makeCustomObject($xml)
    {
        $object = $this->getInjector()->getObject('Model_Object_Interface');
        foreach ($xml as $field=>$value) {
            if ($object->hasElement($field)) {
                $object->{$field} = $value;
            }
        }
        $object->setMappingStyle(Model_Object_Interface::STYLE_CUSTOM);
        return $object;
    }

    public function setObjectFromValues(Model_Object_Interface $object, array $values)
    {
        foreach ($values as $key=>$val) {
            if ($object->hasElement($key)) {
                $object->{$key} = $val;
            }
        }
        return $object;
    }


}
