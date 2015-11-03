<?php

require_once 'App/Model/Mapper/Db/Plugin/Interface.php';

class Model_Mapper_Db_Plugin_Abstract implements Model_Mapper_Db_Plugin_Interface
{

    /**
     * @var Model_Mapper_Db_Interface
     */
    protected $_mapper = NULL;

    /**
     * checks if plugin has table and should be used when building simple objct
     */
    protected $_hasTable = TRUE;

    /**
     * @var Model_Db_Table_Abstract
     */
    protected $_table = NULL;


    public function __construct()
    {
    }


    public function hasTable()
    {
        return $this->_hasTable;
    }

    /**
     * getter for _table
     * @return Model_Db_Table_Abstract
     */
    public function getTable()
    {
        if ($this->_table === NULL) {
            $this->_throwException('table should be set previously');
        }
        return $this->_table;
    }


    /**
     * setter for _table
     * @param Model_Db_Table_Abstract
     * return $this
     */
    public function setTable(Model_Db_Table_Abstract $table)
    {
        $this->_table = $table;
        return $this;
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
     * runs after saving object (mapper::save)
     */
    public function onAfterSave(Model_Object_Interface $obj,array $values, $isNew = FALSE)
    {
        return $obj;
    }


    /**
     * mapper::saveComplex
     */
    public function onAfterSaveComplex(Model_Object_Interface $obj, array $values, $isNew = FALSE)
    {
        return $obj;
    }


    public function onBeforeSave(Model_Object_Interface $obj, array $values, $isNew = FALSE)
    {
        return $obj;
    }

    public function onBeforeSaveComplex(Model_Object_Interface $obj, array $values, $isNew = FALSE)
    {
        return $obj;
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
     * @param Zend_Db_Select
     * @return Zend_Db_Select
     */
    public function onFetchComplex(Zend_Db_Select $select)
    {
        return $select;
    }

    public function onBeforeDelete(Model_Object_Interface $object)
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
    public function onMapObject(Model_Object_Interface $obj, array $values, $addedPrefix = TRUE)
    {
        return $obj;
    }


    /**
     * transform select query only when calling from pagination
     * @param Zend_Db_Select
     * @return Zend_Db_Select
     */
    public function onPagination(Zend_Db_Table_Select $select)
    {
        return $select;
    }

    protected function _throwException($msg)
    {
        throw new Model_Mapper_Db_Plugin_Exception(get_class($this).' says :'.$msg);
    }

}
