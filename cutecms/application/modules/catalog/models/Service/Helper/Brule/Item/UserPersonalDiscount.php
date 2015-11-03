<?php

/**
 * constant discount to items for user
 * value of dicount percent must be set into user's "personal_discount" field
 *
 */

class Catalog_Model_Service_Helper_Brule_Item_UserPersonalDiscount implements Catalog_Model_Service_Helper_Brule_Item_Interface
{

    protected static $_cache = array();


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
        $hash = $this->_hash($obj);
        if ( ! isset(self::$_cache['price'][$hash])) {
            $prevPrice = $this->_getPrevPrice($obj);
            $price = $prevPrice - $this->calculateDiscountValue($obj);
            self::$_cache['price'][$hash] = $price;
        }
        return self::$_cache['price'][$hash];
    }

    public function calculateDiscountValue(Model_Object_Interface $obj)
    {
        $hash = $this->_hash($obj);
        if ( ! isset(self::$_cache['discountValue'][$hash])) {
            $rawPrice = $this->_getRawPrice($obj);
            $percent = $this->calculateDiscountPercent($obj);
            $value = (float) $rawPrice*($percent/100);
            self::$_cache['discountValue'][$hash] = $value;
        }
        return self::$_cache['discountValue'][$hash];
    }

    public function calculateDiscountPercent(Model_Object_Interface $obj)
    {
        $hash = $this->_hash($obj);
        if ( ! isset(self::$_cache['discountPercent'][$hash])) {
            self::$_cache['discountPercent'][$hash] = $this->_userPercent();
        }
        return self::$_cache['discountPercent'][$hash];
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

    protected function _userPercent()
    {
        if ($user = Model_Service::factory('user')->getCurrent()) {
            $percent = (float) $user->personal_discount;
        }
        else {
            $percent = (float) '0';
        }
        return $percent;
    }

    protected function _hash(Model_Object_Interface $obj)
    {
        $hash = md5($this->_getRawPrice($obj).'_'.$this->_userPercent());
        return $hash;
    }

}

