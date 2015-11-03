<?php

class Model_Service_Helper_Content_Abstract extends Model_Service_Helper_Abstract implements Model_Service_Helper_Content_Interface
{ 
    
    protected $_contentType = NULL;
    protected $_refTableClass = NULL;
    protected $_relationMode = NULL;
    protected $_linkedServiceClass = 'Model_Service_Content_Interface';
    protected $_linkedService = NULL;
    protected $_contentTitleField = NULL;
        
    public function init()
    {
        $linkedMapper = $this->getLinkedService()->getMapper();
        $contentMapper = $this->getService()->getMapper();
        $linkedMapper->getInjector()
                     ->inject(Model_Mapper_Db_ContentLinkable_Interface::REF_TABLE_DEFAULT_CLASS, $this->_getRefTableClass());
        $linkedMapper->setContentTable($contentMapper->getTable())
                     ->setContentDescriptionTable($contentMapper->getInjector()->getObject('Model_Db_Table_Description'))
                     ->setContentTitleField($this->_getContentTitleField())
                     ;
        if ($plugin = $contentMapper->getPlugin('Description')) {
            $linkedMapper->setLanguage($plugin->getCurrentLanguage());
        }                
        $this->_postInit();
    }
    
    protected function _postInit()
    {
    }
    
    public function getLinkedService()
    {
        if ( ! $this->_linkedService) {
            $injector = $this->getService()->getInjector();
            if ( ! $linkedServiceClass = $this->_getOption('linkedServiceClass')) {
                $this->_throwException('the "linkedServiceClass" option was not inited in service\'s init() method or helper\'s class definition');
            }
            if ($injector->hasInjection($linkedServiceClass)) {
                $this->_linkedService = $injector->getObject($linkedServiceClass);
            }
            else {
                $this->_throwException('service must have injection for "'.$linkedServiceClass.'"');
            }
        }
        return $this->_linkedService;
    } 

    protected function _getRefTableClass()
    {
        if ( ! $result = $this->_getOption('refTableClass')) {
            $this->_throwException('the "refTableClass" option was not inited in service\'s init() method');            
        }
        return $result;
    }
    
    public function getContentType()
    {
        if ( ! $result = $this->_getOption('contentType')) {
            $this->_throwException('the "contentType" option was not inited in service\'s init() method');
        }
        return $result;
    }
    
    public function _getContentTitleField()
    {
        if ( ! $result = $this->_getOption('contentTitleField')) {
            $this->_throwException('the "contentTitleField" option was not inited in service\'s init() method');
        }
        return $result;
    }

    protected function _getRelationMode()
    {
        if ( ! $result = $this->_getOption('relationMode')) {
            $this->_throwException('the "relationMode" option was not inited in service\'s init() method or helper\'s class definition');            
        }
        return $result;
    }
    
    
    public function getLinkedToContent($contentId)
    {
        $result = $this->getLinkedService()->getMapper()->fetchComplexByContent($this->getContentType(), $contentId);
        return $result;
    }

    public function paginatorGetLinkedToContent($contentId, $rowsPerPage, $page)
    {
        $result = $this->getLinkedService()->getMapper()->paginatorFetchComplexByContent($this->getContentType(), $contentId, $rowsPerPage, $page);
        return $result;
    }
    
    
    public function clearLinkedToContent($contentId)
    {
        $this->getLinkedService()->getMapper()->unlinkByContent($this->getContentType(), $contentId);
        return $this;
    }
}