<?php

require_once 'App/Model/Mapper/XML/Plugin/Interface.php';

class Model_Mapper_XML_Plugin_Abstract implements Model_Mapper_XML_Plugin_Interface
{

    /**
     * @var Model_Mapper_Db_Interface
     */
    protected $_mapper = NULL;


    public function __construct()
    {
    }


    /**
     * setter for _mapper
     * @param Model_Mapper_Db_Interface
     * @return $this
     */
    public function setMapper(Model_Mapper_Db_Interface $mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

    /**
     * getter for _mapper
     * @return Model_Mapper_Db_Interface
     */
    protected function _getMapper()
    {
        if ($this->_mapper === NULL) {
            throw new Model_Mapper_Db_Plugin_Exception('mapper should be set previously');
        }
        return $this->_mapper;
    }

    public function getMapper()
    {
        return $this->_getMapper();
    }



	/**
	 * run event specific method if present
	 */
	public function triggerEvent($event, array $params = NULL)
	{
		$methodName = 'on'.ucfirst($event);
		if (method_exists($this, $methodName)) {
			$result = call_user_func_array(array($this, $methodName), $params);
		}
		else {
			$result = $params[0];
		}
		return $result;
	}


    /**
     * map fields to object
     * @param Model_Object_Interface object itself
     * @param array values to map
     * @return Model_Object_Interface
     */
    public function onBuildComplex(Model_Object_Interface $object, array $values)
    {
        return $object;
    }




    /**
     * unmap plugin fields of object
     * @param Model_Object_Interface object itself
     * @param array values prepared before this plugin
     * @return array
     */
    public function onUnmapObject(Model_Object_Interface $obj, array $values)
    {
        return $values;
    }


    /**
     * map plugin fields of object
     * @param Model_Object_Interface object itself
     * @param array values prepared before this plugin
     * @return Model_Object_Interface
     */
    public function onMapObject(Model_Object_Interface $obj, array $values)
    {
        return $obj;
    }



    protected function _throwException($msg)
    {
        throw new Model_Mapper_XML_Plugin_Exception(get_class($this).' says :'.$msg);
    }

}
