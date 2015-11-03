<?php

class Catalog_Model_Mapper_Db_Category extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Catalog_Model_Db_Table_Category',
        'Model_Object_Interface' => 'Catalog_Model_Object_Category',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Category',

        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Sorting',
        'Model_Mapper_Db_Plugin_Tree',
        'Model_Mapper_Db_Plugin_Resource',

        'Model_Db_Table_Description' => 'Catalog_Model_Db_Table_CategoryDescription',
        'Model_Db_Table_Tree' => 'Catalog_Model_Db_Table_CategoryTree',
        'Model_Db_Table_Resources',

        'Model_Db_Plugin_NestedSets',

        /*'App_Resource_Adapter_Image',*/
    
        'Model_Mapper_Db_Plugin_Multisite' => 'Model_Mapper_Db_Plugin_Multisite_ManyToMany',
        'Model_Mapper_Db_Site',
        'Model_Db_Table_SiteRef' => 'Catalog_Model_Db_Table_CategorySiteRef',
    
    );

    public function init()
    {
        $objClass = $this->getInjector()->getInjection('Model_Object_Interface');
        $this->addPlugin(
                'Description',
                $this ->getInjector()
                      ->getObject(
                        'Model_Mapper_Db_Plugin_Description',
                        array(
                            'mapper' => $this,
                            'table' => $this->getInjector()->getObject('Model_Db_Table_Description'),
                            'refColumn' => 'category_id',
                            'descFields' => array(
                                'name', 'brief', 'full',
                                'html_title', 'meta_keywords', 'meta_description',
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
                        'refColumn' => 'category_tree_id',
                        'nestedSets' => $this->getInjector()->getObject(
                                                    'Model_Db_Plugin_NestedSets',
                                                    'category_tree',
                                                    'category_tree',
                                                    Zend_Db_Table_Abstract::getDefaultAdapter()
                                                )
                    )
                )
          )
        ->addPlugin('Resource',$this->getInjector()->getObject(
    		'Model_Mapper_Db_Plugin_Resource', 
             array('rc_id'), 
             count(Zend_Registry::get('config')->images->previewDimensions->{$objClass})
        ))        
        ->addPlugin('Multisite', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Multisite', array(
            'siteMapper' => $this->getInjector()->getObject('Model_Mapper_Db_Site'),
            'refTable' => $this->getInjector()->getObject('Model_Db_Table_SiteRef'),
            'refEntityColumn' => 'category_id',
        )))
        ;
    }

    /**
     * @param Model_Object_Interface
     * @param array
     * @return Model_Object_Interface
     */
    protected function _preSaveComplex(Model_Object_Interface $obj, array $values)
    {
        $key = 'description_language_'.Model_Service::factory('language')->getCurrent()->id.'_name';
        if (empty($values['seo_id']) AND isset($values[$key])) {
            $obj->seo_id = App_Utf8::urlClean($values[$key]);
        }
        return $obj;
    }

    
    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        $select
                       ->joinLeft(
                            array('children' => 'category_tree'),
                            'children.category_tree_parent = category_tree.category_tree_id',
                            array('category_children_count' => 'COUNT(DISTINCT children.category_tree_id)')
                         )
                       ->joinLeft(
                            array('itemref' => 'category_item_ref'),
                            'category.category_id = itemref.ci_ref_category_id',
                            array('category_items_count' => 'COUNT(DISTINCT itemref.ci_ref_item_id)')
                         )
                         ->group('category.category_id')
                         ;
        
        return $select;
    }


    /**
     * @param string $seoId
     * @return Model_Object_Interface
     */
    public function fetchOneBySeoId($seoId)
    {
        if (empty($seoId)) {
            throw new Model_Mapper_Db_Exception('id should be set');
        }
        if ( ! $rows = $this->fetchComplex(array('category_seo_id = ?' => $seoId))) {
            throw new Model_Mapper_Db_Exception('table row with seo_id="'.$seoId.'" not found!');
        }
        else {
            $object = $rows->current();
        }
        return $object;
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
        if ( ! $rows = $this->fetchComplex(array('category_category_tree_id = ?' => $treeId))) {
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
                       /*->joinLeft(
                            array('children' => 'category_tree'),
                            'children.category_tree_parent = category_tree.category_tree_id',
                            array('category_children_count' => 'count(children.category_tree_id)')
                         )
                       ->joinLeft(
                            array('itemref' => 'category_item_ref'),
                            'category.category_id = itemref.ci_ref_category_id',
                            array('category_items_count' => 'COUNT(DISTINCT itemref.ci_ref_item_id)')
                         )
                         ->group('category.category_id')*/
                       ;
        if ( ! empty($parentId) AND ( ! is_numeric($parentId))) {
            $select->joinLeft(
                        array('parent' => $this->getTable()->getTableName()),
                        'parent.category_category_tree_id = category_tree.category_tree_parent',
                        array()
                     )
                   ->where('parent.category_seo_id = ?', $parentId)
                   ;
        }
        else if ( (int) $parentId) {
            $select->joinLeft(
                        array('parent' => $this->getTable()->getTableName()),
                        'parent.category_category_tree_id = category_tree.category_tree_parent',
                        array()
                     )
                   ->where('parent.category_id = ?', $parentId)
                   ;
        }
        else {
            $select->where('category_tree.category_tree_parent = ?', $this->getPlugin('Tree')->getNestedSets()->getRootId());
        }
        $select->order('category_tree.category_tree_left ASC');

        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }
    
    public function fetchComplexActiveByParent($parentId, $fetch = TRUE)
    {
        return $this->fetchComplexByParent($parentId, array('category.category_status > 0'), $fetch);
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
            $ids[]=$row['category_tree_id'];
        }
        $select = $this->fetchComplex($where, FALSE)
                       ->where('category_category_tree_id IN (?)', $ids)
                       ->reset('order')
                       ->order('category_tree_left')
                       ;
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }
    
    public function fetchChildrenIdsByRootIdArray(array $rootIds)
    {
        $select = $this->getTable()->select()->from(array('root'=>'category'), array())
                       ->joinLeft(array('root_tree'=>'category_tree'), 'root_tree.category_tree_id = root.category_category_tree_id', array())
                       ->joinLeft(array('kid_tree'=>'category_tree'), 'kid_tree.category_tree_left >= root_tree.category_tree_left AND kid_tree.category_tree_right <= root_tree.category_tree_right', array())
                       ->joinLeft(array('kid'=>'category'), 'kid.category_category_tree_id = kid_tree.category_tree_id', array('category_id'))
                       ->where('root.category_id IN ('.implode($rootIds,',').')')
                       ;
        $rows = $select->query()->fetchAll();
        $ids = array();
        foreach ($rows as $row) {
            $ids[]=$row['category_id'];
        }
        return $ids;
    }


    /**
     * @param int
     * @return Model_Collection_Category
     */
    public function fetchAllByItemId($id)
    {
        $select = $this->getTable()
                              ->select()->setIntegrityCheck(FALSE)
                              ->from(array('category'), array('category_id', 'category_site_ids'))
                              ->joinLeft(
                                    array('category_description'),
                                    'category_desc_language_id = '.$this->getPlugin('Description')->getCurrentLanguage()->id
                                        . ' AND category_desc_category_id = category_id',
                                    array('category_desc_name')
                                )
                              ->joinLeft(
                                    array('category_item_ref'),
                                    'ci_ref_category_id = category_id',
                                    array()
                                )
                              ->where('ci_ref_item_id = ?', $id)
                              ;
        $collection = $this->makeComplexCollection($select->query()->fetchAll());
        return $collection;
    }

    
    /**
     * get array of category_id fields searched by category_guid field values
     * @param array - (guid, guid, ...)
     * @return array - (guid=>id, guid=>id, ...)
     */
    public function fetchIdsByGuids(array $guids) 
    {
        if ( ! empty($guids)) {
            $table = $this->getTable();
            $pref = $table->getColumnPrefix();
            $sep = $table->getPrefixSeparator();
            $select = $table->select()
                            ->from($table->getTableName(), 
                                   array('id'=>$pref.$sep.'id', 'guid'=>$pref.$sep.'guid'))
                            ->where($pref.$sep.'guid IN (?)', $guids)
                            ;
            $result = array();
            if ($rows = $select->query()->fetchAll()) {            
                foreach ($rows as $row) {
                    $result[$row['guid']] = $row['id'];
                }
            }
        }
        else {
            $result = array();
        }
        return $result;
    }

    
    /**
     * get array of category_tree_id fields searched by category_guid field values
     * @param array - (guid, guid, ...)
     * @return array - (guid=>id, guid=>id, ...)
     */
    public function fetchTreeIdsByGuids(array $guids) 
    {
        if ( ! empty($guids)) {
            $table = $this->getTable();
            $pref = $table->getColumnPrefix();
            $sep = $table->getPrefixSeparator();
            $select = $table->select()
                            ->from($table->getTableName(), 
                                   array('tree_id'=>$pref.$sep.$pref.$sep.'tree_id', 
                                         'guid'=>$pref.$sep.'guid'))
                            ->where($pref.$sep.'guid IN (?)', $guids)
                            ;
            $result = array();
            if ($rows = $select->query()->fetchAll()) {            
                foreach ($rows as $row) {
                    $result[$row['guid']] = $row['tree_id'];
                }
            }
        }
        else {
            $result = array();
        }
        return $result;
    }
    
    /**
     * fetch all categories sorted by tree level
     * @param bool $fetch
     * @return Model_Collection_Interface
     */
    public function fetchFullTreeSortedByLevel($fetch = TRUE, $activeOnly = FALSE)
    {
        $select = $this->fetchComplex(NULL, FALSE)
                       ->reset('order')
                       ->order(array('category_tree_level ASC', 'category_tree_parent ASC', 'category_desc_name ASC'));
        if ($activeOnly === TRUE) {
            $select->where('category_status > 0');
        }
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }
    
    

    /**
     * saves collection with poolUpdate and poolInsert
     * @param Model_Collection_Interface $coll
     * @return $this
     */
    public function saveImportedCollection(Model_Collection_Interface $coll)
    {           
        /**
         * tables used: category, category_description, category_tree
         */
        $catTable      = $this->getTable();
        $catTableName  = $catTable->getTableName();
        $catPref       = $catTable->getColumnPrefix().$catTable->getPrefixSeparator();
        
        $descTable     = $this->getInjector()->getObject('Model_Db_Table_Description');
        $descTableName = $descTable->getTableName();
        $descPref      = $descTable->getColumnPrefix().$descTable->getPrefixSeparator();
        
        $treeTable     = $this->getInjector()->getObject('Model_Db_Table_Tree');
        $treeTableName = $treeTable->getTableName();
        $treePref      = $treeTable->getColumnPrefix().$treeTable->getPrefixSeparator();
        
        $lang = Model_Service::factory('language')->getCurrent();
        
        $insertedGuids = array();
        $parentGuids = array();
        $objGuids = array();
        
        foreach ($coll as $obj) {
            $catFields = array(
                $catPref.'status' => 1,
                $catPref.'guid' => $obj->guid,
                $catPref.'parent_guid' => $obj->parent_guid,
                $catPref.'seo_id' => App_Utf8::urlClean($obj->name),
            );            
            $descFields = array(
                $descPref.'language_id'=>$lang->id,
                $descPref.'name'=>$obj->name,
                $descPref.'brief'=>$obj->brief,
                $descPref.'full'=>$obj->full,
            );
            if (($obj->id) AND ($obj->delete)) {
                $this->delete($obj->id);
            }
            else if (( ! $obj->id) AND ( ! $obj->delete)) {
                $this->poolInsert($catTableName, $catFields); 
                $insertedGuids []= $obj->guid;
                $parentGuids []= $obj->parent_guid;
            }
            else if (($obj->id) AND ( ! $obj->delete)) {
                $this->poolUpdate($catTableName, $catFields, array($catPref.'id = ?'=>$obj->id));
                $parentGuids []= $obj->parent_guid;
                $descFields[$descPref.'category_id'] = $obj->id;
                $this->poolUpdate($descTableName, $descFields, array(
                    $descPref.'language_id = ?' => $lang->id,
                    $descPref.'category_id = ?' => $obj->id,
                ));
            }
            $objGuids[$obj->id]=$obj->guid;
        }

        $this->poolInsert($catTableName);
        $this->poolUpdate($catTableName);
        
        $this->poolUpdate($descTableName);
        
        /**
         * insert descriptions and tree nodes
         * update category_tree_id
         */
        $insertedIds = $this->fetchIdsByGuids($insertedGuids);
        $objTreeIds = $this->fetchTreeIdsByGuids($objGuids);
        $parentTreeIds = $this->fetchTreeIdsByGuids($parentGuids);
        $tree = $this->getPlugin('Tree');
        foreach ($coll as $obj) {
            if (( ! empty($obj->parent_guid)) AND array_key_exists($obj->parent_guid, $parentTreeIds)) {
                $parentId = $parentTreeIds[$obj->parent_guid];
            }
            else {
                $parentId = $tree->getNestedSets()->getRootId();
            }
            
            if (array_key_exists($obj->guid, $insertedIds)) {
                $obj->id = $insertedIds[$obj->guid];
                
                $descFields = array(
                    $descPref.'language_id'=>$lang->id,
                    $descPref.'name'=>$obj->name,
                    $descPref.'brief'=>$obj->brief,
                    $descPref.'full'=>$obj->full,
                    $descPref.'category_id'=>$obj->id,
                );
                $this->poolInsert($descTableName, $descFields);
                if ( ! $obj->tree_id = $tree->getNestedSets()->Insert($parentId)) {
                    $obj->tree_id = NULL;
                }
                $this->poolUpdate($catTableName, array($catPref.'category_tree_id'=>$obj->tree_id), array($catPref.'id = ?'=>$obj->id));
                $parentTreeIds[$obj->guid] = $obj->tree_id;
            }
            else {
                $tree->getNestedSets()->MoveAll($objTreeIds[$obj->guid], $parentId);
            }
                
        }
        $this->poolInsert($descTableName);
        $this->poolUpdate($catTableName);
        
        return $this;
    }
    
}