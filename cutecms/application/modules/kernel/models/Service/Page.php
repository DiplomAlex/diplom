<?php

class Model_Service_Page extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
    	'Model_Mapper_Interface' => 'Model_Mapper_Db_Page',
        'Model_Object_Interface' => 'Model_Object_Page',
        'Model_Service_Language',
        'Model_Service_Helper_Multisite',
    );


    /**
     * initializes object
     * @see Model_Service_Abstract::init()
     */
    public function init()
    {
        $lang = $this->getInjector()->getObject('Model_Service_Language');
        $this->getMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());
        $this->addHelper('Multisite', $this->getInjector()->getObject('Model_Service_Helper_Multisite', $this));
    }


	/**
	 * @param mixed (string|int) seo_id or id of the page
	 * @return Model_Object_Page
	 */
	public function get($id)
	{
		if (is_numeric($id)) {
			$page = $this->getMapper()->fetchOneById($id);
		}
		else if (is_string($id)) {
			$page = $this->getMapper()->fetchOneBySeoId($id);
		}
		else {
			throw new Model_Service_Exception('unknown parameter for get method - '.$id);
		}
		return $page;
	}

    /**
     * returns list of available drtivers
     */
    public function getDriversList()
    {
        try {
            $pageConf = Zend_Registry::get('config')->page;
            $list = $pageConf->driver->toArray();
        }
        catch (Zend_Config_Exception $e) {
            $list = array();
            App_Debug::log('Add "[page]\n driver[]=DriverName" to your kernel/config.ini or config.ini of frontend module');
        }
        return $list;
    }


    public function isDriverEnabled($driverCode)
    {
        if (is_numeric($driverCode)) {
            $result = FALSE;
        }
        else {
            $conf = Zend_Registry::get('config');
            if (($pageConf = $conf->get('page')) AND (in_array($driverCode, $pageConf->driver->toArray()))) {
                $result = TRUE;
            }
            else {
                $result = FALSE;
                App_Debug::log('Add "[page]\n driver[]=DriverName" to your kernel/config.ini or config.ini of frontend module');
            }
        }
        return $result;
    }


    /**
     * get objects fields values for edit form
     * @return array
     */
    public function getEditFormValues($id)
    {
        $obj = $this->get($id);
        $values = $obj->toArray();
        $descs = $this->getMapper()->getPlugin('Description')->fetchDescriptions($id);
        $rcs = $this->getMapper()->getPlugin('Resource')->fetchResources($obj);
        $values = $values + $descs + $rcs;
        return $values;
    }



    public function getAllByCodes()
    {
        $cacheKey = __CLASS__.'__'.__FUNCTION__;
        $cache = Zend_Registry::get('Zend_Cache');
        if ( ! $pages = $cache->load($cacheKey)) {
            $pages = array();
            $all = $this->getAll();
            foreach ($all as $page) {
                if ($page->code) {
                    $pages[$page->code] = $page;
                }
            }
            $cache->save($pages, $cacheKey, array('page'));
        }
        return $pages;
    }

    public function getByCode($code)
    {
        $cacheKey = __CLASS__.'__'.__FUNCTION__.'__'.$code;
        $cache = Zend_Registry::get('Zend_Cache');
        if ( ! $page = $cache->load($cacheKey)) {
            $page = $this->getMapper()->fetchOneByCode($code);
            $cache->save($page, $cacheKey, array('page'));
        }
        return $page;
    }

    public function getAllByFlag($num)
    {
        return $this->getMapper()->fetchComplexByFlag((int) $num);
    }

    public function getBySeoId($seo_id)
    {
        return $this->getMapper()->fetchBySeoId($seo_id);
    }
    
    public function getByText($text = NULL)
    {
        return $this->getMapper()->fetchByText($text);
    }

}