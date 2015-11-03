<?php

/**
 * constant discount to items for user
 * value of dicount percent must be set into user's "personal_discount" field
 *
 */

class Catalog_Model_Service_Helper_Brule_Item_Configurable implements Catalog_Model_Service_Helper_Brule_Item_Interface
{


    protected $_rawPrice = NULL;
    protected $_prevPrice = NULL;

    protected $_brule = NULL;

    public function __construct(Model_Object_Interface $brule)
    {
        $this->_brule = $brule;
    }

    public function setRawPrice($price)
    {
        $this->_rawPrice = $price;
        return $this;
    }

    public function setPrevPrice($price)
    {
        $this->_prevPrice = $price;
        return $this;
    }

    public function calculatePrice(Model_Object_Interface $obj)
    {
        $prevPrice = $this->_getPrevPrice($obj);
        $price = $prevPrice + $this->_calculateAttributesPrice($obj) + $this->_calculateBundlesPrice($obj);
        return $price;
    }

    public function calculateDiscountValue(Model_Object_Interface $obj)
    {
        return 0;
    }

    public function calculateDiscountPercent(Model_Object_Interface $obj)
    {
        return 0;
    }

    protected function _getRawPrice(Model_Object_Interface $obj)
    {
        if ( ! $result = (float) $this->_rawPrice) {
            $result = (float) $obj->price;
        }
        return $result;
    }

    protected function _getPrevPrice(Model_Object_Interface $obj)
    {
        if ( ! $result = (float) $this->_prevPrice) {
            $result = $this->_getRawPrice($obj);
        }
        return $result;
    }
    
    protected function _calculateAttributesPrice(Model_Object_Interface $obj)
    {
        $price = 0;
        if ($obj->attributes AND $obj->attributes->count()) { 
            foreach ($obj->attributes as $attr) {
                if ($attr->type == 'variant') {
                    if ($attr->current_variant AND $attr->current_variant->param1) {
                        $price += (float) $attr->current_variant->param1;
                    }
                }
                else {
                    if ($attr->param1) {
                        $price += (float) $attr->param1;
                    }
                } 
            }
        }
        return $price;        
    }

    protected function _calculateBundlesPrice(Model_Object_Interface $obj)
    {
        $bundles = Model_Service::factory('catalog/item-bundle')->getBundlesForItem($obj);
        $price = 0;
        if ($obj->current_bundles AND $obj->current_bundles->count()) { 
            foreach ($obj->current_bundles as $bundle) {
                if ($bundle->current_subitem_price) {
                    $price += (float) $bundle->current_subitem_price;
                }
            }
        }
        return $price;
    }

}

