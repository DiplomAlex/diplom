<?php

class Catalog_Model_Service_ItemBundle extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Catalog_Model_Object_ItemBundle',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_ItemBundle',
        'Model_Mapper_Interface'     => 'Catalog_Model_Mapper_Db_ItemBundle',
        'Model_Object_Item'          => 'Catalog_Model_Object_Item',
        'Model_Object_Subitem'       => 'Catalog_Model_Object_Subitem',
        'Model_Collection_Subitem'   => 'Catalog_Model_Collection_Subitem',
        'Model_Mapper_XML_Subitem'   => 'Catalog_Model_Mapper_XML_Subitem',
    );
    
    /**
     * @param bool
     * @return array
     */
    public function getAllStatuses($translated = FALSE)
    {
        $result = array();
        $statuses = $this->getStatusesList();
        foreach ($statuses as $key=>$id) {
            if ($translated) {
                $result[$id] = $this->getTranslator()->_('bundle_status.'.$key);
            }
            else {
                $result[$id] = $key;
            }
        }
        return $result;
    }    
    
    public function createBundleFromValues(array $values)
    {
        $bundle = $this->create();
        $bundle->code = $values['code'];
        $bundle->name = $values['name'];
        $bundle->status = $values['status'];
        $bundle->is_required = $values['is_required'];
        $bundle->param1 = $values['param1'];
        $bundle->param2 = @$values['param2'];
        return $bundle;
    }
    
    public function createBundlesCollection()
    {
        return $this->getInjector()->getObject('Model_Collection_Interface');
    }
    
    public function createSubitemsCollection()
    {
        return $this->getInjector()->getObject('Model_Collection_Subitem');
    }
    
    /**
     * parse subitems from xml to collection
     *
     * @param string xml
     * @return Model_Collection_Interface
     */
    public function parseSubitemsFromXML($xml)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Subitem');
        if ( ! empty($xml)) {
            $res = new SimpleXMLElement($xml);
        }
        else {
            $res = NULL;
        }
        return $mapper->makeSimpleCollection($res);
    }

    public function parseSubitemsToXML(Model_Collection_Interface $coll)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Subitem');
        return $mapper->unmapCollectionToXML($coll);
    }
    
    public function saveBundlesForItem($item, Model_Collection_Interface $bundles)
    {
        if ($item instanceof Model_Object_Interface) {
            $itemId = $item->id;
        }
        else {
            $itemId = $item;
        }
        $this->clearBundlesForItem($itemId);
        foreach ($bundles as $bundle) {
            $bundle->id = NULL;
            $bundle->item_id = $itemId;
            $this->saveComplex($bundle);
        }
        Zend_Registry::get('Zend_Cache')->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('itemBundles', 'itemId_'.$itemId));
        return $this;
    }
    
    public function clearBundlesForItem($item)
    {
        if ($item instanceof Model_Object_Interface) {
            $itemId = $item->id;
        }
        else {
            $itemId = $item;
        }
        $this->getMapper()->clearBundlesForItem($itemId);
        return $this;
    }
    
    /**
     * @param mixed Model_Object_Interface | int
     * @return Model_Collection_Interface
     */
    public function getBundlesForItem($item)
    {
        if ($item instanceof Model_Object_Interface) {
            $itemId = $item->id;
        }
        else {
            $itemId = $item;
        }
        $cache = Zend_Registry::get('Zend_Cache');
        $cacheKey = 'bundles_for_item_'.$itemId;
        if ( ! $bundles = $cache->load($cacheKey)) {
            $bundles = $this->getMapper()->fetchBundlesForItem($itemId);
            $cache->save($bundles, $cacheKey, array('itemBundles', 'itemId_'.$itemId));
        }
        return $bundles;
    }

    
    /**
     * returns array('array'=> array(code=>array(id=>, title=>, qty=>), ...), 'html'=> , 'text'=> ,)
     * or one of them
     * 
     * @param mixed Catalog_Model_Object_Item|Catalog_Model_Collection_ItemBundle
     * @param string $spec - one of NULL|"array"|"html"|"text"
     * @return mixed
     */
    public function getBundlesSpecification($bundles = NULL, $spec = NULL)
    {
        $bArr = array();
        $bTxt = array();
        $bHtml = array();
        if ($bundles) {
            if (get_class($bundles) == $this->getInjector()->getInjection('Model_Object_Item')) {
                $item = $bundles;
                $bundles = $item['current_bundles'];
            }
            foreach ($bundles as $bundle) {
                if ($sub = $bundle->current_subitem) {
                    $val = $bundle->current_subitem_name;
                    $bArr[$bundle->code] = array('id'=>$sub->id, 'name'=>$val, 'qty'=>$sub->qty);
                    if ($sub->qty > 1) {
                        $strQty = ' ( x '.$sub->qty.')';
                    }
                    else {
                        $strQty = '';
                    }
                    if ($bundle->status > 0) {
                        $bTxt[]= $bundle->name.': '.$val.$strQty;
                        $bHtml[]= '<strong>'.$bundle->name.'</strong> : '.$val.$strQty;
                    }
                }
            }
        }
        $result = array(
            'array' => $bArr,
            'text'  => implode(", \r\n", $bTxt),
            'html'  => implode('<br/>', $bHtml),
        );
        if ($spec !== NULL) {
            $result = $result[$spec];
        }
        return $result;
    }

    
    public function mergeAllWithCurrentsFromArray(Model_Object_Interface $item, array $bundlesArr)
    {
        if (array_key_exists('bundles', $bundlesArr)) {
            $bundlesArr = $bundlesArr['bundles'];
        }
        $bundles = $this->getBundlesForItem($item);
        foreach ($bundles as $bundle) {
            if (array_key_exists($bundle->code, $bundlesArr)) {
                $val = $bundlesArr[$bundle->code];
                if (is_array($val)) {
                    $bundle->current_subitem_id = $val['id'];
                }
                else {
                    $bundle->current_subitem_id = $val;
                }
            }
        }
        return $bundles;
    }
    
    /**
     * gets real codes by aliases from config
     * @param string $alias
     * @return string 
     */
    public function getCodeByAlias($alias)
    {
        if (($code = Zend_Registry::get('catalog_config')->itemBundleAlias->{$alias}) AND ( ! empty($code))) {
            $result = $code;
        }
        else {
            $result = $alias;
        }
        return $result;
    }
        
    
    
}