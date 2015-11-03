<?php

class Model_Mapper_Db_Plugin_Multisite_ManyToMany extends Model_Mapper_Db_Plugin_Multisite_Abstract
{
    
    protected $_hasTable = FALSE;    
        
    /**
     * ALL COLUMN NAMES SHOULD BE SPECIFIED WITHOUT PREFIX!!!
     */
    
    /**
     * column of reference to entity (in reference table) 
     * @var string
     */
    protected $_refEntityColumn = 'ref_id';

    /**
     * column of reference to site (in reference table) 
     * @var string
     */
    protected $_refSiteColumn = 'site_id';

    /**
     * column of primary key of entity (in entity table) 
     * @var string
     */    
    protected $_entityKeyColumn = 'id';

    /**
     * column where serialized list of linked sites ids is stored (in entity table) 
     * @var string
     */    
    protected $_sitesValuesField = 'site_ids';
    
    /**
     * field of entity object where unserialized array of linked sites ids is stored (in entity OBJECT)
     * @var string
     */    
    protected $_sitesObjectField = 'site_ids';

    /**
     * addon columns in relation table
     * @var array
     */
    protected $_addonColumns = array();


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
     * relations (reference) table
     * @var Model_Db_Table_Interface
     */
    protected $_refTable = NULL;
    
    
    /**
     * _refTable getter
     * @return string
     */    
    public function getRefTable()
    {
        if ($this->_refTable === NULL) {
            $this->_throwException('refTable is NULL (looks like it was not initialized in mapper\'s init()) ');
        }
        return $this->_refTable;
    }
    
    /**
     * _refTable setter
     * @param string
     * @return $this
     */
    public function setRefTable(Model_Db_Table_Interface $refTable)
    {
        $this->_refTable = $refTable;
        return $this;
    }

    /**
     * _refEntityColumn getter
     * @return string
     */    
    public function getRefEntityColumn()
    {
        return $this->_refEntityColumn;
    }

    /**
     * _refEntityColumn setter
     * @param string
     * @return $this
     */
    public function setRefEntityColumn($column)
    {
        $this->_refEntityColumn = $column;
        return $this;
    }

    /**
     * _refSiteColumn getter
     * @return string
     */    
    public function getRefSiteColumn()
    {
        return $this->_refSiteColumn;
    }
    
    /**
     * _refSiteColumn setter
     * @param string
     * @return $this
     */
    public function setRefSiteColumn($column)
    {
        $this->_refSiteColumn = $column;
        return $this;
    }

    /**
     * _entityKeyColumn getter
     * @return string
     */    
    public function getEntityKeyColumn()
    {
        return $this->_entityKeyColumn;
    }
        
    /**
     * _entityKeyColumn setter
     * @param string
     * @return $this
     */
    public function setEntityKeyColumn($keyField)
    {
        $this->_entityKeyColumn = $keyField;
        return $this;
    }

    /**
     * _sitesValuesField getter
     * @return string
     */    
    public function getSitesValuesField()
    {
        return $this->_sitesValuesField;
    }
    
    /**
     * _sitesValuesField setter
     * @param string
     * @return $this
     */
    public function setSitesValuesField($field)
    {
        $this->_sitesValuesField = $field;
        return $this->_sitesValuesField;
    }

    /**
     * _sitesObjectField getter
     * @return string
     */    
    public function getSitesObjectField()
    {
        return $this->_sitesObjectField;
    }
    
    /**
     * _sitesObjectField setter
     * @param string
     * @return $this
     */
    public function setSitesObjectField($field)
    {
        $this->_sitesObjectField = $field;
        return $this->_sitesObjectField;
    }

    /**
     * _addonColumns getter
     * @return array
     */
    public function getAddonColumns()
    {
        return $this->_addonColumns;
    }
    
    /**
     * _addonColumns setter
     * @param array $columns
     * @return $this
     */
    public function setAddonColumns(array $columns)
    {
        $this->_addonColumns = $columns;
        return $this;
    }
        
    
    /**
     * set values for all settings by array 
     * @param array $config
     */
    public function setConfig(array $config)
    {
        if (array_key_exists('refTable', $config)) {
            $this->setRefTable($config['refTable']);
        }
        if (array_key_exists('mapper', $config)) {
            $this->setMapper($config['mapper']);
        }
        if (array_key_exists('siteMapper', $config)) {
            $this->setSiteMapper($config['siteMapper']);
        }
        if (array_key_exists('addonColumns', $config)) {
            $this->setAddonColumns($config['addonColumns']);
        }
        if (array_key_exists('refEntityColumn', $config)) {
            $this->setRefEntityColumn($config['refEntityColumn']);
        }
        if (array_key_exists('refSiteColumn', $config)) {
            $this->setRefSiteColumn($config['refSiteColumn']);
        }
        if (array_key_exists('entityKeyColumn', $config)) {
            $this->setEntityKeyColumn($config['entityKeyColumn']);
        }
        if (array_key_exists('sitesValuesField', $config)) {
            $this->setSitesValuesField($config['sitesValuesField']);
        }
        if (array_key_exists('sitesObjectField', $config)) {
            $this->setSitesValuesField($config['sitesObjectField']);
        }
        return $this;
    }
        
    /**
     * is called when object fields values were readed from db
     * @param Model_Object_Interface $obj
     * @param array $values
     * @param bool
     * @return Model_Object_Interface
     */
    public function onMapObject(Model_Object_Interface $obj, array $values, $addedPrefix = TRUE)
    {
        $entityTable = $this->getMapper()->getTable();
        $entityPrefix = $entityTable->getColumnPrefix() . $entityTable->getPrefixSeparator();
        $field = $this->getSitesValuesField();
        if ($addedPrefix) {
            $field = $entityPrefix . $field;
        }
        if ( ! array_key_exists($field, $values)) {
            App_Debug::dump(debug_backtrace(), 'full backtrace');
            $this->_throwException('Check for mapper method that fetching data - it should fetch site_ids column too!');
        }
        $value = $values[$field];
        $obj->{$this->getSitesObjectField()} = unserialize($value);
        return $obj;
    }

    /**
     * is called when object is preparing for saving to db
     * @param Model_Object_Interface $obj
     * @param array $values
     * @return array
     */
    public function onUnmapObject(Model_Object_Interface $obj, array $values)
    {
        $entityTable = $this->getMapper()->getTable();
        $entityPrefix = $entityTable->getColumnPrefix() . $entityTable->getPrefixSeparator();
        $value = serialize($obj->{$this->getSitesObjectField()});
        $values[$entityPrefix.$this->getSitesValuesField()] = $value;        
        return $values;
    }
    
    /**
     * mapper::saveComplex
     */
    public function onAfterSaveComplex(Model_Object_Interface $obj, array $values, $isNew = FALSE)
    {
        $sitesField = $this->getSitesObjectField();        
        if (array_key_exists($sitesField, $values)) {
            $mapper = $this->getMapper();
            $refTable = $this->getRefTable();
            $refTableName = $refTable->getTableName();
            $refPrefix = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
            $refEntityColumn = $refPrefix.$this->getRefEntityColumn();
            $refSiteColumn = $refPrefix.$this->getRefSiteColumn();;
            $insertData = array();
            $refTable->delete(array($refEntityColumn.' = ?'=>$obj->id));        
            $ids = $obj->{$sitesField};
            if (is_array($ids)) {
                foreach ($ids as $siteId) {
                    $mapper->poolInsert($refTableName, array($refEntityColumn=>$obj->id, $refSiteColumn=>$siteId));
                }                
                $mapper->poolInsert($refTableName);
            }
        }
        return $obj;
    }
    
        
    

    /**
     * add multisiting left join and where to the select query
     * @param Zend_Db_Select $select
     * @return Zend_Db_Select $select
     */
    public function addMultisitingToSelect(Zend_Db_Select $select, $siteId = NULL)
    {
        $refTable = $this->getRefTable();
        $refTableName = $refTable->getTableName();
        $refPrefix = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
        $refEntityColumn = $refTableName.'.'.$refPrefix.$this->getRefEntityColumn();
        $refSiteColumn = $refTableName.'.'.$refPrefix.$this->getRefSiteColumn();;
        
        $entityTable = $this->getMapper()->getTable();
        $entityTableName = $entityTable->getTableName();
        $entityPrefix = $entityTable->getColumnPrefix() . $entityTable->getPrefixSeparator();
        $entityKeyColumn = $entityTableName.'.'.$entityPrefix.$this->getEntityKeyColumn();
        
        $columns = $this->getAddonColumns();

        if ($siteId === NULL) {
            $siteService = Model_Service::factory('site');/* should be refactored */
            $siteId = $siteService->getCurrent()->id;
        }
        
        $select->joinLeft($refTableName,
                          $refEntityColumn.' = '.$entityKeyColumn,
                          $columns)
               ->where($refSiteColumn.' = '.$siteId)
               ;
        return $select;
    }
    
    /**
     * fetches all linked sites from db into collection
     * @param Model_Object_Interface|int object|id of object
     * @return Model_Collection_Interface
     */
    public function fetchLinkedSites($obj)
    {
        if ($obj instanceof Model_Object_Interface) {
            $ids = $obj->{$this->getSitesObjectField()};
        }
        else if (is_int($obj)) {
            $id = $obj;
            $refTable = $this->getRefTable();
            $refTableName = $refTable->getTableName();
            $refPrefix = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
            $refEntityColumn = $refPrefix.$this->getRefEntityColumn();
            $refSiteColumn = $refPrefix.$this->getRefSiteColumn();;
            $select = $this->getRefTable()->select()
                                          ->distinct()
                                          ->from($refTableName, array($refSiteColumn))
                                          ->where($refEntityColumn.' = ?', $id)
                                          ;
            $ids = array();
            if ($rows = $select->query()->fetchAll()) {
                foreach ($rows as $row) {
                    $ids []= $row[$refSiteColumn];
                }
            }
        }
        else {
            $this->_throwException('parameter of this function should be int or instanceof Model_Object_Interface');
        }
        $lang = Model_Service::factory('language');
        $this->getSiteMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());        
        $coll = $this->getSiteMapper()->fetchByIdArray($ids);
        return $coll;
    }
    
        
    public function linkToSiteByIdArray(array $ids, array $siteIds)
    {
        if ( ! empty($ids) AND ! empty($siteIds)) {
            $refTable = $this->getRefTable();
            $refTableName = $refTable->getTableName();
            $refPrefix = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
            $refEntityColumn = $refPrefix.$this->getRefEntityColumn();
            $refSiteColumn = $refPrefix.$this->getRefSiteColumn();        
            $query = 'REPLACE '.$refTableName.' ('.$refEntityColumn.','.$refSiteColumn.') VALUES ';
            $values = array();
            foreach ($ids as $id) {
                foreach ($siteIds as $siteId) {
                    $values []= '('.$id.', '.$siteId.')';
                }
            }
            $valuesStr = implode($values, ',');
            $query .= $valuesStr;
            App_Debug::dump($query);
            $refTable->getAdapter()->query($query);
        }
        App_Debug::dump($ids);
        App_Debug::dump($siteIds);
        return $this;
    }

    public function unlinkFromSiteByIdArray(array $ids, array $siteIds)
    {
        if ( ! empty($ids) AND ! empty($siteIds)) {
            $refTable = $this->getRefTable();
            $refTableName = $refTable->getTableName();
            $refPrefix = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
            $refEntityColumn = $refPrefix.$this->getRefEntityColumn();
            $refSiteColumn = $refPrefix.$this->getRefSiteColumn();
            $query = 'DELETE FROM '.$refTableName.' WHERE '.$refEntityColumn.' IN ('.implode($ids, ',').') AND '.$refSiteColumn.' IN ('.implode($siteIds, ',').')';
            $refTable->getAdapter()->query($query);
        }
        App_Debug::dump($ids);
        return $this;
    }
    
}
