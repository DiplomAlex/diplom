<?php


require_once 'App/Model/Mapper/Db/Interface.php';
require_once 'App/Model/Mapper/Db/Exception.php';
require_once 'App/DIContainer.php';
require_once 'App/Model/Db/Table/Interface.php';
require_once 'App/Model/Object/Interface.php';
require_once 'Zend/Db/Select.php';



class Model_Mapper_Db_Abstract implements Model_Mapper_Db_Interface
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
		'Model_Db_Table_Interface',
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
	 * db table object
	 * @var Model_Db_Table_Abstract
	 */
	protected $_table = NULL;



	/**
	 * @var array(name => plugin, ...)
	 */
	protected $_plugins = array();


    protected $_poolUpdate = array();
    protected $_poolInsert = array();

    const POOL_LIMIT = 10;
    protected static $_poolUpdateCounter = 0;
    protected static $_poolInsertCounter = 0;
    protected static $_fetchComplexCounter = 0;


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
        throw new Model_Mapper_Db_Exception(geT_class($this).' throws: '.$message);
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
	 * lazy init table
	 * @return Model_Db_Table_Abstract
	 */
	public function getTable($name = NULL)
	{
		if ($name === NULL) {
			if ($this->_table === NULL) {
				$this->_table = $this->getInjector()->getObject('Model_Db_Table_Interface');
			}
			$result = $this->_table;
		}
		else {

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
			$tableClass = $prefix.'Model_Db_Table_'.$name;
			$result = $this->getInjector()->getObject($tableClass);
		}
		return $result;
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
        $className = $prefix.'Model_Mapper_Db_'.$name;
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
        $object->trigger(Model_Object_Interface::EVENT_BEFORE_UNMAP);
        $values = array();
        $prefix = $this->getTable()->getColumnPrefix().$this->getTable()->getPrefixSeparator();
        foreach ($object->getElements() as $elem=>$value) {
            $values[$prefix.$elem] = $value;
        }
        $this->triggerPlugins(Model_Mapper_Interface::EVENT_UNMAP_OBJECT, array($object, &$values));
        $object->trigger(Model_Object_Interface::EVENT_AFTER_UNMAP);
        return $values;
    }



	/**
	 * mapper from table to object:
     * creates object and populates it with values
     * mapped automatically as $objFieldName => $values[$prefix.'_'.$objFieldName]
     *
	 * @param array $values
	 * @param bool|string - true if was added standard prefix to field name ($this->getTable()->getColumnPrefix())
	 *                      or string if it is prefix itself
	 * @return Model_Object_Interface
	 */
	public function makeSimpleObject(array $values, $addedPrefix = TRUE)
	{
	    $object = $this->getInjector()->getObject('Model_Object_Interface');
        /*$object->trigger(Model_Object_Interface::EVENT_BEFORE_MAP);*/        
	    $arr = array();
	    $els = $object->getElements();
	    $prefixSeparator = $this->getTable()->getPrefixSeparator();
	    $tablePrefix = $this->getTable()->getColumnPrefix() . $prefixSeparator;
	    $pluginPrefixes = array();
	    $pluginHasTable = array();
		foreach ($this->_plugins as $pluginName => $plugin) {
			$pluginHasTable[$pluginName] = $plugin->hasTable();
			if ($pluginHasTable[$pluginName]) {
				$plTable = $plugin->getTable();
				$pluginPrefixes[$pluginName] = $plTable->getColumnPrefix() . $plTable->getPrefixSeparator();
			}
		}
	    foreach ($els as $elemName=>$value) {
			if ($addedPrefix === TRUE) {
				$name = $tablePrefix . $elemName;
			}
            else if ($addedPrefix === FALSE) {
                $name = $elemName;
            }
            else if (is_string($addedPrefix)) {
    			$name = $addedPrefix . $prefixSeparator . $elemName;
            }

            if ( ! array_key_exists($name, $values)) {
                $isset = FALSE;
                foreach ($this->_plugins as $pluginName=>$plugin) {
                    if ($pluginHasTable[$pluginName]) {
                        if ( ! $addedPrefix) {
                            $newName = $pluginPrefixes[$pluginName] . $name;
                        }
                        else {
                            $newName = $pluginPrefixes[$pluginName] . $elemName;
                        }
                        if (array_key_exists($newName, $values)) {
                            $name = $newName;
                            $isset = TRUE;
                            break;
                        }
                    }
                }
            }
            else {
                $isset = TRUE;
            }

            if ($isset === TRUE) {
                $arr [$elemName] = $values[$name];
            }
		}

		$object->populate($arr);

        $this->triggerPlugins(Model_Mapper_Interface::EVENT_MAP_OBJECT, array($object, $values, $addedPrefix));

        /*$object->trigger(Model_Object_Interface::EVENT_AFTER_MAP);*/
        
        return $object;
	}

	/**
	 * @param int $id
	 * @return Model_Object_Interface
	 */
	public function fetchOneById($id)
	{
		if ( ! $id) {
			$this->_throwException('id should be set');
		}
		if ( ! $rows = $this->getTable()->find($id)) {
			$this->_throwException('table row with id="'.$id.'" not found!');
		}
		else {
			$row = $rows->current();
		}
        if ($row === NULL) {
            $this->_throwException('row with id="'.$id.'" not found in table "'.$this->getTable()->getTableName().'". (is table correct?)');
        }
		$object = $this->makeSimpleObject($row->toArray());
		return $object;
	}

    /**
     * @param int $id
     * @return Model_Object_Interface
     */
    public function fetchComplexOneById($id)
    {
        if ( ! $id) {
            $this->_throwException('id should be set');
        }
        if ( ! $collection = $this->fetchComplex(array($this->getTable()->info('name').'.'.$this->getTable()->getColumnPrefix().'_id = ?' => $id))) {
            $this->_throwException('table row with id="'.$id.'" not found!');
        }
        else {
            $object = $collection->current();
        }
        return $object;
    }


    /**
     * fetches all values of field
     * @param string
     * @param mixed array|string
     * @return array
     */
    public function fetchDistinctField($field, $where = NULL)
    {
        $column = $this->getTable()->getColumnPrefix().'_'.$field;
        $select = $this->getTable()->select()
                                   ->distinct(TRUE)
                                   ->from($this->getTable()->getTableName(), array($column));
        if ($where !== NULL) {
            if (is_array($where)) foreach ($where as $cond=>$value) {
                if (is_numeric($cond)) {
                    $select->where($value);
                }
                else {
                    $select->where($cond, $value);
                }
            }
            else {
                $select->where($where);
            }
        }
        $result = array();
        $data = $select->query()->fetchAll();
        foreach ($data as $row) {
            $result []= $row[$column];
        }
        return $result;
    }




	/**
	 * @return Model_Collection_Interface
	 */
	public function fetchAll()
	{
		$set = $this->getInjector()->getObject('Model_Collection_Interface');
        $select = $this->getTable()->select()->from($this->getTable()->getTableName(), $this->getTable()->info('cols'));
		if ($rowSet = $select->query()->fetchAll()) {
			foreach ($rowSet as $row) {
				$set->add($this->makeSimpleObject($row));
			}
		}
		return $set;
	}

    /**
     * count all rows in query
     */
    protected function _countSelectResult($select = NULL)
    {
        $newSelect = $this->getTable()->select()->setIntegrityCheck(FALSE)->from(array('inner'=>$select), array('cnt'=>'count(*)'));
        $row = current($newSelect->query()->fetchAll());
        return (int) $row['cnt'];
    }

	/**
	 * @param Model_Object_Interface
     * @param bool
	 * @return Model_Object_Interface
	 */
	public function save(Model_Object_Interface $object, $disablePluginEvents = FALSE, $disableObjectEvents = FALSE)
	{
        if ( ! $disablePluginEvents) {
            if ( ! is_object($object)) {
                echo __FILE__.' '.__LINE__;
                var_dump(debug_print_backtrace());exit;
            }
            if ( ! $object instanceof Model_Object_Interface) {
                echo __FILE__.' '.__LINE__;
                var_dump($object, get_class($object));exit;
            }
            $this->triggerPlugins(Model_Mapper_Db_Plugin_Interface::EVENT_BEFORE_SAVE, array($object, $object->toArray(), ($object->id > 0)));
        }
        if ( ! $disableObjectEvents) {
            try {
              $object->trigger(Model_Object_Interface::EVENT_BEFORE_SAVE);
            }
            catch (Model_Object_Exception $e) {
                $this->_throwException('unable to save object because - ' . $e->getMessage());
            }
        }
        $values = $this->unmapSimpleObject($object);
        $cols = $this->getTable()->info('cols');
        $realValues = array();
        foreach ($cols as $col) {
            if (array_key_exists($col, $values)) {
                $realValues[$col] = $values[$col];
            }
        }

		if ( (int) $object->id /*AND ($rows = $this->getTable()->find($object->id))*/) {
            $pk = $this->getTable()->getColumnPrefix().'_id';
            $this->getTable()->update($realValues, $pk.' = '.$realValues[$pk]);
            $isNew = FALSE;
		}
		else {
            $this->getTable()->insert($realValues);
            $object->id = $this->getTable()->getAdapter()->lastInsertId();
            $isNew = TRUE;
		}

        if ( ! $disableObjectEvents) {
            $object->trigger(Model_Object_Interface::EVENT_AFTER_SAVE);
        }
        if ( ! $disablePluginEvents) {
            $this->triggerPlugins(Model_Mapper_Db_Plugin_Interface::EVENT_AFTER_SAVE, array($object, $values, $isNew));
        }

		return $object;
	}

    /**
     * save object from array of values or from object itself
     * @param mixed array|Model_Object_Interface
     * @return $this
     */
    public function saveComplex($obj, $returnObj = FALSE)
    {
        if ($obj instanceof Model_Object_Interface) {
            $values = $obj->toArray();
        }
        else if (is_array($obj)) {
            $values = $obj;
            if (isset($values['id']) AND ( (int) $values['id'] > 0)) {
                $obj = $this->fetchOneById($values['id']);
            }
            else {
                $obj = $this->getInjector()->getObject('Model_Object_Interface');
            }
            foreach ($values as $field=>$value) {
                if ($obj->hasElement($field)) {
                    $obj->$field = $value;
                }
            }
        }

        $isNew = (bool) ( ! ($obj->id > 0));


        $obj->trigger(Model_Object_Interface::EVENT_BEFORE_SAVE_COMPLEX);
        
        $obj = $this->_preSaveComplex($obj, $values);

        $this->triggerPlugins(Model_Mapper_Db_Plugin_Interface::EVENT_BEFORE_SAVE_COMPLEX, array($obj, $values, $isNew));
        $obj = $this->save($obj);
        $this->triggerPlugins(Model_Mapper_Db_Plugin_Interface::EVENT_AFTER_SAVE_COMPLEX, array($obj, $values, $isNew));

        $obj = $this->_postSaveComplex($obj, $values);
        
        $obj->trigger(Model_Object_Interface::EVENT_AFTER_SAVE_COMPLEX);

        if ($returnObj === TRUE) {
            return $obj;
        }
        else {
            return $this;
        }
    }

    /**
     * @param Model_Object_Interface
     * @param array
     * @return Model_Object_Interface
     */
    protected function _preSaveComplex(Model_Object_Interface $obj, array $values)
    {
        return $obj;
    }

    /**
     * @param Model_Object_Interface
     * @param array
     * @return Model_Object_Interface
     */
    protected function _postSaveComplex(Model_Object_Interface $obj, array $values)
    {
        return $obj;
    }

    /**
     * @param Model_Object_Interface
     * @return Model_Object_Interface
     */
    protected function _onDelete(Model_Object_Interface $obj)
    {
        return $obj;
    }

	/**
	 * delete as object or by id
	 * @param mixed int|Model_Object_Interface
	 * @return $this
	 */
	public function delete($object)
	{
		if ($object instanceof Model_Object_Interface) {
			$id = $object->id;
		}
		else if (is_numeric($object)) {
			$id = $object;
            $object = $this->fetchOneById($id);
		}
		else {
			$this->_throwException('nothing to delete');
		}
		if ( (int) $id AND ($rows = $this->getTable()->find($id))) {
            $this->triggerPlugins(Model_Mapper_Db_Plugin_Interface::EVENT_BEFORE_DELETE, array($object));
            $this->_onDelete($object);
			$rows->current()->delete();
            $this->triggerPlugins(Model_Mapper_Db_Plugin_Interface::EVENT_AFTER_DELETE, array($object));
		}
		return $this;
	}

	/**
     * TODO may be should be removed?
     *
	 * join current table to query prepared before
	 * @param Zend_Db_Select
	 * @param string - table alias in query
	 * @param string - ON condition
	 * @return Zend_Db_Select
	 */
	public function joinSubQuery(Zend_Db_Select $select, $alias, $on, $cols = NULL)
	{
		$tableName = $this->getTable()->getTableName();
        if ($cols === NULL) $cols = $this->getTable()->info('cols');
		$select -> joinLeft(
						array($alias => $tableName),
						$on,
						$cols
				   );
		return $select;
	}

	/**
	 * fetch with joined tables to init object fields
	 * @param mixed array|string|Zend_Db_Select
     * @param bool fetch or just return Zend_Db_Select
     * @return mixed Model_Collection_Interface | Zend_Db_Select
	 */
	public function fetchComplex($where = NULL, $fetch = TRUE, $limit = NULL)
	{
		$select = $this -> getTable()
						-> select() -> setIntegrityCheck(FALSE)
						-> from(array($this->getTable()->info('name')), $this->getTable()->info('cols'));
        if ($limit) $select->limit($limit);
		if (is_array($where)) {
			foreach ($where as $key=>$val) {
				if (is_numeric($key)) {
					$select->where($val);
				}
				else {
					$select->where($key, $val);
				}
			}
		}
		else if (is_string($where) AND ! empty($where)) {
			$select->where($where);
		}

		$select = $this->triggerPlugins(Model_Mapper_Db_Plugin_Interface::EVENT_FETCH_COMPLEX, array($select));

		$select = $this->_onFetchComplex($select);

		if ($fetch === TRUE) {
			$result = $this->makeComplexCollection($select->query()->fetchAll());
            ++ self::$_fetchComplexCounter;
		}
		else {
			$result = $select;
		}

		return $result;
	}



    /**
     * fetch data from main table to init object fields
     * @param mixed Zend_Db_Table_Select|Model_Collection_Interface
     */
    public function fetchSimple($where = NULL, $fetch = TRUE)
    {
        $select = $this -> getTable()
                        -> select() -> setIntegrityCheck(FALSE)
                        -> from(array($this->getTable()->info('name')), $this->getTable()->info('cols'));

        if (is_array($where)) {
            foreach ($where as $key=>$val) {
                if (is_numeric($key)) {
                    $select->where($val);
                }
                else {
                    $select->where($key, $val);
                }
            }
        }
        else if (is_string($where) AND ! empty($where)) {
            $select->where($where);
        }

        if ($fetch === TRUE) {
            $result = $this->makeSimpleCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }

        return $result;
    }



	/**
	 * build object with subobjects
	 * @param array
	 * @return Model_Object_Interface
	 */
	public function makeComplexObject(array $values, $addedPrefix = TRUE)
	{
		$object = $this->makeSimpleObject($values, $addedPrefix);

        $object->setIsComplex();

        $debug_objectClass = get_class($object);
        $debug_object = $object;

		$object = $this->triggerPlugins(Model_Mapper_Db_Plugin_Interface::EVENT_BUILD_COMPLEX, array($object, $values, $addedPrefix));


        if ($object === NULL) {
            $errorText = 'error in '.get_class($this).'::'.__FUNCTION__.' - object of '.$debug_objectClass.' became NULL after triggerPlugins(buildComplex,...) '
                     .'it looks like one of plugins('.implode(',', array_keys($this->_plugins)).') has wrong trigger onBuildComplex (it ought to return Model_Object_Interface)';
            if (APPLICATION_ENV == 'development') {
                echo __FILE__.' '.__LINE__;
                Zend_Debug::dump($errorText);
                debug_print_backtrace();
                exit;
            }
            $this->_throwException($errorText);
        }


		$object = $this->_onBuildComplexObject($object, $values, $addedPrefix);

		return $object;
	}

	/**
	 * addon actions when building complex object
	 * @param Model_Object_Interface $object
	 * @param array $values
	 * @return Model_Object_Interface
	 */
	protected function _onBuildComplexObject(Model_Object_Interface $object, array $values = NULL, $addedPrefix = TRUE)
	{
	    return $object;
	}

	/**
	 * addons for complex fetch
	 * @param Zend_Db_Select
	 * @return Zend_Db_Select
	 */
	protected function _onFetchComplex(Zend_Db_Select $select)
	{
		return $select;
	}


    /**
     * sets values (correctly mapped array) to object fields and marks object as STYLE_CUSTOM
     * @param array
     * @return Model_Object_Interface
     */
    public function makeCustomObject(array $values)
    {
        $object = $this->getInjector()->getObject('Model_Object_Interface');
        foreach ($values as $field=>$value) {
            if ($object->hasElement($field)) {
                $object->{$field} = $value;
            }
        }
        $object->setMappingStyle(Model_Object_Interface::STYLE_CUSTOM);
        return $object;
    }



    /**
     * builds objects from array
     * @param array( row1 => array(), array())
     * @return Model_Collection_Interface
     */
    public function makeComplexCollection(array $data)
    {
        return $this->_makeCollection('complex', $data);
    }

    /**
     * builds objects from array
     * @param array( row1 => array(), array())
     * @return Model_Collection_Interface
     */
    public function makeSimpleCollection(array $data)
    {
        return $this->_makeCollection('simple', $data);
    }

    /**
     * builds objects from array
     * @param array( row1 => array(), array())
     * @return Model_Collection_Interface
     */
    public function makeCustomCollection(array $data)
    {
        return $this->_makeCollection('custom', $data);
    }


    /**
     * @param string suffix for make method
     * @param array data
     */
    protected function _makeCollection($style, array $data)
    {
        /** this method can recieve empty data and create empty collection
         *
        if (empty($data)) {
            throw new Model_Mapper_Db_Exception('_makeCollection recieved empty $data - check for previous select query ');
        }
        */

        $objects = $this->getInjector()->getObject('Model_Collection_Interface');
        foreach ($data as $row) {
            $objects->add($this->{'make'.ucfirst($style).'Object'}($row));
        }
        return $objects;
    }


    /**
     * make paginator for complex fetching
     * @param mixed array|string
     * @param int
     * @param int
     */
    public function paginatorFetchComplex($where, $rowsPerPage, $page)
    {
        $query = $this->fetchComplex($where, FALSE);

        return $this->paginator($query,  $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
    }

    /**
     * make paginator for query
     * @param Zend_Db_Table_Select
     * @param int
     * @param int
     * @param int
     */
    public function paginator(Zend_Db_Table_Select $select, $rowsPerPage, $page, $style = Model_Object_Interface::STYLE_SIMPLE)
    {
        $this->triggerPlugins(Model_Mapper_Interface::EVENT_PAGINATION, array($select));

        $paginator = Zend_Paginator::factory(
                                array(
                                    'select'=>$select,
                                    'mapper'=>$this,
                                    'style'=>Model_Object_Interface::STYLE_COMPLEX,
                                ),
                                'ModelMapperDb'
                            )
                            ->setItemCountPerPage($rowsPerPage)
                            ->setCurrentPageNumber($page)
                            ;

        return $paginator;
    }
    
    /**
     * making paginator from array or collection
     * @param $data array|Model_Collection_Interface
     * @param $rowsPerPage int
     * @param $page int
     * @return Zend_Paginator
     */
    public function paginatorArray($data, $rowsPerPage, $page)
    {
        if ($data instanceof Model_Collection_Interface) {
            $data = $data->toArray();
        }
        $paginator = Zend_Paginator::factory($data,'Array')
                            ->setItemCountPerPage($rowsPerPage)
                            ->setCurrentPageNumber($page)
                            ;
        return $paginator;        
    }


    /**
     * get collection from objects with ids in array
     * @param array
     */
    public function fetchByIdArray(array $ids, $style = Model_Object_Interface::STYLE_SIMPLE)
    {
        $fieldId = $this->getTable()->getColumnPrefix().'_id';
        $where = array($fieldId . ' IN (?)'=>$ids);
        if ($style == Model_Object_Interface::STYLE_SIMPLE) {
            $result = $this->fetchSimple($where);
        }
        else {
            $result = $this->fetchComplex($where);
        }
        return $result;
    }

    /**
     * set all objects in collection active
     * @param Model_Collection_Interface
     */
    public function activateCollection($collection)
    {
        $ids = array();
        foreach ($collection as $obj) {
            if ( ! $obj->hasElement('status')) {
                $this->_throwException('object '.get_class($obj).' has no element "status" that\'s why collection '.get_class($collection).' cannot be activated');
            }
            $ids[] = $obj->id;
            $obj->status = 1;
        }
        $colPrefix = $this->getTable()->getColumnPrefix();
        $this->getTable()->update(
            array($colPrefix.'_status' => new Zend_Db_Expr(1)),
            array($colPrefix.'_id IN (?)' => $ids)
        );
        return $this;
    }


    /**
     * set all objects in collection inactive
     * @param Model_Collection_Interface
     */
    public function deactivateCollection($collection)
    {
        $ids = array();
        foreach ($collection as $obj) {
            if ( ! $obj->hasElement('status')) {
                $this->_throwException('object '.get_class($obj).' has no element "status" that\'s why collection '.get_class($collection).' cannot be deactivated');
            }
            $ids[] = $obj->id;
            $obj->status = 0;
        }
        $colPrefix = $this->getTable()->getColumnPrefix();
        $this->getTable()->update(
            array($colPrefix.'_status' => new Zend_Db_Expr(0)),
            array($colPrefix.'_id IN (?)' => $ids)
        );
        return $this;
    }



    /**
     * delete all objects in collection
     * @param Model_Collection_Interface
     */
    public function deleteCollection($collection)
    {
        $ids = array();
        foreach ($collection as $obj) {
            $ids[] = $obj->id;
            $this->triggerPlugins(Model_Mapper_Db_Plugin_Interface::EVENT_BEFORE_DELETE, array($obj));
        }
        $colPrefix = $this->getTable()->getColumnPrefix();
        if ( ! empty($ids)) {
            $this->getTable()->delete(array($colPrefix.'_id IN (?)' => $ids));
        }
        return $this;
    }


    /**
     * creates one pool of update queries (united into one) for each table
     * executes query when reached POOL_LIMIT or called without $values
     *
     * @param string
     * @param array
     * @param array
     */
    public function poolUpdate($table, array $values = NULL, array $where = NULL)
    {
        if (($values !== NULL ) AND ($where !== NULL)) {
            $this->_poolUpdate[$table][] = array('values' => $values, 'where' => $this->_prepareWhere($where));
        }
        if (isset($this->_poolUpdate[$table]) AND ((count($this->_poolUpdate[$table]) == self::POOL_LIMIT) OR ($values === NULL)) AND ( ! empty($this->_poolUpdate[$table]))) {
            $query = 'UPDATE '.$table.' ';
            $setParts = array();
            $whereParts = array();
            $fields = array_keys($this->_poolUpdate[$table][0]['values']);
            if ($fields) foreach ($fields as $field) {
                $setPart = $field;
                foreach ($this->_poolUpdate[$table] as $part) {
                    $vv = $part['values'][$field];
                    if ($vv === NULL) {
                        $vv = 'NULL';
                    }
                    else {
                        $vv = "'".addslashes($vv)."'";
                    }
                    $setPart = 'IF ('.$part['where'].','.$vv.','.$setPart.')';
                    $whereParts[$part['where']] = $part['where'];
                }
                $setParts[]= $field.' = '.$setPart;
            }
            $query .= 'SET '.implode($setParts, ' , ').' WHERE '.implode($whereParts, ' OR ');
            if ( ! empty($this->_poolUpdate[$table])) {
                try {
                    $this->getTable()->getAdapter()->query($query);
                }
                catch (Exception $e) {
                    $this->_throwException('wrong query ('.$e->getMessage().')- '.$query);
                }
                ++ self::$_poolUpdateCounter;
                $this->_poolUpdate[$table] = array();
            }
        }
    }

    /**
     * @return int
     */
    public function getPoolUpdateCounter()
    {
        return self::$_poolUpdateCounter;
    }


    /**
     * creates one pool of insert queries (united into one) for each table
     * executes query when reached POOL_LIMIT or called without $values
     *
     * @param string
     * @param array
     * @param array
     */
    public function poolInsert($table, array $values = NULL)
    {
        if ($values !== NULL ) {
            $this->_poolInsert[$table][] = $values;
        }
        if (isset($this->_poolInsert[$table]) AND ((count($this->_poolInsert[$table]) == self::POOL_LIMIT) OR ($values === NULL))) {
            $parts = array();
            $fields = array_keys($this->_poolInsert[$table][0]);
            if ($fields)  {
                $query = 'INSERT INTO '.$table.'('.implode(',', $fields).') VALUES ';
                $i = 0;
                foreach ($this->_poolInsert[$table] as $part) {
                    if ($i > 0) {
                        $query .= ', ';
                    }
                    foreach ($part as $key=>$value) {
                        if ($part[$key] === NULL) {
                            $part[$key] = 'NULL';
                        }
                        else {
                            $value = trim($value, "'");
                            $part[$key] = "'".addslashes($value). "'";
                        }
                    }
                    $query .= '('.implode(',', $part).')';
                    ++ $i;
                }
                try {
                    $this->getTable()->getAdapter()->query($query);
                }
                catch (Exception $e) {
                    $this->_throwException('wrong query ('.$e->getMessage().')- '.$query);
                }
                ++ self::$_poolInsertCounter;
                $this->_poolInsert[$table] = array();
            }
        }
    }

    /**
     * @return int
     */
    public function getPoolInsertCounter()
    {
        return self::$_poolInsertCounter;
    }

    /**
     * gets staic counter of fetchComplex queries (sometimes usefull)
     * @return int
     */
    public function getFetchComplexCounter()
    {
        return self::$_fetchComplexCounter;
    }

    /**
     * make where string from array
     * @param array
     * @param string
     */
    protected function _prepareWhere(array $where)
    {
        $strs = array();
        foreach ($where as $cond=>$value) {
            $strs []= $this->getTable()->getAdapter()->quoteInto($cond, $value);
        }
        $str = implode(' AND ', $strs);
        return $str;
    }
    
    /**
     * count values in field
     * @param mixed string|int|double|date $value
     * @param string $field
     * @param bool $addedPrefix
     * @return int
     */
    public function countFieldValues($value, $field, $addedPrefix = FALSE, $except = NULL)
    {
        $table = $this->getTable();
        $prefix = $table->getColumnPrefix() . $table->getPrefixSeparator();
        if ( ! $addedPrefix) {
            $field = $prefix . $field;
        }
        $select = $table->select()->from($table->getTableName(), array('cnt' => 'COUNT(DISTINCT '.$prefix.'id)'))
                                  ->where($field.' = ?', $value);
        if ( (int) $except) {
            $select->where($prefix.'id <> ?', $except);
        }
        $resultRow = $select->query()->fetch();
        $count = (int) $resultRow['cnt'];
        return $count;
    }
	/**
     * Check - whether there is an object with the same seo_id
     * @param string $seo_id
     * @return bool
     */
	public function fetchSeoId($seoId)
	{
        $seoIdField = $this->getTable()->getColumnPrefix() . '_seo_id';

		$select = $this->getTable()
			->select()
			->from(
				$this->getTable(),
				array('count_items' => 'COUNT('.$seoIdField.')')
			)
			->where(
				$seoIdField . ' = ?', $seoId
			);
			
		return $select->query()->fetchObject();
	}

}
