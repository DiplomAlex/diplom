<?php

class Model_Mapper_Db_Plugin_Tree extends Model_Mapper_Db_Plugin_Abstract
{


    /**
     * @var Model_Db_Table_Tree
     */
    protected $_table = NULL;

    protected $_mainTableName = NULL;
    protected $_mainPrefix = NULL;
    protected $_treeTableName = NULL;
    protected $_treePrefix = NULL;

    protected $_moveNewToFirst = TRUE; 
    
    
    /**
     * @var string
     */
    protected $_refColumn = NULL;

    /**
     * @var array
     */
    protected $_positions = array(
        'first', 'prev', 'next', 'last',
    );

    /**
     * nested sets instance
     *
     * @var Model_Db_NestedSets
     */
    protected $_nestedSets = NULL;

    /**
     * plugin has table - if true - mapping simple object uses its fields
     * @var bool
     */
    protected $_hasTable = FALSE;
    

    public function __construct(array $config = NULL)
    {
        $this->setMapper($config['mapper']);
        $this->_table = $config['table'];
        $this->_nestedSets = $config['nestedSets'];
        $this->_refColumn = $config['refColumn'];
        
        if (array_key_exists('moveNewToFirst', $config)) {
            $this->_moveNewToFirst = $config['moveNewToFirst'];
        }

        $this->_mainTableName = $this->getMapper()->getTable()->getTableName();
        $this->_mainPrefix = $this->getMapper()->getTable()->getColumnPrefix();
        $this->_treeTableName = $this->_table->getTableName();
        $this->_treePrefix = $this->_table->getColumnPrefix();
    }


    /**
     * observer for beforeSaveComplex event
     * @param Model_Object_Interface $obj
     * @param array $values
     * @param bool $isNew
     * @return mixed
     */
    public function onBeforeSaveComplex(Model_Object_Interface $obj, array $values, $isNew = FALSE)
    {
        if ( ! $this->_nestedSets->countAll()) {
            $this->_nestedSets->Clear();
        }
        /**
         * when saving new object we need to create a treenode for it (with InsertNode)
         * then set object value of tree_id to new id of tree node
         */

        if ( ! $obj->parent_id) {
            $obj->parent_id = $this->_nestedSets->getRootId();
        }
        if ($isNew === TRUE) {
            $obj->tree_id = $this->_nestedSets->Insert($obj->parent_id);
        }
        else {
            $this->_nestedSets->MoveAll($obj->tree_id, $obj->parent_id);
        }
    }

    /**
     * observer for afterSaveComplex event
     * @param Model_Object_Interface $obj
     * @param array $values
     * @param bool $isNew
     * @return mixed
     */
    public function onAfterSaveComplex(Model_Object_Interface $obj, array $values, $isNew = FALSE)
    {
        if (($isNew === TRUE) AND ($this->_getMoveNewToFirst())) {
            $this->changeSorting($obj, 'first');
        }
    }


    /**
     * unmap tree fields of object
     * @param Model_Object_Interface object itself
     * @param array values prepared before this plugin
     * @return array
     */
    public function onUnmapObject(Model_Object_Interface $obj, array &$values)
    {
        $values[$this->_mainPrefix.'_'.$this->_refColumn] = $obj->tree_id;
        $values[$this->_treePrefix.'_parent'] = $obj->parent_id;
        return $values;
    }


    /**
     * map tree fields of object
     * @param Model_Object_Interface object itself
     * @param array values prepared before this plugin
     * @return Model_Object_Interface
     */
    public function onMapObject(Model_Object_Interface $obj, array $values)
    {
        $obj->tree_id = @$values[$this->_mainPrefix.'_'.$this->_refColumn];
        if (isset($values[$this->_treePrefix.'_parent'])) {
            $obj->parent_id = $values[$this->_treePrefix.'_parent'];
        }
        return $obj;
    }



    /**
     * map tree fields to object
     * @param Model_Object_Interface object itself
     * @param array values to map
     * @return Model_Object_Interface
     */
    public function onBuildComplex(Model_Object_Interface $object, array $values)
    {
        $maps = array(
            'tree_level' => $this->_treePrefix . '_level',
            'tree_left' => $this->_treePrefix . '_left',
            'tree_right' => $this->_treePrefix . '_right',
            'parent_id' => $this->_treePrefix . '_parent',
        );
        foreach ($maps as $key=>$field) {
            if ( ! $object->hasElement($key)) {
                throw new Model_Mapper_Db_Plugin_Exception('object "'.get_class($object).'" should have field "'.$key.'"');
            }
            $object->{$key} = @$values[$field];
        }
        return $object;
    }



    /**
     * @param Zend_Db_Select
     * @return Zend_Db_Select
     */
    public function onFetchComplex(Zend_Db_Select $select)
    {
        $select->joinLeft(
                    $this->_treeTableName,
                    $this->_mainPrefix.'_'.$this->_refColumn.' = '.$this->_treePrefix.'_id',
                    array(
                        $this->_treePrefix.'_parent',
                        $this->_treePrefix.'_left',
                        $this->_treePrefix.'_right',
                        $this->_treePrefix.'_level'
                    )
                 );
        return $select;
    }


    /**
     * delete tree node
     * @param Model_Object_Interface
     * @return $this
     */
    public function onBeforeDelete(Model_Object_Interface $object)
    {
        $this->_nestedSets->Delete($object->tree_id);
        return $this;
    }

    /**
     * @param int
     * @return array
     */
    public function getNodeValues($treeId)
    {
        if ( ! $treeId) {
            $treeId = $this->_nestedSets->getRootId();
        }
        $select =   $this->_table->select()
                               ->from(
                                    $this->_treeTableName,
                                    array(
                                        $this->_treePrefix.'_id',
                                        $this->_treePrefix.'_left',
                                        $this->_treePrefix.'_right',
                                        $this->_treePrefix.'_level',
                                        $this->_treePrefix.'_parent',
                                    )
                                 )
                               ->where($this->_treePrefix.'_id = ?', new Zend_Db_Expr($treeId));
        $node = $select->query()->fetch();
        return
            array(
                'parent_id' => $node[$this->_treePrefix.'_parent'],
                'tree_left' => $node[$this->_treePrefix.'_left'],
                'tree_right' => $node[$this->_treePrefix.'_right'],
                'tree_level' => $node[$this->_treePrefix.'_level'],
            );
    }


    /**
     * get collection of categories in order of full ajared tree
     * excluding branch starting from $id
     * @param int
     * @param bool
     * @param bool
     * @return Model_Collection_Interface
     */
    public function getTreeWithoutBranch($id = NULL, $includeCurrentId = FALSE, $addRoot = FALSE)
    {
        if ( (int) $id > 0) {
            $select = $this->_table  ->select()->setIntegrityCheck(FALSE)
                                     ->from(
                                            $this->_treeTableName,
                                            array(
                                                $this->_treePrefix.'_id',
                                                $this->_treePrefix.'_left',
                                                $this->_treePrefix.'_right',
                                                $this->_treePrefix.'_level',
                                                $this->_treePrefix.'_parent',
                                            )
                                       )
                                     ->joinLeft(
                                            $this->_mainTableName,
                                            $this->_mainTableName.'.'.$this->_mainPrefix.'_'.$this->_refColumn.' = '.$this->_treeTableName.'.'.$this->_treePrefix.'_id',
                                            array()
                                       )
                                     ->where($this->_mainPrefix.'_id = ?', new Zend_Db_Expr($id))
                                     ->limit(1);
            $br = $select->query()->fetch();
        }
        else {
            $br = array();
        }
        $treePrefix = $this->_treeTableName.'.'.$this->_treePrefix;
        
        $condition = array();
        if ($addRoot === FALSE) {
            $condition[$treePrefix.'_level > ?'] = new Zend_Db_Expr('0');
        }
        if (isset($br[$treePrefix.'_id']) AND ($br[$treePrefix.'_id'] > 0)) {
            if ($includeCurrentId === FALSE) {
                $condition[$treePrefix.'_id <> ?'] = new Zend_Db_Expr($br[$treePrefix.'_id']);
            }
            $condition['not('.$treePrefix.'_left between '
                        . $br[$treePrefix.'_left']
                        . ' AND '
                        . $br[$treePrefix.'_right'].')'] = '';

        }

        $select = $this->getMapper()->fetchComplex($condition, FALSE)
                     ->where($treePrefix.'_id > 0')
                     ->order($treePrefix.'_left ASC');
        $data = $select->query()->fetchAll();

        /*$collection = $this->getMapper()->makeComplexCollection($data);*/

        return /*$collection;*/ $data;
    }

        



    /**
     * change position of object in list (sort field)
     * @param Model_Object_Interface
     * @param string (first|last|prev|next)
     */
    public function changeSorting(Model_Object_Interface $obj, $position)
    {
        if ( ! in_array($position, $this->getPositions())) {
            throw new Model_Mapper_Db_Plugin_Exception('wrong position ("'.$position.'") to change sorting in '.__CLASS__);
        }
        switch ($position) {
            case 'first':
                $first = $this->_nestedSets->getFirstSibling($obj->tree_id);
                $this->_nestedSets->ChangePositionAll($obj->tree_id, $first[$this->_treePrefix.'_id'], 'before');
                break;
            case 'last':
                $last = $this->_nestedSets->getLastSibling($obj->tree_id);
                $this->_nestedSets->ChangePositionAll($obj->tree_id, $last[$this->_treePrefix.'_id'], 'after');
                break;
            case 'prev':
                $prev = $this->_nestedSets->getPrevSibling($obj->tree_id);
                $this->_nestedSets->ChangePositionAll($obj->tree_id, $prev[$this->_treePrefix.'_id'], 'before');
                break;
            case 'next':
                $next = $this->_nestedSets->getNextSibling($obj->tree_id);
                $this->_nestedSets->ChangePositionAll($obj->tree_id, $next[$this->_treePrefix.'_id'], 'after');
                break;
        }
        return $this;
    }
    
    protected function _getMoveNewToFirst()
    {
        return $this->_moveNewToFirst; 
    }
    
    public function setMoveNewToFirst($value)
    {
        $this->_moveNewToFirst = $value;
        return $this;
    }


    /**
     * @return array
     */
    public function getPositions()
    {
        return $this->_positions;
    }


    /**
     * for purposes of mapper
     * @return Model_Db_Plugin_NestedSets
     */
    public function getNestedSets()
    {
        return $this->_nestedSets;
    }

}