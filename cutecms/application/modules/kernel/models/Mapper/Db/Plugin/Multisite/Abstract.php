<?php

abstract class Model_Mapper_Db_Plugin_Multisite_Abstract extends Model_Mapper_Db_Plugin_Abstract
{
    
    /**
     * current site id for fetching 
     * @var int|NULL
     */
    protected $_currentSiteId = NULL;


    /**
     * object of Model_Mapper_Db_Sites
     * @var Model_Mapper_Db_Interface
     */    
    protected $_siteMapper = NULL;
        
    
    
    
    /**
     * values of all protected fields can be specified with $config 
     * @param array $config
     */
    public function __construct(array $config = NULL)
    {
        if ($config !== NULL) {
            $this->setConfig($config);
        }
    }    
    
    /**
     * set values for all settings by array 
     * @param array $config
     */    
    abstract public function setConfig(array $config);

    
    /**
     * _siteMapper getter
     * @return Model_Mapper_Db_Interface
     */
    public function getSiteMapper()
    {
        if ( ! $this->_siteMapper) {
            $this->_throwException('siteMapper was not set in mapper init() but is requested now');
        }
        return $this->_siteMapper;
    }
    
    /**
     * _siteMapper setter
     * @param Model_Mapper_Db_Interface $mapper
     * @return $this
     */
    public function setSiteMapper(Model_Mapper_Db_Interface $mapper)
    {
        $this->_siteMapper = $mapper;
        return $this;
    }

    /**
     * add multisiting left join and where to the select query
     * @param Zend_Db_Select $select
     * @return Zend_Db_Select $select
     */
    abstract public function addMultisitingToSelect(Zend_Db_Select $select, $siteId = NULL);
    
    
    /**
     * @param Zend_Db_Select
     * @return Zend_Db_Select
     */
    public function onFetchComplex(Zend_Db_Select $select)
    {
        if ( (int) $this->_currentSiteId) {
            $select = $this->addMultisitingToSelect($select, $this->_currentSiteId);
        }
        return $select;
    }
    
    
    public function setCurrentSiteId($siteId)
    {
        $this->_currentSiteId = $siteId;
        return $this;
    }
    
    
    public function getCurrentSiteId()
    {
        return $this->_currentSiteId;
    }
    
    
    
    
}