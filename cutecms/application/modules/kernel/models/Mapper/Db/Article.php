<?php

class Model_Mapper_Db_Article extends Model_Mapper_Db_Abstract
{ 
    
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_Article',
        'Model_Collection_Interface' => 'Model_Collection_Article',
        'Model_Db_Table_Interface' => 'Model_Db_Table_Article',
        'Model_Mapper_Db_Plugin_Sorting',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Resource',
        'Model_Db_Table_Description' => 'Model_Db_Table_ArticleDescription',
        'Model_Db_Table_ArticleTopicRef',
        'Model_Db_Table_ArticleTopic',
        'Model_Db_Table_ArticleTopicDescription',
        'Model_Db_Table_Resources',    
        'Model_Mapper_Db_Plugin_Multisite' => 'Model_Mapper_Db_Plugin_Multisite_ManyToMany',
        'Model_Mapper_Db_Site',
        'Model_Db_Table_SiteRef' => 'Model_Db_Table_ArticleSiteRef',                        
    );
    
    public function init()
    {
        $this->addPlugin(
            'Description',
            $this ->getInjector()
                  ->getObject(
                    'Model_Mapper_Db_Plugin_Description',
                    array(
                        'mapper' => $this,
                        'table' => $this->getInjector()->getObject('Model_Db_Table_Description'),
                        'refColumn' => 'article_id',
                        'descFields' => array(
                            'title', 'brief', 'text', 'author',
                            'html_title', 'meta_keywords', 'meta_description',
                        ),
                    )
                  )
        )
        ->addPlugin('Resource',$this->getInjector()->getObject('Model_Mapper_Db_Plugin_Resource', array('rc_id'), Zend_Registry::get('config')->images->previewMaxCount))
        ->addPlugin('Sorting', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Sorting'))
        ;
        $this->addPlugin('Multisite', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Multisite', array(
            'siteMapper' => $this->getInjector()->getObject('Model_Mapper_Db_Site'),
            'refTable' => $this->getInjector()->getObject('Model_Db_Table_SiteRef'),
            'refEntityColumn' => 'article_id',
        )));
        if ($config = Zend_Registry::get('config')->images->previewDimensions->{$this->getInjector()->getInjection('Model_Object_Interface')}) {
            $this->getPlugin('Resource')->setPreviewDimensions($config->toArray());
        }                                
    }
    
    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        /* if replation to topics is n-1 then we can select current topic of article */
        $obj = $this->getInjector()->getObject('Model_Object_Interface');
        if ($obj->getMode() == Model_Object_Article::TOPIC_RELATION_ONE_TO_MANY) {
            $refTable = $this->getTable('article-topic-ref');
            $refPrefix = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
            $topicTable = $this->getTable('article-topic');            
            $topicPrefix = $topicTable->getColumnPrefix().$topicTable->getPrefixSeparator();
            $tdescTable = $this->getTable('article-topic-description');            
            $tdescPrefix = $tdescTable->getColumnPrefix().$tdescTable->getPrefixSeparator();
            $articlePrefix = $this->getTable()->getColumnPrefix().$this->getTable()->getPrefixSeparator();
            $lang = $this->getPlugin('Description')->getCurrentLanguage();
            $select->joinLeft(array('topic_ref' => $refTable->getTableName()), 'topic_ref.'.$refPrefix.'article_id = '.$articlePrefix.'id', array())
                   ->joinLeft(array('topic'=>$topicTable->getTableName()), 
                              'topic_ref.'.$refPrefix.'topic_id = topic.'.$topicPrefix.'id', 
                              array($articlePrefix.'topic_id'=>$topicPrefix.'id',
                                    $articlePrefix.'topic_seo_id'=>$topicPrefix.'seo_id',))
                   ->joinLeft(array('tdesc'=>$tdescTable->getTableName()), 
                              'tdesc.'.$tdescPrefix.'topic_id = topic.'.$topicPrefix.'id AND tdesc.'.$tdescPrefix.'language_id = '.$lang->id, 
                              array($articlePrefix.'topic_name'=>$tdescPrefix.'name',))
                   ;
        }
        return $select;
    }
    
    /**
     * @param Model_Object_Interface
     * @param array
     * @return Model_Object_Interface
     */
    protected function _postSaveComplex(Model_Object_Interface $obj, array $values)
    {
        $refTable = $this->getTable('article-topic-ref');
        $refTable->delete(array('at_ref_article_id = ?' => $obj->id));
        if (is_array($values['topics'])) {
            $topics = $values['topics'];
        }
        else if (is_array($obj->topics)) {
            $topics = $obj->topics;
        }
        else if ( ! empty($values['topics'])) {
            $topics = array($values['topics']);
        }
        else if ( ! empty($obj->topics)) {
            $topics = array($obj->topics);
        }
        else {
            $topics = array();
        }
        if (is_array($topics)) {
            $i = 0;
            foreach ($topics as $topic) {
                $refTable->insert(array(
                    'at_ref_article_id' => $obj->id,
                    'at_ref_topic_id' => $topic,
                    'at_ref_sort' => $i++,
                ));
            }
        }
        return $obj;
    }
    
    public function fetchComplexBySeoIdOrId($id, $fetch = TRUE) {
        if (is_numeric($id)) {
            $field = 'id';
        }
        else {
            $field = 'seo_id';
        }
        $table = $this->getTable();
        $result = $this->fetchComplex(
                      array($table->getColumnPrefix().$table->getPrefixSeparator().$field .' = ? ' => $id), 
                      $fetch
                  );
        if ($result->count()) {
            $result = $result->current();
        }
        else {
            $result = FALSE;
        }
        return $result;
    }
    
    public function fetchComplexByTopic($topic, $fetch = TRUE) {
        $query = $this->fetchComplex(NULL, FALSE);
        if ($topic > 0) {
            $query->distinct(TRUE)
                  ->joinLeft(array('atr'=>'article_topic_ref'), 'atr.at_ref_article_id = article_id', array())
                  ->where('atr.at_ref_topic_id = ?', $topic)
                  ;
        }
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($query->query()->fetchAll());
        }
        else {
            $result = $query;
        }        
        return $result;
    }
    
    /**
     * @param array $topic ids
     * @param bool $fetch
     * @return mixed Zend_Db_Select|Model_Collection_Interface
     */
    public function fetchComplexByTopics(array $topics, $fetch = TRUE) {
        $query = $this->fetchComplex(NULL, FALSE);
        if ($topic > 0) {
            $query->distinct(TRUE)
                  ->joinLeft(array('atr'=>'article_topic_ref'), 'atr.at_ref_article_id = article_id', array())
                  ->where('atr.at_ref_topic_id = IN(?)', $topics)
                  ;
        }
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($query->query()->fetchAll());
        }
        else {
            $result = $query;
        }        
        return $result;
    }
        
    /**
     * @param int
     * @param int
     * @param int
     * @return Zend_Paginator
     */
    public function paginatorFetchComplexByTopic($topic, $rows, $page)
    {
        $query = $this->fetchComplexByTopic($topic, FALSE);
        return $this->paginator($query, $rows, $page);
    }
    
    
    public function fetchComplexLast($limit, $fetch = TRUE)
    {
        $select = $this->fetchComplex(NULL, FALSE)
                  ->reset('order')->order(array('article_date_added DESC'))
                  ->limit($limit)
                  ;
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }
    
    
    
}