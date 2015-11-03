<?php

interface Model_Mapper_Db_ContentLinkable_Interface extends Model_Mapper_Db_Interface
{
    
    const REF_TABLE_DEFAULT_CLASS = 'Model_Db_Table_Ref';
    const REF_TABLE_DEFAULT_LINKED_FIELD = 'linked_id';
    const CONTENT_DEFAULT_SEO_ID_FIELD = 'seo_id';
    
    const RELATION_ONE_TO_MANY = 'one';
    const RELATION_MANY_TO_MANY = 'many';
    
    public function fetchComplexByContent($contentType, $contentId);
    
    public function setRefTableLinkedField($field);
    public function getRefTableLinkedField();

    public function setRelationMode($field);
    public function getRelationMode();
    
    public function setContentTable(Model_Db_Table_Interface $table);    
    public function getContentTable();
    
    public function setContentDescriptionTable(Model_Db_Table_Interface $table);
    public function getContentDescriptionTable();
    
    public function setContentTitleField($field);
    public function getContentTitleField();
    
}