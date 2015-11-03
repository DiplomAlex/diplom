<?php

class Model_Service_Site extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
    	'Model_Mapper_Interface' => 'Model_Mapper_Db_Site',
        'Model_Object_Interface' => 'Model_Object_Site',
        'Model_Collection_Interface' => 'Model_Collection_Site',
    );

    /**
     * @var Model_Object_Site
     */
    protected static $_currentSite = NULL;

    public function init()
    {
        if ((App_PreBoot::isInstalled()) AND (APPLICATION_ENV != 'installer')) {
    	   $lang = Model_Service::factory('language');
    	   $this->getMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());
        }
    }

	/**
	 * @param mixed (string|int) host or id of the page
	 * @return Model_Object_Site
	 */
	public function get($id)
	{
		if (is_numeric($id)) {
			$object = $this->getMapper()->fetchOneById($id);
		}
		else if (is_string($id)) {
			$object = $this->getMapper()->fetchOneByHost($id);
		}
		else {
			throw new Model_Service_Exception('unknown parameter for get method - '.$id);
		}
		return $object;
	}

	/**
	 * @return Model_Object_Site
	 */
	public function getCurrent()
	{
		if (self::$_currentSite === NULL) {
			$base = (APPLICATION_BASE == '/' ? NULL : APPLICATION_BASE);
			$host = $this->getCurrentHost();
            $cache = Zend_Registry::get('Zend_Cache');
            $cacheKey = 'Model_Service_Site__getCurrent__'.md5($host . $base);
            if ( ! self::$_currentSite = $cache->load($cacheKey)) {
                self::$_currentSite = $this->getMapper()->fetchOneByHost($host, $base);
                $cache->save(self::$_currentSite, $cacheKey);
            }
		}
		return self::$_currentSite;
	}


    public function getCurrentHost()
    {
        if ( ! isset($_SERVER['HTTP_HOST']) OR ! ($host = $_SERVER['HTTP_HOST'])) {
            $host = Zend_Registry::get('config')->www->domain;
        }
        return $host;
    }


	/**
	 * get all objects but not current
	 * @return array(Model_Object_Site)
	 */
	public function getAllButCurrent()
	{
        $cache = Zend_Registry::get('Zend_Cache');
        $cacheKey = 'Model_Service_Site__getAllButCurrent__'.$this->getCurrent()->id;
        if ( ! $result = $cache->load($cacheKey)) {
            $result = $this->getMapper()->fetchIdNotIn(array($this->getCurrent()->id));
            $cache->save($result, $cacheKey);
        }
		return $result;
	}

	/**
	 * @return array(Model_Object_Site)
	 */
	public function getAll()
	{
		return $this->getMapper()->fetchComplex();
	}


	public function getAllAsSelectOptions($withAll = FALSE)
	{
	    $list = array();
	    if (is_string($withAll)) {
	        $list [0]= ' -- '.$withAll.' -- ';
	    }
        else if ($withAll === TRUE) {
            $list [0]= ' -- '.$this->getTranslator()->_('All').' -- ';
        }
        $sites = $this->getAll();
        foreach ($sites as $site) {
            $list[$site->id] = $site->specification;
        }
        return $list;
	}

	public function getByIdArray(array $ids)
	{
	    return $this->getMapper()->fetchByIdArray($ids);
	}


    /**
     * get objects fields values for edit form
     * @return array
     */
    public function getEditFormValues($id)
    {
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        $descs = $this->getMapper()->getPlugin('Description')->fetchDescriptions($id);
        $values = $values + $descs;
        return $values;
    }

    public function getLinkedByDefault()
    {
        return $this->getMapper()->fetchLinkedByDefault();
    }


}