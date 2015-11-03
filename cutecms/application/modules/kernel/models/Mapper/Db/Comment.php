<?php

class Model_Mapper_Db_Comment extends Model_Mapper_Db_ContentLinkable_Abstract
{
    
    protected $_refTableLinkedField = 'comment_id';
    
    protected $_relationMode = Model_Mapper_Db_ContentLinkable_Interface::RELATION_ONE_TO_MANY;
    
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_Comment',
        'Model_Collection_Interface' => 'Model_Collection_Comment',  
        'Model_Db_Table_Interface' => 'Model_Db_Table_Comment',
        'Model_Mapper_Db_Plugin_Resource',
        'Model_Db_Table_Resources',
        'Model_Db_Table_Tree' => 'Model_Db_Table_CommentTree',
        'Model_Db_Plugin_NestedSets',
        'Model_Mapper_Db_Plugin_Tree',
        'Model_Db_Table_Ref' => 'Model_Db_Table_CommentRef',
    );
    
    protected $_sortingOrder = NULL; 
    
    public function init()
    {
        $this->addPlugin('Resource',$this->getInjector()->getObject(
            'Model_Mapper_Db_Plugin_Resource', 
            array('rc_id'),
            Zend_Registry::get('config')->images->previewMaxCount 
        ));
        $treeTable = $this->getInjector()->getObject('Model_Db_Table_Tree');
        $this->addPlugin(
                'Tree',
                $this->getInjector()->getObject(
                    'Model_Mapper_Db_Plugin_Tree',
                    array(
                        'mapper' => $this,
                        'table' => $treeTable,
                        'refColumn' => 'comment_tree_id',
                        'moveNewToFirst' => FALSE,
                        'nestedSets' => $this->getInjector()->getObject(
                                                    'Model_Db_Plugin_NestedSets',
                                                    $treeTable->getTableName(),
                                                    $treeTable->getColumnPrefix(),
                                                    Zend_Db_Table_Abstract::getDefaultAdapter()
                                               )
                    )
                )
        );        
    }


    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        
        $select->joinLeft(array('adder'=>'user'), 'adder.user_id = comment_adder_id', array(
            'comment_adder_login' => 'adder.user_login',
            'comment_adder_name' => 'adder.user_name',
            'comment_adder_email' => 'adder.user_email',
        ));
        
        if ($this->_contentTable) {
            $select = $this->_joinContentToSelect($select);            
        }
        else {
            $select = $this->_joinRefTableToSelect($select);
        }        
        $select
               ->joinLeft(
                    array('children' => 'comment_tree'),
                    'children.comment_tree_parent = comment_tree.comment_tree_id',
                    array('comment_children_count' => 'count(distinct children.comment_tree_id)')
                 )
               ->joinLeft(
                    array('all_children' => 'comment_tree'),
                    'all_children.comment_tree_left > comment_tree.comment_tree_left AND all_children.comment_tree_right < comment_tree.comment_tree_right',
                    array('comment_all_children_count' => 'count(distinct all_children.comment_tree_id)')
                 )
                 ->group('comment.comment_id')        
               ->reset('order')
               ->order($this->_getSortingOrder())
               ;
        return $select;
    }
    
    protected function _preSaveComplex(Model_Object_Interface $obj, array $values)
    {
        if ( ! $obj->id) {
            if ($values['parent_id']) {
                $parent = $this->fetchComplexOneById($values['parent_id']);
                $obj->parent_id = $parent->tree_id;
                $obj->content_type = $parent->content_type;
                $obj->content_id = $parent->content_id;
            }
            else if ($values['content_type'] AND $values['content_id']) {
                $obj->content_type = $values['content_type'];
                $obj->content_id = $values['content_id'];                
            }
            else {
                $this->_throwException('neither parent_id nor content_type with content_id were set in $values when saving new comment');
            }
        }
        return $obj;
    }
    
    protected function _getSortingOrder()
    {
        if ($this->_sortingOrder === NULL) {
            $this->setSortingOrder(array('tree'));
        }
        return $this->_sortingOrder;
    }
    
    public function setSortingOrder(array $order)
    {
        $sqlOrder = array();
        foreach ($order as $key=>$dir) {
            if (is_numeric($key)) {
                $alias = $dir;
            }
            else {
                $alias = $key;
            }
            if ($alias == 'new') {
                $sqlOrder[] = 'comment.comment_date_added DESC';
            }
            else if ($alias == 'tree') {
                $sqlOrder[] = 'comment_tree.comment_tree_left ASC';
            }
        }
        $this->_sortingOrder = $sqlOrder;
        return $this;
    }
        
    
    public function deleteResource($rcId)
    {
        $table = $this->getInjector()->getObject('Model_Db_Table_Resources');
        $table->delete(array('rc_id = ?' => $rcId));
        return $this;
    }
    
    public function setResourceFromRequest(Model_Object_Interface $obj)
    {
        $this->getPlugin('Resource')->saveUploadedResource($obj, array('resource_rc_id_del'=>FALSE));
    }
    
    
    public function fetchParentIds($id)
    {
        $curr = $this->fetchOneById($id);
        $ns = $this->getPlugin('Tree')->getNestedSets();
        $ids = array();
        foreach ($ns->getAllParents($curr->tree_id) as $row) {
            $ids[]=$row['comment_tree_id'];
        }
        return $ids;        
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
        $ids = $this->fetchParentIds($id);
        $select = $this->fetchComplex($where, FALSE)
                       ->where('comment_comment_tree_id IN (?)', $ids)
                       ->reset('order')
                       ->order('comment_tree_left')
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