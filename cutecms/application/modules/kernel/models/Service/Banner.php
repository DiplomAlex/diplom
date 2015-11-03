<?php

class Model_Service_Banner extends Model_Service_Abstract
{


    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_Banner',
    	'Model_Mapper_Interface' => 'Model_Mapper_Db_Banner',
        'Model_Service_Helper_Multisite',
    );

    
    protected $_placesList = NULL;


    /**
     * initializes object
     * @see Model_Service_Abstract::init()
     */
    public function init()
    {
        $lang = Model_Service::factory('language');
        $this->getMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());
        $this->addHelper('Multisite', $this->getInjector()->getObject('Model_Service_Helper_Multisite', $this));
    }


    /**
     * get places from config
     * @return Zend_Config
     */
    public function getPlacesList()
    {
        if ($this->_placesList === NULL) {
            $this->_placesList = Zend_Registry::get('config')->banners;
        }
        return $this->_placesList;
    }
    
    public function setPlacesList(Zend_Config $places)
    {
        $this->_placesList = $places;
        return $this;
    }

    /**
     * get random banner by place
     * @return Model_Object_Interface
     */
    public function getRandomByPlace($place)
    {
        return $this->getMapper()->fetchRandomByPlace($place);
    }

	public function getAllByPlace($place)
    {
        return $this->getMapper()->fetchAllByPlace($place);
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
        $rcs = $this->getMapper()->getPlugin('Resource')->fetchResources($obj);
        $values = $values + $descs + $rcs;
        return $values;
    }
	
	 public function getByDescName($name)
    {
        return $this->getMapper()->fetchByDescName($name);
    }



}