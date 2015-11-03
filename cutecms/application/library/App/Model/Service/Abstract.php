<?php

require_once 'App/DIContainer.php';
require_once 'App/Model/Service/Interface.php';
require_once 'App/Model/Mapper/Interface.php';
require_once 'App/Model/Object/Interface.php';
require_once 'App/Model/Service/Exception.php';

class Model_Service_Abstract implements Model_Service_Interface
{

	/**
	 * @var string
	 */
	protected $_modelName = NULL;

	/**
	 * @var App_DIContainer
	 */
	protected $_injector = NULL;

	/**
	 * @var Zend_Translate_Adapter
	 */
    protected $_translator = NULL;


    /**
     * @var array (Model_Service_Helper_Interface)
     */
    protected $_helpers = array();

	/**
	 * @var array(string)
	 */
	protected $_defaultInjections = array(
        'Model_Object_Interface',
    );


    /**
     * @var array(int=>string)
     */
    protected $_statusesList = array(
        'disabled' => 0,
        'enabled' => 1,
    );


	/**
	 * @var array(Model_Mapper_Interface)
	 */
	protected $_mappers = array();

	public function __construct()
	{
		$this->injectDefaults();
		$this->init();
	}

	/**
	 * initializes service object
	 */
	public function init()
	{
	}

    protected function _throwException($message)
    {
        throw new Model_Service_Exception(get_class($this).' throws: '.$message);
    }

	/**
	 * inject all $this->_defaultInjections
	 *
	 * @return Model_Service_Abstract $this
	 */
	public function injectDefaults()
	{
		foreach ($this->_defaultInjections as $interface => $class) {
			$this->getInjector()->inject($interface, $class);
		}
		return $this;
	}

	/**
	 * lazy init of DI
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
     * gets helper by alias
     * @param string
     * @return Model_Service_Helper_Interface
     */
    public function getHelper($alias)
    {
        if ( ! isset($this->_helpers[$alias])) {
            $this->_throwException('helper "'.$alias.'" was not added to service before');
        }
        return $this->_helpers[$alias];
    }

    /**
     * adds helper to service
     * @param string
     * @param Model_Service_Helper_Interface $helper
     * @return Model_Service_Interface $this
     */
    public function addHelper($alias, Model_Service_Helper_Interface $helper)
    {
        $this->_helpers[$alias] = $helper;
        return $this;
    }


	/**
	 * lazy init translator
	 * @return Zend_Translate_Adapter
	 */
    public function getTranslator()
    {
        if ($this->_translator === NULL) {
            $this->_translator = Zend_Registry::get('Zend_Translate')->getAdapter();
        }
        return $this->_translator;
    }


	/**
	 * lazy init of model name as last part of class name
	 * (also can be set at designtime)
	 * @return string
	 */
	public function getModelName()
	{
		if ($this->_modelName === NULL) {
			$arr = explode('_', get_class($this));
			$name = array_pop($arr);
			$this->_modelName = ucfirst($name);
		}
		return $this->_modelName;
	}


	/**
	 * if $name is empty - returns mapper for current model
	 * @param string name of model for mapper
	 * @return Model_Mapper_Interface
	 */
	public function getMapper($name = NULL)
	{
		if (empty($name)) {
			$name = 'Interface';
		}

		$nameArr = explode('/', $name);
		if (count($nameArr) > 1) {
            $module = ucfirst($nameArr[0]).'_';
		}
		else {
            $module = '';
		}

		$mapperName = $module.'Model_Mapper_'.$name;
		if ( ! isset($this->_mappers[$mapperName])) {
			$this->_mappers[$mapperName] = $this->getInjector()->getObject($mapperName);
		}
		return $this->_mappers[$mapperName];
	}


	/**
	 * @param mixed (string|int) seo_id or id of the page
	 * @return Model_Object_Interface
	 */
	public function get($id)
	{
		if (is_numeric($id)) {
			$object = $this->getMapper()->fetchOneById($id);
		}
		else {
			$this->_throwException('unknown parameter for get method - '.$id);
		}
		return $object;
	}


    /**
     * @param int id
     * @return Model_Object_Interface
     */
    public function getComplex($id)
    {
        if (is_numeric($id)) {
            $object = $this->getMapper()->fetchComplexOneById($id);
        }
        else {
            $this->_throwException('unknown parameter for getComplex method - '.$id);
        }
        return $object;
    }

	/**
	 * get all objects
	 * @return Model_Collection_Interface
	 */
	public function getAll()
	{
		return $this->getMapper()->fetchComplex();
	}
	
	public function getAllByIdArray(array $ids, $style = Model_Object_Interface::STYLE_SIMPLE)
	{
	    return $this->getMapper()->fetchByIdArray($ids, $style);
	}
	

	/**
	 * @param Model_Object_Interface
	 * @return $this
	 */
	public function save(Model_Object_Interface $object)
	{
		$this->getMapper()->save($object);
		return $this;
	}


    /**
     * @param Model_Object_Interface
     * @return $this
     */
    public function saveComplex(Model_Object_Interface $object)
    {
        $this->getMapper()->saveComplex($object);
        return $this;
    }


	/**
	 * if deleting by id - just silent delete, otherwise - triggers event on object
	 * @param mixed int|Model_Object_Interface
	 * @return $this
	 */
	public function delete($obj)
	{
		if (is_numeric($obj)) {
			$id = $obj;
            $obj = $this->getMapper()->fetchOneById($id);
	    }
	    if ($obj instanceof Model_Object_Interface) {
			/*$obj->trigger(Model_Object_Interface::EVENT_DELETE);*/
            $obj->trigger(Model_Object_Interface::EVENT_BEFORE_DELETE);
			$id = $obj->id;
            $this->getMapper()->delete($id);
            $obj->trigger(Model_Object_Interface::EVENT_AFTER_DELETE);
        }
	    else {
	    	$this->_throwException('nothing to delete');
	    }
		return $this;
	}



    /**
     * get all rows as page of Zend_Paginator object
     * @param int
     * @param int
     * @return Zend_Paginator
     */
    public function paginatorGetAll($rowsPerPage = NULL, $page = NULL)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        $paginator = $this->getMapper()->paginatorFetchComplex(NULL, $rowsPerPage, $page);
        return $paginator;
    }


    /**
     * change position in list
     * @param int objectId
     * @param string (first|last|prev|next)
     */
    public function changeSorting($objId, $position)
    {
        if ( ! $this->getMapper()->hasPlugin('Sorting')) {
            $this->_throwException('my mapper ('.get_class($this->getMapper()).') has no plugin "Sorting" so it cannot changeSorting');
        }
        $object = $this->get($objId);
        $this->getMapper()->getPlugin('Sorting')->changeSorting($object, $position);
        return $this;
    }

    /**
     * activate all objects (set status=1) with ids in array
     * @param array
     */
    public function activateByIdArray(array $ids)
    {
        $collection = $this->getMapper()->fetchByIdArray($ids);
        App_Event::factory('Model_Collection_Interface__beforeActivateCollection', array($collection))->dispatch();
        $this->getMapper()->activateCollection($collection);
        App_Event::factory('Model_Collection_Interface__afterActivateCollection', array($collection))->dispatch();
        return $this;
    }

    /**
     * deactivate all objects (set status=0) with ids in array
     * @param array
     */
    public function deactivateByIdArray(array $ids)
    {
        $collection = $this->getMapper()->fetchByIdArray($ids);
        App_Event::factory('Model_Collection_Interface__beforeDeactivateCollection', array($collection))->dispatch();
        $this->getMapper()->deactivateCollection($collection);
        App_Event::factory('Model_Collection_Interface__afterDeactivateCollection', array($collection))->dispatch();
        return $this;
    }

    /**
     * delete all objects  with ids in array
     * @param array
     */
    public function deleteByIdArray(array $ids)
    {
        $collection = $this->getMapper()->fetchByIdArray($ids);
        App_Event::factory('Model_Collection_Interface__beforeDeleteCollection', array($collection))->dispatch();
        $this->getMapper()->deleteCollection($collection);
        App_Event::factory('Model_Collection_Interface__afterDeleteCollection', array($collection))->dispatch();
        return $this;
    }


    /**
     * save it
     * @param array
     * @param bool
     * @return mixed Model_Service_Interface | Model_Object_Interface
     */
    public function saveFromValues(array $values, $returnObj = FALSE)
    {
        /**
         * this "if" should be removed while refactoring
         */
        if (empty($values['id'])) {
            unset($values['id']);
        }
        $obj = $this->getMapper()->saveComplex($values, TRUE);
        if ($returnObj === TRUE) {
            return $obj;
        }
        else {
            return $this;
        }
    }

    /**
     * creates new object
     * @return Model_Object_Interface
     */
    public function create()
    {
        return $this->getInjector()->getObject('Model_Object_Interface');
    }

    /**
     * returns statuses list
     * @return array
     */
    public function getStatusesList()
    {
        return $this->_statusesList;
    }


    /**
     * get objects fields values for edit form
     * @return array
     */
    public function getEditFormValues($id)
    {
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        return $values;
    }
    
    /**
     * @param string $value
     * @param string $field
     * @return int
     */
    public function countFieldValues($value, $field, $except = NULL)
    {
        return $this->getMapper()->countFieldValues($value, $field, FALSE, $except);
    }

	/**
     * Check - whether there is an object with the same seo_id
     * @param string $seo_id
     * @return int $count_items
     */
	public function checkSeoId($seoId)
	{
		return $this->getMapper()->fetchSeoId($seoId)->count_items;
	}

}