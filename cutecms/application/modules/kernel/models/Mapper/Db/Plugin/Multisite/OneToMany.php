<?php

class Model_Mapper_Db_Plugin_Multisite_OneToMany extends Model_Mapper_Db_Plugin_Multisite_Abstract
{
    
    protected $_hasTable = FALSE;    
        
    /**
     * ALL COLUMN NAMES SHOULD BE SPECIFIED WITHOUT PREFIX!!!
     */
    
    /**
     * column of reference to site (in reference table) 
     * @var string
     */
    protected $_siteColumn = 'site_id';
    
    /**
     * column of primary key of entity (in entity table) 
     * @var string
     */    
    protected $_keyColumn = 'id';
    
    
    /**
     * _siteColumn getter
     * @return string
     */    
    public function getSiteColumn()
    {
        return $this->_siteColumn;
    }
    
    /**
     * _siteColumn setter
     * @param string
     * @return $this
     */
    public function setSiteColumn($value)
    {
        $this->_siteColumn = $value;
        return $this->_siteColumn;
    }
    

    /**
     * _keyColumn getter
     * @return string
     */    
    public function getKeyColumn()
    {
        return $this->_keyColumn;
    }
    
    /**
     * _keyObjectField setter
     * @param string
     * @return $this
     */
    public function setKeyColumn($value)
    {
        $this->_keyColumn = $value;
        return $this->_keyColumn;
    }
    
    
    
    
    /**
     * set values for all settings by array 
     * @param array $config
     */
    public function setConfig(array $config)
    {
        if (array_key_exists('keyColumn', $config)) {
            $this->setKeyColumn($config['keyColumn']);
        }
        if (array_key_exists('siteColumn', $config)) {
            $this->setSiteColumn($config['siteColumn']);
        }        
        if (array_key_exists('mapper', $config)) {
            $this->setMapper($config['mapper']);
        }
        if (array_key_exists('siteMapper', $config)) {
            $this->setSiteMapper($config['siteMapper']);
        }
        return $this;
    }
    
    
    
    public function addMultisitingToSelect(Zend_Db_Select $select, $siteId = NULL)
    {
        $table = $this->getMapper()->getTable();
        $tableName = $table->getTableName();
        $prefix = $table->getColumnPrefix() . $table->getPrefixSeparator();
        $siteColumn = $tableName.'.'.$prefix.$this->getSiteColumn();
        $siteId = $this->_currentSiteId;
        if ($siteId === NULL) {
            $siteService = Model_Service::factory('site');/* should be refactored */
            $siteId = $siteService->getCurrent()->id;
        }        
        $select->where($siteColumn.' = ?', $siteId);
        return $select;
        
    }
    
    
    /**
     * mapper::saveComplex
     */
    public function onBeforeSaveComplex(Model_Object_Interface $obj, array $values, $isNew = FALSE)
    {
        $siteField = $this->getSiteColumn();
        if ($isNew AND $this->_currentSiteId AND ! (int) $obj->{$siteField} AND ! (int) $values[$siteField]) {
            $obj->{$siteField} = $this->_currentSiteId;
        }
        return $obj;
    }
        

    
}