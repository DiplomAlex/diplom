<?php

class Model_Mapper_Db_ContentLinkable_Abstract extends Model_Mapper_Db_Abstract implements Model_Mapper_Db_ContentLinkable_Interface
{ 
    
    protected $_refTableLinkedField = NULL;
    
    protected $_relationMode = NULL;
    
    protected $_contentTitleField = NULL;
    protected $_contentTable = NULL;
    protected $_contentDescriptionTable = NULL;
    
    protected $_contentSeoIdField = NULL;
    
    protected $_language = NULL;
    
    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        if ($this->_contentTable) {
            $select = $this->_joinContentToSelect($select);            
        }
        else {
            $select = $this->_joinRefTableToSelect($select);
        }
        return $select;
    }
    
    protected function _postSaveComplex($obj, $values)
    {
        $this->linkObjectToContent($obj, $obj->content_type, $obj->content_id);
        return $obj;
    }
    
    protected function _joinContentToSelect(Zend_Db_Select $select)
    {
        if ($this->getRelationMode() == Model_Mapper_Db_ContentLinkable_Interface::RELATION_ONE_TO_MANY) {
            $table = $this->getTable();
            $tableName = $table->getTableName();
            $tablePrefix = $table->getColumnPrefix().$table->getPrefixSeparator();            
            $contTable = $this->getContentTable();
            $contTableName = $contTable->getTableName();
            $contPrefix = $contTable->getColumnPrefix().$contTable->getPrefixSeparator();
            $contDescTable = $this->getContentDescriptionTable();
            $contDescTableName = $contDescTable->getTableName();
            $contDescPrefix = $contDescTable->getColumnPrefix().$contDescTable->getPrefixSeparator();
            $refTable = $this->getInjector()->getObject(Model_Mapper_Db_ContentLinkable_Interface::REF_TABLE_DEFAULT_CLASS);
            $refTableName = $refTable->getTableName();
            $refPrefix = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
            $seoIdField = $this->getContentSeoIdField();
            $cols = array($tablePrefix.'content_id'=>$contPrefix.'id');
            if (in_array($contPrefix.$seoIdField, $contTable->info(Zend_Db_Table::COLS))) {
                $cols[$tablePrefix.'content_seo_id']= $contPrefix.$seoIdField;
            }            
            $select = $this->_joinRefTableToSelect($select)            
                           ->joinLeft(array('content'=>$contTableName), 
                                      'content.'.$contPrefix.'id = '.$refTableName.'.'.$refPrefix.'content_id',
                                      $cols)
                           ;
            if ($this->_language) {
                $select->joinLeft(array('content_desc'=>$contDescTableName), 
                                  'content_desc.'.$contDescPrefix.$contPrefix.'id = '.$refTableName.'.'.$refPrefix.'content_id AND content_desc.'.$contDescPrefix.'language_id = '.$this->getLanguage()->id, 
                                  array($tablePrefix.'content_title'=>$contDescPrefix.$this->getContentTitleField()))
                       ;
            }
        }
        return $select;        
    }
    
    protected function _joinRefTableToSelect(Zend_Db_Select $select)
    {
        $table = $this->getTable();
        $tableName = $table->getTableName();
        $tablePrefix = $table->getColumnPrefix().$table->getPrefixSeparator();                    
        $refTable = $this->getInjector()->getObject(Model_Mapper_Db_ContentLinkable_Interface::REF_TABLE_DEFAULT_CLASS);
        $refTableName = $refTable->getTableName();
        $refPrefix = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
        $linkedField = $this->getRefTableLinkedField();
        $select->joinLeft(array($refTableName), 
                          $refTableName.'.'.$refPrefix.$linkedField.' = '.$tableName.'.'.$tablePrefix.'id', 
                          array(
                              $tablePrefix.'content_type' => $refPrefix.'content_type',
                              $tablePrefix.'content_id' => $refPrefix.'content_id',
                          ));
        return $select;
    }
    
    public function fetchComplexByContent($contentType, $contentId, $fetch = TRUE)
    {
        $refTable = $this->getInjector()->getObject(Model_Mapper_Db_ContentLinkable_Interface::REF_TABLE_DEFAULT_CLASS);
        $refTableName = $refTable->getTableName();
        $refPrefix = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
        $select = $this->fetchComplex(NULL, FALSE);
        if ($this->getRelationMode() != Model_Mapper_Db_ContentLinkable_Interface::RELATION_ONE_TO_MANY) {
            $select = $this->_joinRefTableToSelect($select);
        }
        $select->where($refTableName.'.'.$refPrefix.'content_type = ?', $contentType)
               ->where($refTableName.'.'.$refPrefix.'content_id = ?', $contentId)
                       ;
                  
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }
    
    public function paginatorFetchComplexByContent($contentType, $contentId, $rowsPerPage, $page)
    {
        $select = $this->fetchComplexByContent($contentType, $contentId, FALSE);
        $result = $this->paginator($select, $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
        return $result;
    }
    
    public function linkObjectToContent(Model_Object_Interface $obj, $contentType, $contentId)
    {
        /**
         *
         * 1) если n к 1-му : delete from refTable where linked_id = $obj->id
         * 2) если n к n : delete from refTable where linked_id = $obj->id AND content_type=$contentType AND $content_id = $contentId
         * 3) insert into refTable (linked_id, content_type, content_id) values ($obj->id, $contentType, $contentId)
         * 
         */
        $refTable = $this->getInjector()->getObject(Model_Mapper_Db_ContentLinkable_Interface::REF_TABLE_DEFAULT_CLASS);
        $refTableName = $refTable->getTableName();
        $refPrefix = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
        $linkedField = $this->getRefTableLinkedField();
        if ($this->getRelationMode() == Model_Mapper_Db_ContentLinkable_Interface::RELATION_ONE_TO_MANY) {
            $refTable->delete(array($refPrefix.$linkedField.' = ?' => $obj->id));
        }
        else {
            $refTable->delete(array($refPrefix.$linkedField.' = ?' => $obj->id,
                                    $refPrefix.'content_type = ?' => $contentType, 
                                    $refPrefix.'content_id = ?' => $contentId,));
        }
        $refTable->insert(array(
            $refPrefix.$linkedField => $obj->id,
            $refPrefix.'content_type' => $contentType,
            $refPrefix.'content_id' => $contentId,
        ));
        return $refTable->getAdapter()->lastInsertId();
    }

    public function unlinkByContent($contentType, $contentId)
    {
        /**
         * 1) Delete from refTable by contentType and contentId
         * 
         * 2) Delete all linked elements which are not linked anymore to any content (sanityze)
         * Example:
         * delete from gallery
         * left join gallery_ref
         * on gallery_ref_gallery_id = gallery_id
         * where isnull(gallery_ref_id)
         */
        $refTable = $this->getInjector()->getObject(Model_Mapper_Db_ContentLinkable_Interface::REF_TABLE_DEFAULT_CLASS);
        $refTableName = $refTable->getTableName();
        $refPrefix = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
        $refTable->delete(array($refPrefix.'content_type = ?' => $contentType, 
                                $refPrefix.'content_id = ?' => $contentId,));
        $table = $this->getTable();
        $tableName = $table->getTableName();
        $tablePrefix = $table->getColumnPrefix().$table->getPrefixSeparator();
        $linkedField = $this->getRefTableLinkedField();
        $table->getAdapter()->query('DELETE '.$tableName.' FROM '.$tableName.'
                                     LEFT JOIN '.$refTableName.'
                                     ON '.$refPrefix.$linkedField.' = '.$tablePrefix.'id
                                     WHERE ISNULL('.$refPrefix.'id)');
        return $this;
    }
    
    
    public function setRefTableLinkedField($field)
    {
        $this->_refTableLinkedField = $field;
        return $this;
    }
    
    public function getRefTableLinkedField()
    {
        if ( ! $this->_refTableLinkedField) {
            $this->_refTableLinkedField = Model_Mapper_Db_ContentLinkable_Interface::REF_TABLE_DEFAULT_LINKED_FIELD;
        }
        return $this->_refTableLinkedField;
    }

    public function setRelationMode($mode)
    {
        $this->_relationMode = $mode;
        return $this;
    }
    
    public function getRelationMode()
    {
        if ( ! $this->_relationMode) {
            $this->_throwException('relationMode was not set before, but is requested (set its value in mapper class definition)');
        }
        return $this->_relationMode;
    }
    
    public function setContentTable(Model_Db_Table_Interface $table)
    {
        $this->_contentTable = $table;
        return $this;
    }
    
    public function getContentTable()
    {
        if ( ! $this->_contentTable) {
            $this->_throwException('setContentTable($table) was not called before in helper\'s init() method');
        }
        return $this->_contentTable;
    }
    
    public function setContentDescriptionTable(Model_Db_Table_Interface $table)
    {
        $this->_contentDescriptionTable = $table;
        return $this;
    }
    
    public function getContentDescriptionTable()
    {
        if ( ! $this->_contentDescriptionTable) {
            $this->_throwException('setContentDescriptionTable($table) was not called before in helper\'s init() method');
        }
        return $this->_contentDescriptionTable;
    }
    
    public function setContentTitleField($field)
    {
        $this->_contentTitleField = $field;
        return $this;
    }
    
    public function getContentTitleField()
    {
        if ( ! $this->_contentTitleField) {
            $this->_throwException('setContentTitleField($field) was not called before in helper\'s init() method');
        }
        return $this->_contentTitleField;
    }

    public function setContentSeoIdField($field)
    {
        $this->_contentSeoIdField = $field;
        return $this;
    }
    
    public function getContentSeoIdField()
    {
        if ( ! $this->_contentSeoIdField) {
            $this->_contentSeoIdField = Model_Mapper_Db_ContentLinkable_Interface::CONTENT_DEFAULT_SEO_ID_FIELD;
        }
        return $this->_contentSeoIdField;
    }       
        
    
    public function setLanguage(Model_Object_Interface $lang)
    {
        $this->_language = $lang;
        return $this;
    }
    
    public function getLanguage()
    {
        if ( ! $this->_language) {
            $this->_throwException('setLanguage($lang) was not called before in helper\'s init() method');
        }
        return $this->_language;
    }
    
    
    
    
}