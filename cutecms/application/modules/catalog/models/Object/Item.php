<?php

class Catalog_Model_Object_Item extends Model_Object_Abstract
{
    
    const TYPE_CONFIGURABLE = 1;
    const TYPE_DOWNLOADABLE = 2;
    

    protected $_defaultInjections = array(
        'Model_Collection_Attribute' => 'Catalog_Model_Collection_Attribute',
        'Model_Collection_Brule'     => 'Catalog_Model_Collection_Brule',
        'Model_Collection_Image'     => 'Catalog_Model_Collection_Image',
        'Model_Service_Attribute'    => 'Catalog_Model_Service_Attribute',  
    );

    protected $_attrsParsed = array();

    public function init()
    {

        $this->addElements(array(
            'id', 'seo_id', 'row_number', 
            'sku',
            'code', 'unit',
            'status',
            'type', 'is_configurable', 'is_downloadable',
            'sort',        
            'name',
            'material',
            'brief',
            'full',
            'full2',
            'full3',
            'full4',
            'more',
            'html_title',
            'meta_description',
            'meta_keywords',
            'date_added', 'date_changed',
            'adder_id', 'changer_id',
            'rc_id', 'rc_id_filename', 'rc_id_preview',  'rc_id_preview2', 'rc_id_preview3', 'rc_id_preview4',
            'rc_id_preview5',  'rc_id_preview6', 'rc_id_preview7', 'rc_id_preview8', 'rc_id_preview9', 'rc_id_preview10', 
            'param1', 'param2', 'param3',
            'attributes', 'attributes_xml',
            'images', 'images_xml',
            'brules', 'brules_xml',
            'price', 'price2', 'price3', 'old_price',
            'qty', 'stock_qty',
            'model', 'manufacturer',
            'site_ids',

            'is_new',
            'is_popular',
            'home_page_our_collections',
            'home_page_item_slider',
            'manufacturer_id',
            'manufacturer_name',
        
            /* for importer */
            'guid', 'category_guid', 'delete',
        
            'comments_count',
        
            'current_bundles', /* bundles user configured */
        
            'category_id', 'category_name', 'category_seo_id',
            'views', 'votes', 'rate',

            /* From remains */
            'size',
            'weight',
            'characteristics',
            'remain_price',
            'probe'
        ));

    }


    /**
     * @return Model_Collection_Interface
     */
    /*public function getAttributes()
    {
        $xml = $this->attributes_xml;
        $md5Xml = md5($xml);
        if ( ! isset($this->_attrsParsed[$md5Xml])) {
            $this->_attrsParsed = array($md5Xml => Model_Service::factory('catalog/item')->parseAttributesFromXML($xml));
        }
        return $this->_attrsParsed[$md5Xml];
    }*/

    /**
     * @param Model_Collection_Interface
     */
    /*public function setAttributes(Model_Collection_Interface $coll)
    {
        $this->attributes_xml = Model_Service::factory('catalog/item')->parseAttributesToXML($coll);
        $this->_attrsParsed = array(md5($this->attributes_xml) => $coll);
    }*/


    public function getAttributes()
    {
        $service = Model_Service::factory('catalog/item');
        $val = $this->_elements['attributes'];
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Attribute');
        }
        else {
            try {
                $newVal = $service->parseAttributesFromXML($val);
            }
            catch (Exception $e) {                
                var_dump($this->_elements);exit;
            }
        }
        $this->_elements['attributes'] = $newVal;
       return $this->_elements['attributes'];
    }


    public function setAttributes($val)
    {
        $service = Model_Service::factory('catalog/item');
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Attribute');
        }
        else {
            try {
                $newVal = $service->parseAttributesFromXML($val);
            }
            catch (Exception $e) {
                var_dump($this->_elements);exit;
            }
        }
        $this->_elements['attributes'] = $newVal;
        return $this;
    }



    public function getBrules()
    {
        $service = Model_Service::factory('catalog/item');
        $val = $this->_elements['brules'];
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Brule');
        }
        else {
            $newVal = $service->parseBrulesFromXML($val);
        }
        $this->_elements['brules'] = $newVal;
       return $this->_elements['brules'];
    }


    public function setBrules($val)
    {
        $service = Model_Service::factory('catalog/item');
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Brule');
        }
        else {
            $newVal = $service->parseBrulesFromXML($val);
        }
        $this->_elements['brules'] = $newVal;
       return $this;
    }



    public function getImages()
    {
        $service = Model_Service::factory('catalog/item');
        $val = $this->_elements['images'];
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Image');
        }
        else {
            $newVal = $service->parseImagesFromXML($val);
        }
        $this->_elements['images'] = $newVal;
       return $this->_elements['images'];
    }


    public function setImages($val)
    {
        $service = Model_Service::factory('catalog/item');
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Image');
        }
        else {
            $newVal = $service->parseImagesFromXML($val);
        }
        $this->_elements['images'] = $newVal;
        return $this;
    }


    /**
     * getter for configurable flag
     * @return bool
     */    
    public function getIs_configurable()
    {
        return (bool) ($this->type & self::TYPE_CONFIGURABLE);
    }
    
    /**
     * setter for configurable flag
     * @param bool $is
     * @return $this
     */
    public function setIs_configurable($is)
    {
        if ( (bool) $is === TRUE) {
            $this->_elements['type'] = $this->_elements['type'] | self::TYPE_CONFIGURABLE;
        }
        else {
            $this->_elements['type'] = ($this->_elements['type'] | self::TYPE_CONFIGURABLE) ^ self::TYPE_CONFIGURABLE;
        }
        return $this;
    }


    /**
     * getter for downloadable flag
     * @return bool
     */    
    public function getIs_downloadable()
    {
        return (bool) ($this->type & self::TYPE_DOWNLOADABLE);
    }
    
    /**
     * setter for downloadable flag
     * @param bool $is
     * @return $this
     */
    public function setIs_downloadable($is)
    {       
        if ( (bool) $is === TRUE) {
            $this->type = $this->type | self::TYPE_DOWNLOADABLE;
        }
        else {
            $this->type = ($this->type | self::TYPE_DOWNLOADABLE) ^ self::TYPE_DOWNLOADABLE;
        }
        return $this;
    }

    public function isCalculatable()
    {
        if ($this->price) {
            $attrsOk = TRUE;
            /*foreach ($this->attributes as $attr) {
                if ($attr->isInputRequired()) {
                    $attrsOk = FALSE;
                    break;
                }
            }*/
            $bundlesOk = TRUE;
            if ($attrsOk AND $this->is_configurable) {
                foreach ($this->current_bundles as $bundle) {
                    if ($bundle->isInputRequired()) {
                        $bundlesOk = FALSE;
                        break;
                    }                
                }
            }
            $result = ($attrsOk AND $bundlesOk);
        }
        else {
            $result = FALSE;
        }
        return $result;
    }
    
    
    /**
     * sometimes it is usefull to think about some attributes as they are properties of item
     * here is an example of how it can be organized
     * (attribute qty is used as property qty)
     * 
        public function getQty()
        {
            return $this->attributes->qty->current_value;
        }
        
        public function setQty($value)
        {
            $this->attributes->qty->current_value = $value;
            return $this;
        }
     *  
     *  
     */
    
    protected function _getAttributeCodeByAlias($alias)
    {
        return $this->_getAttrService()->getCodeByAlias($alias);
    }
    
    protected function _getAttrService()
    {
        if ($this->_attrService === NULL) {
            $this->_attrService = $this->getInjector()->getObject('Model_Service_Attribute');
        }
        return $this->_attrService;
    }
    
    public function setManufacturer_id($value)
    {        
        if ( ! (int) $value) {
            $value = NULL;
        }
        $this->_elements['manufacturer_id'] = $value;
        return $this;
    }

}

