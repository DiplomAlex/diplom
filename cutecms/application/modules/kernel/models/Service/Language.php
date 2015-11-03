<?php

class Model_Service_Language extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Model_Object_Language',
        'Model_Collection_Interface' => 'Model_Collection_Language',
    	'Model_Mapper_Interface'     => 'Model_Mapper_Db_Language',
    );

    protected $_default = NULL;
    protected $_current = NULL;
    
    protected $_session = NULL;
    
    protected function _session()
    {
        if ($this->_session == NULL) {
            $this->_session = new Zend_Session_Namespace(__CLASS__);
        }
        return $this->_session;
    } 
    
	/**
	 * lazy init static default language
	 */
	public function getDefault()
	{
		if ($this->_default === NULL) {
            $cache = Zend_Registry::get('Zend_Cache');
            if ( ! $this->_default = $cache->load('Model_Service_Language__getDefault')) {
			     $this->_default = $this->getMapper()->fetchDefault();
                 $cache->save($this->_default, 'Model_Service_Language__getDefault');
            }
		}
		return $this->_default;
	}

	/**
	 * lazy init static current language
	 */
	public function getCurrent()
	{
	    if (($this->_current === NULL) AND ( ! $this->_current = $this->_session()->language)) {
	        $this->_current = $this->getDefault();
	    }
        return $this->_current;
	}

    /**
     * set current language
     *
     * @param Model_Object_Language_Interface
     * @return $this
     */
    public function setCurrent(Model_Object_Language_Interface $newCurrent)
    {
        $this->_current = $newCurrent;
        $this->_session()->language = $newCurrent;
        return $this;
    }

    public function getAllActive()
    {
        $cache = Zend_Registry::get('Zend_Cache');
        $cacheKey = 'Model_Service_Language__getAllActive';
        if ( ! $rows = $cache->load($cacheKey)) {
            $rows = $this->getMapper()->fetchAllActive();
            $cache->save($rows, $cacheKey);
        }
        return $rows;
    }
    
    public function getOneByCode2($code2)
    {
        return $this->getMapper()->fetchOneByCode2($code2);
    }
    
}