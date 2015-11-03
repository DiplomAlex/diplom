<?php

class Catalog_Model_Service_Helper_Importer_Item extends Catalog_Model_Service_Helper_Importer_Abstract
{
    
    protected $_filenameItem = NULL;
    protected $_filenamePrice = NULL;
    protected $_backup = array();
    
    protected $_fieldAliasPrice = NULL;
    protected $_fieldAliasPrice2 = NULL;
    protected $_fieldAliasPrice3 = NULL;
    
    /**
     * 1) читаем xml из файла продуктов
     * 2) читаем xmlPrice из файла цен
     * 3) маппим xml и xmlPrice в Catalog_Model_Collection_Item
     * 4) обновляем у всей коллекции id по guid
     * 5) сохраняем коллекцию через db-маппер
     */
    public function process()
    {
        
        $itemXml = $this->_getXml($this->getFilename('item'), 'items', array(
                'product_name', 'product_brief_desc', 'product_full_desc', 
                'product_model', 'product_manufacturer', 
                'product_unit', 'product_code', 'product_sku',
        ));        
        $priceXml = $this->_getXml($this->getFilename('price'), 'prices')->product;        
        $prices = array();
        foreach ($priceXml as $row) {
            if ( ! array_key_exists($row->price_product_guid, $prices)) {
                $prices[$row->price_product_guid] = array();
            }
            $prices[$row->price_product_guid][$row->price_type] = $row->price_value;
        }      
        
        $xmlMapper = $this->getService()->getInjector()->getObject('Model_Mapper_Importer');
        $dbMapper = $this->getService()->getMapper();
        $coll = $xmlMapper->makeComplexCollection($itemXml->product);
        $guids = array();
        $fieldPrice  = $this->getFieldAlias('price');
        $fieldPrice2 = $this->getFieldAlias('price2');
        $fieldPrice3 = $this->getFieldAlias('price3');
        foreach ($coll as $obj) {
            $guids []= $obj->guid;
            if (array_key_exists($obj->guid, $prices)) {
                if (array_key_exists($fieldPrice, $prices[$obj->guid])) {
                    $obj->price = $prices[$obj->guid][$fieldPrice];
                }
                if (array_key_exists($fieldPrice2, $prices[$obj->guid])) {
                    $obj->price2 = $prices[$obj->guid][$fieldPrice2];
                }
                if (array_key_exists($fieldPrice3, $prices[$obj->guid])) {
                    $obj->price3 = $prices[$obj->guid][$fieldPrice3];
                }
            }
        }
        $ids = $dbMapper->fetchIdsByGuids($guids); // from db-mapper        
        foreach ($coll as $obj) {
            if (array_key_exists($obj->guid, $ids)) {
                $obj->id = $ids[$obj->guid];
            }
        }
        $dbMapper->saveImportedCollection($coll);
        $this->_sanitize();
    }
    
    public function getFieldAlias($field)
    {
        $property = '_fieldAlias'.ucfirst($field);
        if ($this->{$property} === NULL) {
            $this->{$property} = Zend_Registry::get('catalog_config')->importItem->fieldAlias->{$field};
        }
        return $this->{$property};
    }
    
    public function setFieldAlias($field, $alias)
    {
        $property = '_fieldAlias'.ucfirst($field);
        $this->{$property} = $alias;
        return $this;
    }
    
    
    /**
     * @return string
     */
    public function getFilename($prefix)
    {
        $property = '_filename'.ucfirst($prefix);
        if ($this->{$property} === NULL) {
            $this->{$property} = APPLICATION_PATH.'/'.Zend_Registry::get('catalog_config')->importItem->{$prefix.'File'};
        }
        return $this->{$property};
    }
    
    public function setFilename($prefix, $filename)
    {
        $property = '_filename'.ucfirst($prefix);
        $this->{$property} = $filename;
        return $this;
    }

    /**
     * @param string - $prefix is the same as for getFilename()
     * @return string
     */
    public function getBackupFilename($prefix)
    {
        if ( ! array_key_exists($prefix, $this->_backup)) {
            $info = pathinfo($this->getFilename($prefix));
            $this->_backup[$prefix] = $info['dirname'] . '/backup/' . $info['filename']. '_' . date('Y-m-d H_i_s').'.'.$info['extension'];
        }
        return $this->_backup[$prefix];
    }    
    
    /**
     * backup imported file and remove original
     */
    protected function _sanitize()
    {
        $itemFilename = $this->getFilename('item');
        $priceFilename = $this->getFilename('price');
        copy($itemFilename, $this->getBackupFilename('item'));
        copy($priceFilename, $this->getBackupFilename('price'));
        unlink($itemFilename);
        unlink($priceFilename);
    }
    
    
    
}