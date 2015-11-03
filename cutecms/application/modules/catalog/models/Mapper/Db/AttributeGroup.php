<?php

class Catalog_Model_Mapper_Db_AttributeGroup extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Catalog_Model_Db_Table_AttributeGroup',
        'Model_Object_Interface' => 'Catalog_Model_Object_AttributeGroup',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_AttributeGroup',
        'Model_Db_Table_Description' => 'Catalog_Model_Db_Table_AttributeGroupDescription',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Db_Plugin_NestedSets',
        'Model_Mapper_Db_Plugin_Resource',
        'Model_Db_Table_Tree' => 'Catalog_Model_Db_Table_AttributeGroupTree',
        'Model_Mapper_Db_Plugin_Tree',
        'Model_Db_Table_Resources',
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
                        'refColumn' => 'ag_id',
                        'descFields' => array(
                            'name', 'brief',
                        ),
                    )
                  )
        )
        ->addPlugin(
                'Tree',
                $this->getInjector()->getObject(
                    'Model_Mapper_Db_Plugin_Tree',
                    array(
                        'mapper' => $this,
                        'table' => $this->getInjector()->getObject('Model_Db_Table_Tree'),
                        'refColumn' => 'ag_tree_id',
                        'nestedSets' => $this->getInjector()->getObject(
                                                    'Model_Db_Plugin_NestedSets',
                                                    'attribute_group_tree',
                                                    'ag_tree',
                                                    Zend_Db_Table_Abstract::getDefaultAdapter()
                                                )
                    )
                )
          )
        ->addPlugin('Resource',$this->getInjector()->getObject('Model_Mapper_Db_Plugin_Resource', array('rc_id'), 1))
        ;
    }


    /**
     * @param int
     * @return Model_Collection_AttributeGroup
     */
    public function fetchAllByAttributeId($id)
    {
        $select = $this->getTable()
                              ->select()->setIntegrityCheck(FALSE)
                              ->from(array('ag'=>'attribute_group'), array('ag_id'))
                              ->joinLeft(
                                    array('ag_desc'=>'attribute_group_description'),
                                    'ag_desc_language_id = '.$this->getPlugin('Description')->getCurrentLanguage()->id
                                        . ' AND ag_desc_ag_id = ag_id',
                                    array('ag_desc_name')
                                )
                              ->joinLeft(
                                    array('agr'=>'attribute_group_ref'),
                                    'agr_ag_id = ag_id',
                                    array()
                                )
                              ->where('agr_attr_id = ?', $id)
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
            $ids[]=$row['ag_tree_id'];
        }
        $select = $this->fetchComplex($where, FALSE)
                       ->where('ag_ag_tree_id IN (?)', $ids)
                       ->reset('order')
                       ->order('ag_tree_left')
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
                            array('children' => 'attribute_group_tree'),
                            'children.ag_tree_parent = attribute_group.ag_ag_tree_id',
                            array('ag_children_count' => 'count(children.ag_tree_id)')
                         )
                       ->group('attribute_group.ag_id')
                       ;
        if ( (int) $parentId) {
            $select->joinLeft(
                        array('parent' => $this->getTable()->getTableName()),
                        'parent.ag_ag_tree_id = attribute_group_tree.ag_tree_parent',
                        array()
                     )
                   ->where('parent.ag_id = ?', $parentId)
                   ;
        }
        else {
            $select->where('attribute_group_tree.ag_tree_parent = ?', $this->getPlugin('Tree')->getNestedSets()->getRootId());
        }
        $select->order('attribute_group_tree.ag_tree_left ASC');

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
        if ( ! $rows = $this->fetchComplex(array('ag_tree_id = ?' => $treeId))) {
            throw new Model_Mapper_Db_Exception('table row with tree_id="'.$treeId.'" not found!');
        }
        else {
            $object = $rows->current();
        }
        return $object;
    }


    public function fetchGroupsWithAttributesList()
    {

        $select = $this->fetchComplex(NULL, FALSE)
                       ->joinLeft('attribute_group_ref', 'ag_id = agr_ag_id', array())
                       ->joinLeft('attribute', 'agr_attr_id = attr_id', array('attr_id', 'attr_type', 'attr_variants_xml', 'attr_default_value_int', 'attr_default_value_datetime', 'attr_default_value_decimal', 'attr_default_value_string', 'attr_default_value_text', 'attr_default_value_variant'))
                       ->joinLeft('attribute_description', 'attr_desc_attr_id = attr_id AND attr_desc_language_id = '.Model_Service::factory('language')->getCurrent()->id)
                       ->order(array('ag_tree_left ASC', 'ag_desc_name ASC', 'attr_desc_name ASC'))
                       ;
        $data = $select->query()->fetchAll();
        $currAgId = NULL;
        $currLevel = NULL;
        $list = array();
        $serviceAttribute = Model_Service::factory('catalog/attribute');
        foreach ($data as $row) {
            if ($currAgId != $row['ag_id']) {
                $currAgId = $row['ag_id'];
                $currLevel = $row['ag_tree_level'];
                $list []= array('id'=>'group_'.$currAgId, 'rel'=>'group',
                                'tree_level'=>$currLevel,
                                'name'=>$row['ag_desc_name']);
            }
            if ( ! is_null($row['attr_id'])) {
                $list []= array('id'=>$row['attr_id'], 'rel'=>'attr',
                                'tree_level'=>$currLevel+1,
                                'name'=>$row['attr_desc_name'],
                                'type' => $row['attr_type'],
                                'default_value' => $row['attr_default_value_'.$row['attr_type']],
                                'variants' => $serviceAttribute->parseVariantsFromXML($row['attr_variants_xml']));
            }
        }
        return $list;
    }


    public function fetchGroupsList()
    {
        $select = $this->fetchComplex(NULL, FALSE)
                       ->order(array('ag_tree_left ASC', 'ag_desc_name ASC'))
                       ;
        $data = $select->query()->fetchAll();
        $currAgId = NULL;
        $currLevel = NULL;
        $list = array();
        foreach ($data as $row) {
            if ($currAgId != $row['ag_id']) {
                $currAgId = $row['ag_id'];
                $currLevel = $row['ag_tree_level'];
                $list []= array('id'=>'group_'.$currAgId, 'rel'=>'group',
                                'tree_level'=>$currLevel,
                                'name'=>$row['ag_desc_name']);
            }
        }
        return $list;
    }



}