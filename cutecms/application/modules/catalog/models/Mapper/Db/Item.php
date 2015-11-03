<?php

class Catalog_Model_Mapper_Db_Item extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Catalog_Model_Db_Table_Item',
        'Model_Object_Interface' => 'Catalog_Model_Object_Item',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Item',

        'Model_Db_Table_Description' => 'Catalog_Model_Db_Table_ItemDescription',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Sorting',
        'Model_Mapper_Db_Plugin_Resource',
        'Model_Mapper_Db_Plugin_Filter' => 'Catalog_Model_Mapper_Db_Plugin_Filter_Item',
        'Model_Mapper_Db_Category' => 'Catalog_Model_Mapper_Db_Category',
        'Model_Db_Table_Resources',
        'Model_Db_Table_CategoryRef'=>'Catalog_Model_Db_Table_CategoryItemRef',
        'Model_Db_Table_Category'=>'Catalog_Model_Db_Table_Category',
        'Model_Mapper_XML_Attribute' => 'Catalog_Model_Mapper_XML_Attribute',
        'Model_Mapper_XML_Brule' => 'Catalog_Model_Mapper_XML_Brule',
        'Model_Mapper_XML_Image' => 'Catalog_Model_Mapper_XML_Image',
        'Model_Mapper_Db_Plugin_Multisite' => 'Model_Mapper_Db_Plugin_Multisite_ManyToMany',
        'Model_Mapper_Db_Site',
        'Model_Db_Table_SiteRef' => 'Catalog_Model_Db_Table_ItemSiteRef',
    );



    public function init()
    {
        set_time_limit(0);
        ini_set('memory_limit', '500M');
        $table = $this->getTable();
        $tableName = $table->getTableName();
        $prefix = $table->getColumnPrefix().$table->getPrefixSeparator();
        $descTable = $this->getInjector()->getObject('Model_Db_Table_Description');
        $descTableName = $descTable->getTableName();
        $descPrefix = $descTable->getColumnPrefix().$descTable->getPrefixSeparator();
        $this->addPlugin(
            'Description',
            $this ->getInjector()
                  ->getObject(
                    'Model_Mapper_Db_Plugin_Description',
                    array(
                        'mapper' => $this,
                        'table' => $descTable,
                        'refColumn' => $prefix.'id',
                        'descFields' => array(
                            'name', 'material', 'brief', 'full', 'full2', 'full3', 'full4', 'more',
                            'html_title', 'meta_keywords', 'meta_description',
                            'manufacturer',
                            'unit',
                        ),
                    )
                  )
        )
        ->addPlugin('Sorting', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Sorting', array(
            'sort' => $tableName.'.'.$prefix.'sort',
            'name' => $descTable->getColumnPrefix().'.'.$descPrefix.'name', /* getColumnPrefix() is used because description plugin makes such alias for table name */
            'price' => array($tableName.'.'.$prefix.'price' => 'DESC'),
            'price2' => array($tableName.'.'.$prefix.'price2' => 'DESC'),
            'price3' => array($tableName.'.'.$prefix.'price3' => 'DESC'),
            'qty' => array($tableName.'.'.$prefix.'stock_qty' => 'DESC'),
            'rc_id' => array($tableName.'.'.$prefix.'rc_id' => 'DESC'),
            'sku' => array($tableName.'.'.$prefix.'sku' => 'ASC'),
        )))
        ->addPlugin('Resource',$this->getInjector()->getObject('Model_Mapper_Db_Plugin_Resource', array('rc_id'), Zend_Registry::get('config')->images->previewMaxCount))
        ->addPlugin('Multisite', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Multisite', array(
            'siteMapper' => $this->getInjector()->getObject('Model_Mapper_Db_Site'),
            'refTable' => $this->getInjector()->getObject('Model_Db_Table_SiteRef'),
            'refEntityColumn' => $prefix.'id',
        )))
        ->addPlugin('Filters', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Filter',
            array(
                'filter_name',
                'filter_sku',
                'filter_attribute',
                'filter_manufacturer',
                'filter_price',
                'filter_seo_id',
                'filter_is_new',
                'filter_is_popular',
                'filter_main_page_slider',
                'filter_main_left_baner',
                'filter_active',
                'filter_material',
                'filter_probe',
                'filter_size',
                /*'filter_automate',
                'filter_extender',*/
            ),
            array(
                'filter_price' => array('filter_price_min', 'filter_price_max'),                
            )
        ))        
        ;
        if ($config = Zend_Registry::get('config')->images->previewDimensions->{$this->getInjector()->getInjection('Model_Object_Interface')}) {
            $this->getPlugin('Resource')->setPreviewDimensions($config->toArray());
        }                        
    }

    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        $lid = $this->getPlugin('Description')->getCurrentLanguage()->id;

        $select->distinct()
            ->joinLeft(
                array('manufacturer_description'),
                'manufacturer_desc_manufacturer_id = item_manufacturer_id  AND '. 'manufacturer_desc_language_id = ' . $lid,
                array(
                    'item_manufacturer_name'  => 'manufacturer_desc_name',
                    'item_manufacturer_brief' => 'manufacturer_desc_brief'
                )
            )
        ;

        $select->joinLeft(array('item_search'), 'is_id = item_id');

        return $select;
    }

    /**
     * @param Model_Object_Interface
     * @param array
     * @return Model_Object_Interface
     */
    protected function _postSaveComplex(Model_Object_Interface $obj, array $values)
    {
        $refTable = $this->getInjector()->getObject('Model_Db_Table_CategoryRef');
        $refTable->delete(array('ci_ref_item_id = ?' => $obj->id));
        $cats = $values['item_categories'];
        if (is_array($cats)) {
            $i = 0;
            foreach ($cats as $cat) {
                $refTable->insert(array(
                    'ci_ref_item_id' => $obj->id,
                    'ci_ref_category_id' => $cat,
                    'ci_ref_sort' => $i++,
                ));
            }
        }
        return $obj;
    }


    /**
     * @param $seoId
     *
     * @return mixed
     * @throws Model_Mapper_Db_Exception
     */
    public function fetchOneBySeoId($seoId)
    {
        if (empty($seoId)) {
            throw new Model_Mapper_Db_Exception('id should be set');
        }
        if (!$rows = $this->fetchComplex(array('item_seo_id = ?' => $seoId))) {
            throw new Model_Mapper_Db_Exception(
                'table row with seo_id="' . $seoId . '" not found!'
            );
        } else {
            $object = $rows->current();
        }

        return $object;
    }
    
    
    public function fetchAllWithCategory($fetch = TRUE)
    {
        $lang = $this->getPlugin('Description')->getCurrentLanguage();
        $select = $this->fetchComplex(NULL, FALSE)
                       ->distinct(TRUE)
                       ->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item_id', array())
                       ->joinLeft(array('category'), 
                                  'category_id = ci_ref_category_id', 
                                  array('item_category_id' => 'category_id',
                                        'item_category_seo_id' => 'category_seo_id'))
                       ->joinLeft(array('category_description'), 
                                  'category_desc_category_id = category_id AND category_desc_language_id = '.$lang->id, 
                                  array('item_category_name' => 'category_desc_name'))
                       ->joinLeft(array('category_tree'), 'category_category_tree_id = category_tree_id', array())
                       ->reset('order')
                       ->order(array('category_tree_left ASC', 'category_desc_name ASC', 'item_desc_name ASC'))
                       ;
App_Debug::dump($select->assemble());
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;            
        }
        return $result;
    }
    
    /**
     * @param int
     * @param bool
     * @param bool
     * @return Model_Collection_Interface | Zend_Db_Select
     */
    public function fetchComplexByCategory($cat, $includeAllChildren = FALSE, $fetch = TRUE)
    {
        $query = $this->fetchComplex(NULL, FALSE);
        $query->distinct(TRUE);
        if ($cat AND ($includeAllChildren !== TRUE)) {
            $query->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item_id', array())
                  ->where('ci_ref_category_id = ?', (int) $cat);
        }
        else if ($cat AND ($includeAllChildren === TRUE)) {
            $query
                  ->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item_id', array())
                  ->joinLeft(array('root'=>'category'), 'root.category_id = '.$cat, array())
                  ->joinLeft(array('root_tree'=>'category_tree'), 'root.category_category_tree_id = root_tree.category_tree_id', array())
                  ->joinLeft(array('kids_tree'=>'category_tree'), 'kids_tree.category_tree_left>root_tree.category_tree_left AND kids_tree.category_tree_right<root_tree.category_tree_right', array())
                  ->joinLeft(array('kids'=>'category'), 'kids.category_category_tree_id=kids_tree.category_tree_id AND ci_ref_category_id=kids.category_id')
                  ->where('ci_ref_category_id = '.(int) $cat.' OR ci_ref_category_id=kids.category_id')
                  ->group('item_id')
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
     * @param bool
     * @param bool
     * @return int
     */
    public function countAllByCategory($cat, $includeAllChildren = FALSE)
    {
        $query = $this->fetchComplex(NULL, FALSE);
        $query->distinct(TRUE);
        if ($cat AND ($includeAllChildren !== TRUE)) {
            $query->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item_id', array())
                  ->where('ci_ref_category_id = ?', (int) $cat);
        }
        else if ($cat AND ($includeAllChildren === TRUE)) {
            $query
                  ->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item_id', array())
                  ->joinLeft(array('root'=>'category'), 'root.category_id = '.$cat, array())
                  ->joinLeft(array('root_tree'=>'category_tree'), 'root.category_category_tree_id = root_tree.category_tree_id', array())
                  ->joinLeft(array('kids_tree'=>'category_tree'), 'kids_tree.category_tree_left>root_tree.category_tree_left AND kids_tree.category_tree_right<root_tree.category_tree_right', array())
                  ->joinLeft(array('kids'=>'category'), 'kids.category_category_tree_id=kids_tree.category_tree_id AND ci_ref_category_id=kids.category_id')
                  ->where('ci_ref_category_id = '.(int) $cat.' OR ci_ref_category_id=kids.category_id')
                  ->group('item_id')
                  ;
        }
        $query->reset('columns')->columns(array('cnt' => 'COUNT(item.item_id)'));        
        $row = $query->query()->fetch();
        $result = $row['cnt'];
        return $result;
    }
    
    

    /**
     * Получить пагинатор товаров по категории
     *
     * @param int  $cat
     * @param int  $rows
     * @param int  $page
     * @param bool $includeAllChildren
     * @param bool $sortPrice
     * @return Zend_Paginator
     */
    public function paginatorFetchComplexByCategory($cat, $rows, $page, $includeAllChildren = false, $sortPrice = false)
    {
        $query = $this->fetchComplexByCategory($cat, $includeAllChildren, false);
        $query->distinct(false)->group('item_id');

        if ($sortPrice == '0') {
            $query->reset(Zend_Db_Select::ORDER);
            $query->order('item.item_price ASC');
        } elseif ($sortPrice == '1') {
            $query->reset(Zend_Db_Select::ORDER);
            $query->order('item.item_price DESC');
        }

        return $this->paginator($query, $rows, $page);
    }
    
    public function fetchLastDateAddedInCategory($cat, $includeAllChildren = FALSE)
    {
        $query = $this->fetchComplexByCategory($cat, $includeAllChildren, FALSE)
                      ->reset('columns')
                      ->columns(array('max_date_added'=>'MAX(item_date_changed)'))
                      ;
        $row = $query->query()->fetch();
        
        return $row['max_date_added'];
    }


    protected function _preSaveComplex(Model_Object_Interface $obj, array $values)
    {
        $obj->attributes_xml = $this->getInjector()->getObject('Model_Mapper_XML_Attribute')->unmapCollectionToXML($obj->attributes);
        $obj->brules_xml = $this->getInjector()->getObject('Model_Mapper_XML_Brule')->unmapCollectionToXML($obj->brules);
        $obj->images_xml = $this->getInjector()->getObject('Model_Mapper_XML_Image')->unmapCollectionToXML($obj->images);

        if (array_key_exists('is_configurable', $values)) {
            App_Debug::log('setting configurable in mapper ('.(bool) $values['is_configurable'].')');
            $obj->is_configurable = (bool) $values['is_configurable'];
        }
        if (array_key_exists('is_downloadable', $values)) {
            $obj->is_downloadable = (bool) $values['is_downloadable'];
        }
        
        $key = 'description_language_'.Model_Service::factory('language')->getCurrent()->id.'_name';
        if (empty($values['seo_id']) AND isset($values[$key])) {
				$seoId = App_Utf8::urlClean($values[$key]); // 13 is length of 'Model_Object_' string
				while ( ($obj->id == NULL and Model_Service::factory('catalog/item')->checkSeoId($seoId) > 0)
                     or (isset($obj->id) and Model_Service::factory('catalog/item')->checkSeoId($seoId) > 1)) {
					$seoId .= '-';
				}
				$obj->seo_id = $seoId;
        }

        return $obj;
    }

    protected function _onBuildComplexObject(Model_Object_Interface $obj, array $values = NULL, $addedPrefix = TRUE)
    {
        $obj->attributes = $obj->attributes_xml;
        $obj->brules = $obj->brules_xml;
        $obj->images = $obj->images_xml;
        return $obj;
    }


    public function fetchPopular($limit = NULL, $fetch = TRUE)
    {
        $select = $this->fetchComplex(NULL, FALSE);
        $select->where('item_is_popular > 0');
        $select->reset('order')->order(array(/*'item_is_popular DESC', */'item_views DESC'));
        if ($limit !== NULL) {
            $select->limit($limit);
        }
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }
    
    public function fetchHomeOurCollectionsItems($limit = NULL, $fetch = TRUE)
    {
        $select = $this -> fetchComplex(NULL, FALSE)
						-> where('item_home_page_our_collections = 1 and item_status = 1')
						-> joinLeft(array('category_item_ref'), 'ci_ref_item_id = item.item_id', array())
						-> joinLeft(array('category'), 'ci_ref_category_id = category.category_id',
									array('item_category_id'=>'category_item_ref.ci_ref_category_id',
											'item_category_seo_id'=>'category.category_seo_id'))
                        ->group('item_id');

        if ($limit !== NULL) {
            $select->limit($limit);
        }
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }
    
    public function paginatorFetchPopular($rowsPerPage, $page)
    {
        $select = $this->fetchPopular(NULL, FALSE);
        return $this->paginator($select, $rowsPerPage, $page);        
    }    

    public function fetchNew($limit = NULL, $fetch = TRUE)
    {
        $select = $this->fetchComplex(NULL, FALSE);
        $select->where('item_is_new > 0');
        $select->reset('order')->order(array('item_is_new DESC', 'item_date_added DESC'));
        if ($limit !== NULL) {
            $select->limit($limit);
        }
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }
    
    public function paginatorFetchNew($rowsPerPage, $page)
    {
        $select = $this->fetchNew(NULL, FALSE);
        return $this->paginator($select, $rowsPerPage, $page);        
    }

    public function fetchActions($limit = NULL, $fetch = TRUE)
    {
        $select = $this->fetchComplex(NULL, FALSE);
        $select->where('item_old_price > 0')
               ->where('item_price <> item_old_price');
        $select->reset('order')->order(array('item_is_new DESC', 'item_date_added DESC'));
        if ($limit !== NULL) {
            $select->limit($limit);
        }
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }
    
    public function paginatorFetchActions($rowsPerPage, $page)
    {
        $select = $this->fetchActions(NULL, FALSE);
        return $this->paginator($select, $rowsPerPage, $page);        
    }

    public function fetchAlternates($itemId, $limit = NULL, $fetch = TRUE)
    {
        $select = $this->fetchComplex(NULL, FALSE)
                       ->joinLeft(array('cir1'=>'category_item_ref'), 'cir1.ci_ref_item_id = item_id', array())
                       ->joinLeft(array('cir2'=>'category_item_ref'), 'cir2.ci_ref_category_id = cir1.ci_ref_category_id', array())
                       ->where('cir2.ci_ref_item_id<>cir1.ci_ref_item_id')
                       ->where('cir2.ci_ref_item_id = ?', $itemId)
                       ;
        if ($limit !== NULL) {
            $select->limit($limit);
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
     * get array of category_id fields searched by category_guid field values
     * @param array - (guid, guid, ...)
     * @return array - (guid=>id, guid=>id, ...)
     */
    public function fetchIdsByGuids(array $guids) 
    {
        $result = array();
        if ( ! empty($guids)) {
            $table = $this->getTable();
            $pref = $table->getColumnPrefix();
            $sep = $table->getPrefixSeparator();
            $select = $table->select()
                            ->from($table->getTableName(), 
                                   array('id'=>$pref.$sep.'id', 'guid'=>$pref.$sep.'guid'))
                            ->where($pref.$sep.'guid IN (?)', $guids)
                            ;
            if ($rows = $select->query()->fetchAll()) {            
                foreach ($rows as $row) {
                    $result[$row['guid']] = $row['id'];
                }
            }
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
        $itemTable     = $this->getTable();
        $itemTableName = $itemTable->getTableName();
        $itemPref      = $itemTable->getColumnPrefix().$itemTable->getPrefixSeparator();
        
        $descTable     = $this->getInjector()->getObject('Model_Db_Table_Description');
        $descTableName = $descTable->getTableName();
        $descPref      = $descTable->getColumnPrefix().$descTable->getPrefixSeparator();
        
        $refTable      = $this->getInjector()->getObject('Model_Db_Table_CategoryRef');
        $refTableName  = $refTable->getTableName();
        $refPref       = $refTable->getColumnPrefix().$refTable->getPrefixSeparator();
        
        $lang = Model_Service::factory('language')->getCurrent();
        
        $insertedGuids = array();
        $insertedCategoriesGuids = array();
        $updatedCategoriesGuids = array();
        
        foreach ($coll as $obj) {
            $itemFields = array(
                $itemPref.'status' => 1,
                $itemPref.'seo_id' => App_Utf8::urlClean($obj->name),
                $itemPref.'guid' => $obj->guid,
                $itemPref.'sku' => $obj->sku,
                $itemPref.'code' => $obj->code,
                $itemPref.'category_guid' => $obj->category_guid,
                $itemPref.'model' => $obj->model,
                $itemPref.'stock_qty' => $obj->stock_qty,
                $itemPref.'price' => $obj->price,
                $itemPref.'price2' => $obj->price2,
                $itemPref.'price3' => $obj->price3,
                $itemPref.'param1' => $obj->param1,
                $itemPref.'param2' => $obj->param2,
                $itemPref.'param3' => $obj->param3,                                
            );
            
            $descFields = array(
                $descPref.'language_id'=>$lang->id,
                $descPref.'name'=>$obj->name,
                $descPref.'brief'=>$obj->brief,
                $descPref.'full'=>$obj->full,
                $descPref.'manufacturer'=>$obj->manufacturer,
                $descPref.'unit'=>$obj->unit,
            );
            if (($obj->id) AND ($obj->delete)) {
                $this->delete($obj);
            }
            else if (( ! $obj->id) AND ( ! $obj->delete)) {
                $this->poolInsert($itemTableName, $itemFields); 
                $insertedGuids []= $obj->guid;
                $insertedCategoriesGuids []= $obj->category_guid;
            }
            else if (($obj->id) AND ( ! $obj->delete)) {
                $this->poolUpdate($itemTableName, $itemFields, array($itemPref.'id = ?'=>$obj->id));
                $descFields[$descPref.'item_id'] = $obj->id;
                $this->poolUpdate($descTableName, $descFields, array(
                    $descPref.'language_id = ?'=>$lang->id,
                    $descPref.'item_id = ?'=>$obj->id,
                ));
                $updatedCategoriesGuids[$obj->id] = $obj->category_guid;
            }
        }        
        $this->poolInsert($itemTableName);
        $this->poolUpdate($itemTableName);
        $this->poolUpdate($descTableName);
        
        /**
         * insert descriptions and category refs
         */
        $categoryMapper = $this->getInjector()->getObject('Model_Mapper_Db_Category');
        $insertedIds = $this->fetchIdsByGuids($insertedGuids);
        $insertedCategoriesIds = $categoryMapper->fetchIdsByGuids($insertedCategoriesGuids);
        $updatedCategoriesIds = $categoryMapper->fetchIdsByGuids($updatedCategoriesGuids);
        foreach ($coll as $obj) {
            if (array_key_exists($obj->guid, $insertedIds)) {
                $obj->id = $insertedIds[$obj->guid];
                
                $descFields = array(
                    $descPref.'language_id'=>$lang->id,
                    $descPref.'name'=>$obj->name,
                    $descPref.'brief'=>$obj->brief,
                    $descPref.'full'=>$obj->full,
                    $descPref.'manufacturer'=>$obj->manufacturer,
                    $descPref.'unit'=>$obj->unit,
                    $descPref.'item_id'=>$obj->id,
                );
                $this->poolInsert($descTableName, $descFields);                
            }
            $refTable->delete(array($refPref.'item_id = ?'=>$obj->id,));
            if ($obj->delete) {                
            }
            else if (array_key_exists($obj->category_guid, $insertedCategoriesIds)) {
                $this->poolInsert($refTableName, array(
                    $refPref.'category_id'=>$insertedCategoriesIds[$obj->category_guid],
                    $refPref.'item_id'=>$obj->id,
                ));
            }
            else if (array_key_exists($obj->category_guid, $updatedCategoriesIds)) {
                $this->poolInsert($refTableName, array(
                    $refPref.'category_id'=>$updatedCategoriesIds[$obj->category_guid],
                    $refPref.'item_id'=>$obj->id,
                ));
            }
            
            
        }
        $this->poolInsert($descTableName);
        $this->poolInsert($refTableName);
        $this->poolUpdate($refTableName);
        
        return $this;
    }
 
    /**
     * fetch collection (possible limited!) of items by collection of categories (or ids array)
     * 
     * algorythm of limiting in group was found here 
     * @link http://www.xaprb.com/blog/2006/12/07/how-to-select-the-firstleastmax-row-per-group-in-sql/
     * method's efficiency proof:
     * @link http://www.mysqlperformanceblog.com/2006/08/10/using-union-to-implement-loose-index-scan-to-mysql/
     * 
     * @param mixed $categories - Model_Collection_Interface | array
     * @param int $limitInCategory
     * @param bool $fetch
     * @return Model_Collection_Interface
     */
    public function fetchComplexByCategories($categories, $limitInCategory = NULL, $where = NULL, $includingChildCategories = FALSE)
    {
        if (empty($categories) OR ( ! count($categories))) {
            return $this->getInjector()->getObject('Model_Collection_Interface');
        }
        $select = $this->fetchComplex($where, FALSE)                       
                       ->reset('order');
        if ($includingChildCategories) {
            $select->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item.item_id', array())
                   ->joinLeft(array('self_cat'=>'category'), 'ci_ref_category_id = self_cat.category_id', array())
                   ->joinLeft(array('self_tree' => 'category_tree'), 'self_cat.category_category_tree_id = self_tree.category_tree_id', array())
                   ->joinLeft(array('par_tree' => 'category_tree'), 'par_tree.category_tree_left < self_tree.category_tree_left AND par_tree.category_tree_right > self_tree.category_tree_right AND par_tree.category_tree_level > 0', array())
                   ->joinLeft(array('par_cat'=>'category'), 'par_tree.category_tree_id = par_cat.category_category_tree_id', array())
                   ->where('(self_cat.category_id = 0 OR par_cat.category_id = 0)')
                   ;
        }
        else {
            $select->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item.item_id', array('item_category_id'=>'ci_ref_category_id'))
                   ->where('ci_ref_category_id = ?', 0)
                   ;            
        }
                       
        if ($limitInCategory) {
            $select->limit($limitInCategory);
        }
        $selectSql = $select->assemble();
        $querySqls = array();
        foreach ($categories as $category) {
            if ($category instanceof Model_Object_Interface) {
                $categoryId = $category->id;
            }
            else {
                $categoryId = $category;
            }
            if ($includingChildCategories) { 
                $newSql = str_replace('self_cat.category_id = 0 OR par_cat.category_id = 0', 'self_cat.category_id = '.$categoryId.' OR par_cat.category_id = '.$categoryId, $selectSql);
                $newSql = str_replace('FROM', ', CASE WHEN self_cat.category_id = '.$categoryId.' THEN self_cat.category_id ELSE par_cat.category_id END AS item_category_id FROM ', $newSql);
                
                $querySqls []= '(' . $newSql . ')';
            }
            else {
                $querySqls []= '(' . str_replace('ci_ref_category_id = 0', 'ci_ref_category_id = '.$categoryId, $selectSql) . ')';
            }
        }
        $querySql = implode(' UNION  ', $querySqls);
        /*$querySql = 'SELECT DISTINCT * FROM ('.$querySql.') AS unioned';*/
        App_Debug::dump($querySql);
        $result = $this->makeComplexCollection($this->getTable()->getAdapter()->query($querySql)->fetchAll());
        return $result;
    }
    
    public function fetchNewByCategories($categories, $limit = NULL, $includingChildCategories = FALSE)
    {
        return $this->fetchComplexByCategories($categories, $limit, array('item_is_new > 0'), $includingChildCategories);
    }
    
    public function fetchPopularByCategories($categories, $limit = NULL, $includingChildCategories = FALSE)
    {
        return $this->fetchComplexByCategories($categories, $limit, array('item_is_popular > 0'), $includingChildCategories);
    }
        
    
    public function fetchComplexByManufacturer($manufacturerId, $fetch = TRUE)
    {
        return $this->fetchComplex(array('item_manufacturer_id = ?'=>$manufacturerId), $fetch);
    }
    
    public function paginatorFetchByManufacturer($manufacturerId, $rowsPerPage, $page)
    {
        return $this->paginator($this->fetchComplexByManufacturer($manufacturerId, FALSE), $rowsPerPage, $page);
    }
    
    
    /**
     * get item with its 2 neighbours - previous and next
     * @param mixed string|int - seo_id or id of item
     * @return array - array('previous'=>, 'current'=> , 'next'=>)
     */
    public function fetchCurrentPreviousNext($id, $catId = NULL)
    {
        if (is_numeric($id)) {
            $whereField = 'id';
        }
        else {
            $whereField = 'seo_id';
        }
        $innerSelect = $this->fetchComplex(NULL, FALSE);
        if ( (int) $catId) {
            $innerSelect->joinLeft('category_item_ref', 'ci_ref_item_id = item_id', array())
                        ->joinLeft(array('root'=>'category'), 'root.category_id = '.$catId, array()) 
                       ->joinLeft(array('root_tree'=>'category_tree'), 'root.category_category_tree_id = root_tree.category_tree_id', array())
                       ->joinLeft(array('kids_tree'=>'category_tree'), 'kids_tree.category_tree_left>root_tree.category_tree_left AND kids_tree.category_tree_right<root_tree.category_tree_right', array())
                       ->joinLeft(array('kids'=>'category'), 'kids.category_category_tree_id=kids_tree.category_tree_id AND ci_ref_category_id=kids.category_id')
                       ->where('ci_ref_category_id = '.(int) $catId.' OR ci_ref_category_id=kids.category_id')
                       ->group('item_id');
        }
        $outerSelect = $this->getTable()->select()->setIntegrityCheck(FALSE)
                            ->from(array('inner'=>$innerSelect), '*')
                            ->columns(array('item_row_number'=>new Zend_Db_Expr('@num := @num +1')))
                            ->joinInner(array('nil'=>new Zend_Db_Expr('(select @num:=0)')), '1=1', array());        
        $currentSelect = $this->getTable()->select()->setIntegrityCheck(FALSE)
                                          ->from(array('outer'=>$outerSelect), '*')
                                          ->where('item_'.$whereField.' = ?', $id);
        $currentObj = $this->makeComplexObject($currentSelect->query()->fetch());
        $result = array('current'=>$currentObj);
        if ($currentObj->row_number > 1) {
            $prevSelect = $this->getTable()->select()->setIntegrityCheck(FALSE)
                                            ->from(array('outer'=>$outerSelect), '*')
                                            ->where('item_row_number = ?', $currentObj->row_number-1);
            $prevObj = $this->makeComplexObject($prevSelect->query()->fetch());
            $result['previous'] = $prevObj;
        }
        else {
            $result['previous'] = NULL;
        }
        $nextSelect = $this->getTable()->select()->setIntegrityCheck(FALSE)
                                       ->from(array('outer'=>$outerSelect), '*')
                                       ->where('item_row_number = ?', $currentObj->row_number+1);
        if ($nextRow = $nextSelect->query()->fetch()) {
            $nextObj = $this->makeComplexObject($nextRow);
            $result['next'] = $nextObj;
        }
        else {
            $result['next'] = NULL;
        }
        
        return $result;
    }

    public function paginatorFetchSearchItems($rowsPerPage, $page, $word, $categoryId)
    {
        $select = $this ->fetchComplex(NULL, FALSE)
            -> joinLeft(array('category_item_ref'=>'category_item_ref'),
                              'category_item_ref.ci_ref_item_id = item_id',
                        array('item_category_id'=>'category_item_ref.ci_ref_category_id'))
            -> where('item_desc_name LIKE ?', '%'.$word.'%');
        if ($categoryId){
            $select -> where('category_item_ref.ci_ref_category_id = ?', $categoryId);       
        }
        return $this->paginator($select, $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
    }
    
    public function fetchHomeSliderItems()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
		$select = $this -> fetchComplex(array('item_home_page_item_slider = ?' => 1, 'item_status' => 1), FALSE)
						-> joinLeft(array('category_item_ref'), 'ci_ref_item_id = item.item_id', array())
						-> joinLeft(array('category'), 'ci_ref_category_id = category.category_id',
									array('item_category_id'=>'category_item_ref.ci_ref_category_id',
											'item_category_seo_id'=>'category.category_seo_id'))
                        ->group('item_id');

		$result = $this->makeComplexCollection($select->query()->fetchAll());

        return $result;
    }

    public function fetchRandomItem($kol)
    {


        /*if(isset($kol) or $kol== 0){
            return $this->getInjector()->getObject('Model_Collection_Interface');
        }*/

        $select = $this->fetchComplex(NULL, FALSE)                       
                       ->reset('order');
        $select       ->where('item_status = 1')
                        //->order('item_seo_id')
                        ->order('RAND()')
                        ->limit($kol);

        $result = $this->makeComplexCollection($select->query()->fetchAll());
        //print_r($result);exit();

        return $result;
    }
    
    public function fetchAllActiveByCategory($cat, $page, $rowsPerPage, $includeAllChildren = false, $fetch = true)
    {
        $query = $this->fetchComplex(null, false);
        $query->joinLeft(array('remain'), 'remain_sku = item_sku', array('SUM(remain_in_stock) AS in_stock'))
            ->reset('order')->order('in_stock DESC');
        $this->getPlugin('Filters')->triggerEvent(Model_Mapper_Interface::EVENT_PAGINATION, array($query));

        $query->distinct(true);
        if ($cat AND ( $includeAllChildren !== true)) {
            $query->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item_id', array())
                ->where('ci_ref_category_id = ?', (int) $cat)
                ->where('item_status = ?', 1)
                ->limitPage($page, $rowsPerPage);
        } else if ($cat AND ( $includeAllChildren === true)) {
            $query
                ->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item_id', array())
                ->joinLeft(array('root' => 'category'), 'root.category_id = ' . $cat, array())
                ->joinLeft(array('root_tree' => 'category_tree'), 'root.category_category_tree_id = root_tree.category_tree_id', array())
                ->joinLeft(array('kids_tree' => 'category_tree'), 'kids_tree.category_tree_left>root_tree.category_tree_left AND kids_tree.category_tree_right<root_tree.category_tree_right AND kids_tree.category_tree_parent = root.category_category_tree_id', array())
                ->joinLeft(array('kids' => 'category'), 'kids.category_category_tree_id=kids_tree.category_tree_id AND ci_ref_category_id=kids.category_id')
                ->where('ci_ref_category_id = ' . (int) $cat . ' OR ci_ref_category_id=kids.category_id')
                ->where('item_status = ?', 1)
                ->group('item_id')
                ->limitPage($page, $rowsPerPage)
            ;
        }

        return $fetch ? $this->makeComplexCollection($query->query()->fetchAll()) : $query;
    }

    public function fetchByText($text = NULL , $page, $rowsPerPage)
    {
        $select = $this->fetchComplex(array(), FALSE)
                        ->distinct(TRUE)
                        ->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item_id', array())
                        ->joinLeft(array('category'), 
                                  'category_id = ci_ref_category_id', 
                                  array('item_category_id' => 'category_id',
                                        'item_category_seo_id' => 'category_seo_id'))
                        ->joinLeft(array('remain'), 'remain_sku = item_sku', array('SUM(remain_in_stock) AS in_stock'))
                        ->where('item_status = ?', 1)
                        ->where('item_desc_name LIKE ? OR item_desc_full LIKE ? OR item_price LIKE ? OR item_sku LIKE ?', '%'.$text.'%')
                        ->order('in_stock ASC')
                        ->limitPage($page, $rowsPerPage);

        $result = $this->makeComplexCollection($select->query()->fetchAll());       
        return $result;
    }
    
    public function fetchTotalCountByText($text = NULL)
    {
        $select = $this->getTable()->select()
                        ->from('item', array('count' => 'COUNT(DISTINCT item_id)'))
                        ->setIntegrityCheck(FALSE)
                        ->joinLeft(array('item_description'), 'item_desc_item_id = item_id')
                        ->joinLeft(array('remain'), 'remain_sku = item_sku')
                        ->where('item_status = ?', 1)
                        ->where('item_desc_name LIKE ? OR item_desc_full LIKE ? OR item_price LIKE ? OR item_sku LIKE ?', '%'.$text.'%');

        return $this->getTable()->fetchRow($select)->count;
    }

    /**
     * Получить количество товаров по категории
     *
     * @param $cat
     * @return int
     */
    public function fetchTotalCountByCategory($cat)
    {
        $select = $this->getTable()->select()
            ->from('item', array('count' => 'COUNT(DISTINCT item_id)'))
            ->setIntegrityCheck(false)
            ->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item_id', array())
            ->joinLeft(array('remain'), 'remain_sku = item_sku')
            ->joinLeft(array('root' => 'category'), 'root.category_id = ' . $cat, array())
            ->joinLeft(array('root_tree' => 'category_tree'), 'root.category_category_tree_id = root_tree.category_tree_id', array())
            ->joinLeft(array('kids_tree' => 'category_tree'), 'kids_tree.category_tree_left>root_tree.category_tree_left AND kids_tree.category_tree_right<root_tree.category_tree_right AND kids_tree.category_tree_parent = root.category_category_tree_id', array())
            ->joinLeft(array('kids' => 'category'), 'kids.category_category_tree_id=kids_tree.category_tree_id AND ci_ref_category_id=kids.category_id')
            ->where('ci_ref_category_id = ' . (int) $cat . ' OR ci_ref_category_id=kids.category_id')
            ->where('item_status = ?', 1);

        $this->_onFetchComplex($select);
        $this->getPlugin('Filters')->triggerEvent(Model_Mapper_Interface::EVENT_PAGINATION, array($select));

        return $this->getTable()->fetchRow($select)->count;
    }

    /**
     * Получить артикулы по категории
     *
     * @param int|array $cat
     * @return array
     */
    public function fetchSkuByCategory($cat)
    {
        $select = $this->getTable()->select()
            ->from('item', array('item_sku'))
            ->setIntegrityCheck(false)
            ->joinLeft(array('category_item_ref'), 'ci_ref_item_id = item_id', array())
            ->where('item_status = ?', 1);

            if (is_array($cat)){
                $select->where('ci_ref_category_id IN (?)', $cat);
            }else {
                $select->where('ci_ref_category_id = ?', (int)$cat);
            }

        $this->_onFetchComplex($select);

        return $select->query()->fetchAll();
    }

    /**
     * Fetch item id by sku
     *
     * @param $sku
     *
     * @return mixed
     */
    public function fetchItemIdBySku($sku)
    {
        return $this->getTable()->select()
            ->from($this->getTable(), array('id' => 'item_id'))
            ->where('item_sku = ?', (string)$sku)
            ->query()
            ->fetch();
    }

}