<?php

class Issues_Model_Mapper_Db_Comment extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Collection_Interface' => 'Issues_Model_Collection_Comment',
        'Model_Object_Interface'     => 'Issues_Model_Object_Comment',
        'Model_Db_Table_Interface'   => 'Issues_Model_Db_Table_Comments',
        'Model_Db_Table_Tree'        => 'Issues_Model_Db_Table_CommentsTree',
        'Model_Mapper_Db_Plugin_Tree',
        'Model_Db_Plugin_NestedSets',
    );

    public function init()
    {
        $treeTable = $this->getInjector()->getObject('Model_Db_Table_Tree');
        $this->addPlugin(
            'Tree',
            $this->getInjector()->getObject(
                'Model_Mapper_Db_Plugin_Tree',
                array(
                    'mapper' => $this,
                    'table' => $treeTable,
                    'nestedSets' => $this->getInjector()->getObject('Model_Db_Plugin_NestedSets',
                                                                    $treeTable->getTableName(),
                                                                    $treeTable->getColumnPrefix(),
                                                                    Zend_Db_Table_Abstract::getDefaultAdapter()),
                )
            )
        );
    }

    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        return $select;
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
        if ( ! $rows = $this->fetchComplex(array('ic_ic_tree_id = ?' => $treeId))) {
            throw new Model_Mapper_Db_Exception('table row with tree_id="'.$treeId.'" not found!');
        }
        else {
            $object = $rows->current();
        }
        return $object;
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
                            array('children' => 'issue_comment_tree'),
                            'children.ic_tree_parent = issue_comment_tree.ic_tree_id',
                            array('issue_children_count' => 'count(children.issue_tree_id)')
                         )
                       ->group('issue.issue_id')
                       ;
        if ( (int) $parentId) {
            $select->joinLeft(
                        array('parent' => $this->getTable()->getTableName()),
                        'parent.ic_ic_tree_id = issue_comment_tree.ic_tree_parent',
                        array()
                     )
                   ->where('parent.ic_id = ?', $parentId)
                   ;
        }
        else {
            $select->where('issue_comment_tree.ic_tree_parent = ?', $this->getPlugin('Tree')->getNestedSets()->getRootId());
        }
        $select->order('issue_comment_tree.ic_tree_left ASC');

        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
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
            $ids[]=$row['ic_tree_id'];
        }
        $select = $this->fetchComplex($where, FALSE)
                       ->where('ic_ic_tree_id IN (?)', $ids)
                       ->reset('order')
                       ->order('ic_tree_left')
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