<?php

class Catalog_Model_Service_Item extends Model_Service_Abstract
{

    protected $_defaultInjections
        = array(
            'Model_Object_Interface'        => 'Catalog_Model_Object_Item',
            'Model_Collection_Interface'    => 'Catalog_Model_Collection_Item',
            'Model_Mapper_Interface'        => 'Catalog_Model_Mapper_Db_Item',
            'Model_Service_Language',
            'Model_Mapper_XML_Attribute'    => 'Catalog_Model_Mapper_XML_Attribute',
            'Model_Mapper_XML_Image'        => 'Catalog_Model_Mapper_XML_Image',
            'Model_Mapper_XML_Brule'        => 'Catalog_Model_Mapper_XML_Brule',
            'Model_Service_Helper_Brule'    => 'Catalog_Model_Service_Helper_Brule_Item',
            'Model_Mapper_Importer'         => 'Catalog_Model_Mapper_Config_ImporterItem',
            'Model_Service_Helper_Importer' => 'Catalog_Model_Service_Helper_Importer_Item',
            'Catalog_Model_Service_Helper_Brule_Item_UserPersonalDiscount',
            'Catalog_Model_Service_Helper_Brule_Item_PercentDiscount',
            'Catalog_Model_Service_Helper_Brule_Item_Configurable',
            'Model_Service_Bundle'          => 'Catalog_Model_Service_ItemBundle',
            'Model_Service_Helper_Gallery'  => 'Model_Service_Helper_Content_Gallery',
            'Model_Service_Gallery',
            'Model_Db_Table_GalleryRef',
            'Model_Service_Helper_Comment'  => 'Model_Service_Helper_Content_Comment',
            'Model_Service_Comment',
            'Model_Db_Table_CommentRef',
            'Model_Service_Helper_Multisite',
            'Model_Service_Helper_Sorting',
        );

    /**
     * Proxy for IDE
     *
     * @param null $name
     * @return Catalog_Model_Mapper_Db_Item
     */
    public function getMapper($name = null)
    {
        return parent::getMapper($name);
    }

    /**
     * initializes object
     *
     * @see Model_Service_Abstract::init()
     */
    public function init()
    {
        $lang = $this->getInjector()->getObject('Model_Service_Language');
        $mapper = $this->getMapper();
        $mapper->getPlugin('Description')->setLanguages($lang->getAllActive())
            ->setCurrentLanguage($lang->getCurrent());

        /*$mapper->getPlugin('Filters')->setExtenderCode($this->getInjector()->getObject('Model_Service_Bundle')->getCodeByAlias('extender'));*/

        $this->addHelper(
            'Brule',
            $this->getInjector()->getObject('Model_Service_Helper_Brule', $this)
        );

        $this->addHelper(
            'Importer', $this->getInjector()->getObject(
            'Model_Service_Helper_Importer', $this
        )
        );

        $this->addHelper(
            'Gallery',
            $this->getInjector()->getObject(
                'Model_Service_Helper_Gallery', $this, array(
                'contentType'       => 'catalog/item',
                'refTableClass'     => $this->getInjector()->getInjection(
                    'Model_Db_Table_GalleryRef'
                ),
                'linkedService'     => $this->getInjector()->getObject(
                    'Model_Service_Gallery'
                ),
                'contentTitleField' => 'name',
            )
            )
        );

        $this->addHelper(
            'Comment',
            $this->getInjector()->getObject(
                'Model_Service_Helper_Comment', $this, array(
                'contentType'       => 'catalog/item',
                'refTableClass'     => $this->getInjector()->getInjection(
                    'Model_Db_Table_CommentRef'
                ),
                'linkedService'     => $this->getInjector()->getObject(
                    'Model_Service_Comment'
                ),
                'contentTitleField' => 'name',
            )
            )
        );
        $this->addHelper(
            'Multisite', $this->getInjector()->getObject(
            'Model_Service_Helper_Multisite', $this
        )
        );
        $this->addHelper(
            'Sorting', $this->getInjector()->getObject(
            'Model_Service_Helper_Sorting', $this
        )
        );
    }

    /**
     * creates new object
     *
     * @return Model_Object_Interface
     */
    public function createDefault()
    {
        $obj = $this->create();
        $obj->status = 1;
        $obj->stock_qty = 1;
        return $obj;
    }

    public function createFromArray(array $values, $mergeWithExistent = true)
    {
        if ($mergeWithExistent === true) {
            $item = $this->getComplex($values['id']);
            if (array_key_exists('attributes', $values)) {
                if (is_array($values['attributes'])) {
                    foreach ($values['attributes'] as $attrCode => $attrValue) {
                        $item->attributes->findOneByCode(
                            $attrCode
                        )->current_value = $attrValue;
                    }
                }
            } else {
                foreach ($values as $attrCode => $attrValue) {
                    if ($attr = $item->attributes->findOneByCode($attrCode)) {
                        $attr->current_value = $attrValue;
                    }
                }
            }

            $item->qty             = $values['qty'];
            /* Catalog_Model_Object_Item add fom Remain */
            $item->code            = ((bool)$values['in_stock']) ? $values['code'] : null;
            $item->characteristics = $values['characteristics'];
            $item->remain_price    = $values['price'];
            $item->size            = $values['size'];
            $item->weight          = $values['weight'];
            $item->probe           = $values['probe'];
            $item->material        = $values['material'];
            /* Catalog_Model_Object_Item */
        } else {
            $item = $this->getMapper()->makeComplexObject($values, false);
        }
        return $item;
    }

    public function createCollectionFromArray(
        array $items, $mergeWithExistent = true
    ) {
        $coll = $this->getInjector()->getObject('Model_Collection_Interface');
        if ($mergeWithExistent === true) {
            $ids = array();
            $itemsById = array();
            foreach ($items as $values) {
                $ids [] = $values['id'];
                $itemsById[$values['id']] = $values;
            }
            $existItems = $this->getAllByIdArray(
                $ids, Model_Object_Interface::STYLE_COMPLEX
            );
            foreach ($existItems as $item) {
                $values = $itemsById[$item->id];
                if (array_key_exists('attributes', $values)) {
                    if (is_array($values['attributes'])) {
                        foreach (
                            $values['attributes'] as $attrCode => $attrValue
                        ) {
                            $item->attributes->findOneByCode(
                                $attrCode
                            )->current_value = $attrValue;
                        }
                    }
                } else {
                    foreach ($values as $attrCode => $attrValue) {
                        if ($attr = $item->attributes->findOneByCode(
                            $attrCode
                        )
                        ) {
                            $attr->current_value = $attrValue;
                        }
                    }
                }
                $item->qty = $values['qty'];
                $coll->add($item);
            }
        } else {
            foreach ($items as $values) {
                $item = $this->getMapper()->makeComplexObject($values, false);
                $coll->add($item);
            }
        }
        return $coll;
    }

    /**
     * get objects fields values for edit form
     *
     * @return array
     */
    public function getEditFormValues($id)
    {
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        $descs = $this->getMapper()->getPlugin('Description')
            ->fetchDescriptions($id);
        $values['item_categories'] = $this->getBindedCategoriesIds($obj);
        $rcs = $this->getMapper()->getPlugin('Resource')->fetchResources($obj);
        $values = $values + $descs + $rcs;
        return $values;
    }

    /**
     * returns all the categories where item is placed in
     *
     * @param Model_Object_Interface $obj - item
     */
    public function getBindedCategoriesIds(Model_Object_Interface $obj)
    {
        $cats = Model_Service::factory('catalog/category')->getAllByItem($obj);
        $ids = array();
        foreach ($cats as $cat) {
            $ids[$cat->id] = $cat->id;
        }
        return $ids;
    }

    /**
     * @param mixed (string|int) seo_id or id of the page
     *
     * @return Model_Object_Page
     */
    public function get($id)
    {
        $cache = Zend_Registry::get('Zend_Cache');
        $cacheKey = $this->getInjector()->getInjection('Model_Object_Interface') . '__' . md5($id);
        if (!$obj = $cache->load($cacheKey)) {
            if (is_numeric($id)) {
                $obj = $this->getMapper()->fetchOneById($id);
            } else {
                if (is_string($id)) {
                    $obj = $this->getMapper()->fetchOneBySeoId($id);
                } else {
                    throw new Model_Service_Exception(
                        'unknown parameter for get method - ' . $id
                    );
                }
            }
            $cache->save($obj, $cacheKey, array('item', 'item__' . $obj->id));
        }
        return $obj;
    }

    /**
     * all items of category
     *
     * @param int current category id
     * @param bool
     *
     * @return Model_Collection_Interface
     */
    public function getAllByCategory(
        $cat, $includeAllChildrenCategories = false
    ) {
        return $this->getMapper()->fetchComplexByCategory(
            $cat, $includeAllChildrenCategories
        );
    }

    public function countAllByCategory(
        $cat, $includeAllChildrenCategories = false
    ) {
        return $this->getMapper()->countAllByCategory(
            $cat, $includeAllChildrenCategories
        );
    }

    /**
     * paginator for all items of category
     *
     * @param int current category id
     * @param int
     * @param int
     * @param bool
     *
     * @return Zend_Paginator
     */
    public function paginatorGetAllByCategory(
        $cat, $rowsPerPage = null, $page = null, $includeAllChildren = false,
        $sortPrice = false
    ) {
        if ($rowsPerPage === null) {
            $rowsPerPage = Zend_Registry::get(
                'config'
            )->default->paginator->rowsPerPage;
        }
        if ($page === null) {
            $page = Zend_Controller_Front::getInstance()->getRequest()
                ->getParam('page');
        }
        if ($cat === null) {
            $cat = Zend_Controller_Front::getInstance()->getRequest()->getParam(
                'category'
            );
        }
        $paginator = $this->getMapper()->paginatorFetchComplexByCategory(
            $cat, $rowsPerPage, $page, $includeAllChildren, $sortPrice
        );
        return $paginator;
    }

    public function getLastDateAddedInCategory(
        $cat, $includeAllChildren = false
    ) {
        return $this->getMapper()->fetchLastDateAddedInCategory(
            $cat, $includeAllChildren
        );
    }

    /**
     * parse attributes from xml to collection
     *
     * @param string xml
     *
     * @return Model_Collection_Interface
     */
    public function parseAttributesFromXML($xml)
    {
        /** @var $mapper Catalog_Model_Mapper_XML_Attribute */
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Attribute');
        if (!empty($xml)) {
            $attribs = new SimpleXMLElement($xml);
        } else {
            $attribs = null;
        }
        return $mapper->makeSimpleCollection($attribs);
    }

    public function parseAttributesToXML(
        Catalog_Model_Collection_Attribute $coll
    ) {
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Attribute');
        return $mapper->unmapCollectionToXML($coll);
    }

    /**
     * parse images from xml to collection
     *
     * @param string xml
     *
     * @return Model_Collection_Interface
     */
    public function parseImagesFromXML($xml)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Image');
        if (!empty($xml)) {
            $attribs = new SimpleXMLElement($xml);
        } else {
            $attribs = null;
        }
        return $mapper->makeSimpleCollection($attribs);
    }

    public function parseImagesToXML(Model_Collection_Interface $coll)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Image');
        return $mapper->unmapCollectionToXML($coll);
    }

    /**
     * parse business rules from xml to collection
     *
     * @param string xml
     *
     * @return Model_Collection_Interface
     */
    public function parseBrulesFromXML($xml)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Brule');
        if (!empty($xml)) {
            $attribs = new SimpleXMLElement($xml);
        } else {
            $attribs = null;
        }
        return $mapper->makeSimpleCollection($attribs);
    }

    public function parseBrulesToXML(Catalog_Model_Collection_Brule $coll)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Brule');
        return $mapper->unmapCollectionToXML($coll);
    }

    public function calculatePrice(
        Model_Object_Interface $obj, $inDefaultCurrency = false
    ) {
        $price = $this->getHelper('Brule')->calculatePrice($obj);
        if (!$inDefaultCurrency) {
            $price *= (float)Model_Service::factory('currency')->getCurrent(
            )->rate;
        }
        return $price;
    }

    /**
     * @param string
     *
     * @return Model_Object_Interface
     */
    public function getComplexBySeoId($seoId)
    {
        return $this->getMapper()->fetchOneBySeoId($seoId);
    }

    public function setAttributeValues(Model_Object_Interface $obj, array $values)
    {
        foreach ($values as $key=>$val) {
            if (isset($obj->attributes->$key)) {
                $obj->attributes->$key->current_value = $val;
            }
        }
    }

    public function addToShoppingCart(Model_Object_Interface $item)
    {
        App_Event::factory('Catalog_Model_Service_Item__onAddToCart', array($item))->dispatch();

        return $this;
    }

    public function getPopular($limit = null)
    {
        $cacheKey = __CLASS__ . '__' . __FUNCTION__ . '__' . $limit;
        $cache = Zend_Registry::get('Zend_Cache');
        if (!$data = $cache->load($cacheKey)) {
            if ($limit === null) {
                $limit = Zend_Registry::get(
                    'catalog_config'
                )->box->popularItems->limit;
            }
            $data = $this->getMapper()->fetchPopular($limit);
            $cache->save($data, $cacheKey, array('item'));
        }
        return $data;
    }

    public function getHomeOurCollectionsItems($limit = null)
    {
        $data = $this->getMapper()->fetchHomeOurCollectionsItems($limit);
        return $data;
    }

    public function getNew($limit = null)
    {
        $cacheKey = __CLASS__ . '__' . __FUNCTION__ . '__' . $limit;
        $cache = Zend_Registry::get('Zend_Cache');
        if (!$data = $cache->load($cacheKey)) {
            if ($limit === null) {
                $limit = Zend_Registry::get(
                    'catalog_config'
                )->box->newItems->limit;
            }
            $data = $this->getMapper()->fetchNew($limit);
            $cache->save($data, $cacheKey, array('item'));
        }
        return $data;
    }

    public function paginatorGetNew($rowsPerPage, $page)
    {
        $paginator = $this->getMapper()->paginatorFetchNew($rowsPerPage, $page);
        return $paginator;
    }

    public function paginatorGetActions($rowsPerPage, $page)
    {
        $paginator = $this->getMapper()->paginatorFetchActions(
            $rowsPerPage, $page
        );
        return $paginator;
    }

    public function paginatorGetPopular($rowsPerPage, $page)
    {
        $paginator = $this->getMapper()->paginatorFetchPopular(
            $rowsPerPage, $page
        );
        return $paginator;
    }

    public function getCrossSellers($id)
    {
        return $this->getMapper()->fetchCrossSellers($id);
    }

    public function addCrossSeller($id, $crossId)
    {
        return $this->getMapper()->addCrossSeller($id, $crossId);
    }

    public function deleteCrossSeller($id, $crossId)
    {
        return $this->getMapper()->deleteCrossSeller($id, $crossId);
    }

    public function getAlternates(Model_Object_Interface $item, $limit = null)
    {
        return $this->getMapper()->fetchAlternates($item->id, $limit);
    }

    public function getActions($limit = null)
    {
        return $this->getMapper()->fetchActions($limit);
    }

    /**
     * returns array('array'=> , 'html'=> , 'text'=> ,)
     * or one of them
     *
     * @param        mixed Model_Object_Interface|xml $item|$attribs
     * @param string $spec - one of NULL|"array"|"html"|"text"
     *
     * @return mixed
     */
    public function getAttributesSpecification($attribs = null, $spec = null)
    {
        $aArr = array();
        $aTxt = array();
        $aHtml = array();
        if ($attribs) {
            if (is_string($attribs)) {
                try {
                    $attribs = $this->parseAttributesFromXML($attribs);
                } catch (Exception $e) {
                    try {
                        $attribs = unserialize($attribs);
                    } catch (Exception $e) {
                        // do nothing
                    }
                }
            } else {
                if (get_class($attribs) == $this->getInjector()->getInjection(
                        'Model_Object_Interface'
                    )
                ) {
                    $item = $attribs;
                    $attribs = $item['attributes'];
                }
            }
            foreach ($attribs as $attr) {
                $aArr[$attr->code] = $attr->current_value;
                if ($attr->status > 0) {
                    if ($attr->type == 'variant') {
                        $outValue = $attr->variants->findByElement(
                            'value', $attr->current_value, true
                        )->text;
                    } else {
                        $outValue = $attr->current_value;
                    }
                    $aTxt[] = $attr->name . ' : ' . $outValue;
                    $aHtml[] = '<strong>' . $attr->name . '</strong> : ' . $outValue;
                }
            }
        }
        $result = array(
            'array' => $aArr,
            'text'  => implode(", \r\n", $aTxt),
            'html'  => implode('<br/>', $aHtml),
        );
        if ($spec !== null) {
            $result = $result[$spec];
        }
        return $result;
    }

    public function processImport()
    {
        return $this->getHelper('Importer')->process();
    }

    public function getAllWithCategory()
    {
        return $this->getMapper()->fetchAllWithCategory();
    }

    public function getComplexByCategories(
        $categories, $limit = null, $includingChildCategories = false
    ) {
        return $this->getMapper()->fetchComplexByCategories(
            $categories, $limit, 'item_status = 1', $includingChildCategories
        );
    }

    public function getAllIdsByCategories(
        $categories, $includingChildCategories = false
    ) {
        $items = $this->getMapper()->fetchComplexByCategories(
            $categories, null, $includingChildCategories
        );
        $ids = array();
        foreach ($items as $item) {
            $ids [] = $item->id;
        }
        return $ids;
    }

    public function getNewByCategories(
        $categories, $limit = null, $includingChildCategories = false
    ) {
        return $this->getMapper()->fetchNewByCategories(
            $categories, $limit, $includingChildCategories
        );
    }

    public function getPopularByCategories(
        $categories, $limit = null, $includingChildCategories = false
    ) {
        return $this->getMapper()->fetchPopularByCategories(
            $categories, $limit, $includingChildCategories
        );
    }

    public function paginatorGetByManufacturer(
        $manufacturer, $rowsPerPage, $page
    ) {
        if ($manufacturer instanceof Model_Object_Interface) {
            $manufacturer = $manufacturer->id;
        }
        return $this->getMapper()->paginatorFetchByManufacturer(
            $manufacturer, $rowsPerPage, $page
        );
    }

    public function setFilterParams($filter, array $params)
    {
        $f = new Zend_Filter_Word_DashToCamelCase;
        $filterName = $f->filter($filter);
        call_user_func_array(
            array($this->getMapper()->getPlugin('Filters'),
                  'set' . $filterName), $params
        );
        return $this;
    }

    /**
     * @param Model_Object_Interface
     *
     * @return $this
     */
    public function save(Model_Object_Interface $object)
    {
        $this->getMapper()->save($object);
        Zend_Registry::get('Zend_Cache')->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('item')
        );
        return $this;
    }

    /**
     * @param Model_Object_Interface
     *
     * @return $this
     */
    public function saveComplex(Model_Object_Interface $object)
    {
        $this->getMapper()->saveComplex($object);
        Zend_Registry::get('Zend_Cache')->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('item')
        );
        return $this;
    }

    /**
     * save it
     *
     * @param array
     * @param bool
     *
     * @return mixed Model_Service_Interface | Model_Object_Interface
     */
    public function saveFromValues(array $values, $returnObj = false)
    {
        /**
         * this "if" should be removed while refactoring
         */
        if (empty($values['id'])) {
            unset($values['id']);
        }
        $obj = $this->getMapper()->saveComplex($values, true);
        Zend_Registry::get('Zend_Cache')->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('item')
        );
        if ($returnObj === true) {
            return $obj;
        } else {
            return $this;
        }
    }

    /**
     * @see Catalog_Model_Mapper_Db_Item::fetchCurrentPreviousNext
     */
    public function getCurrentPreviousNext($id, $catId = null)
    {
        return $this->getMapper()->fetchCurrentPreviousNext($id, $catId);
    }

    public function paginatorGetSearchItems(
        $rowsPerPage = null, $page = null, $word, $categoryId
    ) {
        if ($rowsPerPage === null) {
            $rowsPerPage = Zend_Registry::get(
                'config'
            )->default->paginator->rowsPerPage;
        }
        if ($page === null) {
            $page = Zend_Controller_Front::getInstance()->getRequest()
                ->getParam('page');
        }
        return $this->getMapper()->paginatorFetchSearchItems(
            $rowsPerPage, $page, $word, $categoryId
        );
    }

    public function getHomeSliderItems()
    {
        return $this->getMapper()->fetchHomeSliderItems();
    }

    public function getRandomItem($kol = null)
    {
        return $this->getMapper()->fetchRandomItem($kol);
    }

    public function getAllActiveByCategory(
        $cat, $page, $rowsPerPage, $includeAllChildrenCategories = false
    ) {
        return $this->getMapper()->fetchAllActiveByCategory(
            $cat, $page, $rowsPerPage, $includeAllChildrenCategories
        );
    }

    public function getByText($text = null, $page, $rowsPerPage)
    {
        return $this->getMapper()->fetchByText($text, $page, $rowsPerPage);
    }

    public function getTotalCountByText($text = null)
    {
        return $this->getMapper()->fetchTotalCountByText($text);
    }

    public function getTotalCountByCategoryt($cat)
    {
        return $this->getMapper()->fetchTotalCountByCategory($cat);
    }

    /**
     * Получить все артикулы товара кроме текущего
     *
     * @param null|string $id
     *
     * @return array
     */
    public function getAllSkuWithoutCurrent($id = null)
    {
        return $this->getMapper()->fetchDistinctField(
            'sku', $id ? array('item_id <> ?' => $id) : null
        );
    }

    /**
     * Получить артикулы по категории
     *
     * @param $cat
     *
     * @return array
     */
    public function getSkuByCategory($cat)
    {
        return $this->getMapper()->fetchSkuByCategory($cat);
    }

    /**
     * Get item id by sku
     *
     * @param $sku
     *
     * @return mixed
     */
    public function getItemIdBySku($sku)
    {
        $result = $this->getMapper()->fetchItemIdBySku(trim($sku));

        if ($result !== false) return $result['id'];

        return null;
    }

    /**
     * Item to array
     *
     * @param Model_Object_Interface $item
     *
     * @return array
     * @throws Model_Service_Exception
     */
    public function itemToArray(Model_Object_Interface $item)
    {
        /** @var $itemService Catalog_Model_Service_Item */
        $itemService = Model_Service::factory('catalog/item');
        /** @var $bundleService Catalog_Model_Service_ItemBundle */
        $bundleService = Model_Service::factory('catalog/item-bundle');
        $attrSpec = $itemService->getAttributesSpecification($item);
        $bundleSpec = $bundleService->getBundlesSpecification($item);
        $arr = $item->toArray();
        $arr['attributes'] = $attrSpec['array'];
        $arr['attributes_text'] = $attrSpec['text'];
        $arr['attributes_html'] = $attrSpec['html'];
        $arr['price'] = $itemService->calculatePrice($item);
        $arr['bundles'] = $bundleSpec['array'];
        $arr['bundles_text'] = $bundleSpec['text'];
        $arr['bundles_html'] = $bundleSpec['html'];
        return $arr;
    }

}

