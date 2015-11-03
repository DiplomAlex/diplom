<?php

class Model_Mapper_Db_ArticleTopic extends Model_Mapper_Db_Abstract
{ 
    
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_ArticleTopic',
        'Model_Collection_Interface' => 'Model_Collection_ArticleTopic',
        'Model_Db_Table_Interface' => 'Model_Db_Table_ArticleTopic',
        'Model_Mapper_Db_Plugin_Sorting',
        'Model_Mapper_Db_Plugin_Description',    
        'Model_Db_Table_Description' => 'Model_Db_Table_ArticleTopicDescription',
        'Model_Db_Table_Tree' => 'Model_Db_Table_ArticleTopicTree',
        'Model_Db_Plugin_NestedSets',
        'Model_Mapper_Db_Plugin_Tree',
        'Model_Mapper_Db_Plugin_Multisite' => 'Model_Mapper_Db_Plugin_Multisite_ManyToMany',
        'Model_Mapper_Db_Site',
        'Model_Db_Table_SiteRef' => 'Model_Db_Table_ArticleTopicSiteRef',                        
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
                        'refColumn' => 'topic_id',
                        'descFields' => array(
                            'name', 'brief', 'full',
                            'html_title', 'meta_keywords', 'meta_description',
                        ),
                    )
                  )
        );
        $treeTable = $this->getInjector()->getObject('Model_Db_Table_Tree');
        $this->addPlugin(
                'Tree',
                $this->getInjector()->getObject(
                    'Model_Mapper_Db_Plugin_Tree',
                    array(
                        'mapper' => $this,
                        'table' => $treeTable,
                        'refColumn' => 'topic_tree_id',
                        'nestedSets' => $this->getInjector()->getObject(
                                                    'Model_Db_Plugin_NestedSets',
                                                    $treeTable->getTableName(),
                                                    $treeTable->getColumnPrefix(),
                                                    Zend_Db_Table_Abstract::getDefaultAdapter()
                                                )
                    )
                )
        );
        $this->addPlugin('Multisite', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Multisite', array(
            'siteMapper' => $this->getInjector()->getObject('Model_Mapper_Db_Site'),
            'refTable' => $this->getInjector()->getObject('Model_Db_Table_SiteRef'),
            'refEntityColumn' => 'topic_id',
        )));                                    
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
    

    public function fetchTopicsList()
    {
        $select = $this->fetchComplex(NULL, FALSE)
                       ->order(array('topic_tree_left ASC', 'topic_desc_name ASC'))
                       ;
        $data = $select->query()->fetchAll();
        $currTopicId = NULL;
        $currLevel = NULL;
        $list = array();
        foreach ($data as $row) {
            if ($currTopicId != $row['topic_id']) {
                $currTopicId = $row['topic_id'];
                $currLevel = $row['topic_tree_level'];
                $list []= array('id'=>'topic_'.$currTopicId, 
                                'rel'=>'topic',
                                'tree_level'=>$currLevel,
                                'name'=>$row['topic_desc_name']);
            }
        }
        return $list;
    }

    /**
     * @param int
     * @return Model_Collection_ArticleTopic
     */
    public function fetchAllByArticleId($id)
    {
        $select = $this->getTable()
                              ->select()->setIntegrityCheck(FALSE)
                              ->from(array('topic'=>'article_topic'), array('topic_id', 'topic_site_ids'))
                              ->joinLeft(
                                    array('topic_desc'=>'article_topic_description'),
                                    'topic_desc_language_id = '.$this->getPlugin('Description')->getCurrentLanguage()->id
                                        . ' AND topic_desc_topic_id = topic_id',
                                    array('topic_desc_name')
                                )
                              ->joinLeft(
                                    array('atr'=>'article_topic_ref'),
                                    'at_ref_topic_id = topic_id',
                                    array()
                                )
                              ->where('at_ref_article_id = ?', $id)
                              ;
        $collection = $this->makeComplexCollection($select->query()->fetchAll());
        return $collection;
    }
    
    /**
     * get all parents of object
     * @param int
     * @param bool
     * @param mixed array|string
     * @param bool
     * @return mixed Zend_Db_Select|Model_Collection_Interface
     */
    public function fetchComplexParentsOf($id, $where = NULL, $fetch = TRUE)
    {
        $curr = $this->fetchOneById($id);
        $ns = $this->getPlugin('Tree')->getNestedSets();
        $ids = array();
        foreach ($ns->getAllParents($curr->tree_id) as $row) {
            $ids[]=$row['topic_tree_id'];
        }
        $select = $this->fetchComplex($where, FALSE)
                       ->where('topic_topic_tree_id IN (?)', $ids)
                       ->reset('order')
                       ->order('topic_tree_left')
                       ;
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }

    


    /**
     * @param int parent_id
     * @param mixed string|array
     * @param bool
     * @return mixed Zend_Db_Select | Model_Collection_Interface
     */
    public function fetchComplexByParent($parentId, $where = NULL, $fetch = TRUE)
    {
        $select = $this->fetchComplex($where, FALSE)
                       ->joinLeft(
                            array('children' => 'article_topic_tree'),
                            'children.topic_tree_parent = article_topic.topic_topic_tree_id',
                            array('topic_children_count' => 'count(children.topic_tree_id)')
                         )
                       ->group('article_topic.topic_id')
                       ;
        if ( (int) $parentId) {
            $select->joinLeft(
                        array('parent' => $this->getTable()->getTableName()),
                        'parent.topic_topic_tree_id = article_topic_tree.topic_tree_parent',
                        array()
                     )
                   ->where('parent.topic_id = ?', $parentId)
                   ;
        }
        else {
            $select->where('article_topic_tree.topic_tree_parent = ?', $this->getPlugin('Tree')->getNestedSets()->getRootId());
        }
        $select->order('article_topic_tree.topic_tree_left ASC');

        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }



    /**
     * @param string $treeId
     * @return Model_Object_Interface
     */
    public function fetchOneByTreeId($treeId)
    {
        if (empty($treeId)) {
            throw new Model_Mapper_Db_Exception('tree_id should be set');
        }
        if ( ! $rows = $this->fetchComplex(array('topic_tree_id = ?' => $treeId))) {
            throw new Model_Mapper_Db_Exception('table row with tree_id="'.$treeId.'" not found!');
        }
        else {
            $object = $rows->current();
        }
        return $object;
    }

    
    
    public function fetchChildrenIdsByRootIdArray(array $rootIds)
    {
        $select = $this->getTable()->select()->from(array('root'=>'article_topic'), array())
                       ->joinLeft(array('root_tree'=>'article_topic_tree'), 'root_tree.topic_tree_id = root.topic_topic_tree_id', array())
                       ->joinLeft(array('kid_tree'=>'article_topic_tree'), 'kid_tree.topic_tree_left >= root_tree.topic_tree_left AND kid_tree.topic_tree_right <= root_tree.topic_tree_right', array())
                       ->joinLeft(array('kid'=>'article_topic'), 'kid.topic_topic_tree_id = kid_tree.topic_tree_id', array('topic_id'))
                       ->where('root.topic_id IN ('.implode($rootIds,',').')')
                       ;
        $rows = $select->query()->fetchAll();
        $ids = array();
        foreach ($rows as $row) {
            $ids[]=$row['topic_id'];
        }
        return $ids;
    }

    
    
    
}