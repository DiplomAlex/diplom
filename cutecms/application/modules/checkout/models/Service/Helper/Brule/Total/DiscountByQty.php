<?php


/**
 * make discount if total qty of items reaches some value
 */

class Checkout_Model_Service_Helper_Brule_Total_DiscountByQty extends Checkout_Model_Service_Helper_Brule_Total_Abstract
{

    const MIN_QTY = 2;
    const PERCENT = 2.5;

    public function calculateDiscountPercent()
    {
        if ($this->_cache['discountPercent'] === NULL) {
            $qty = $this->calculateTotalQty();
            if ($qty > self::MIN_QTY) {
                $this->_cache['discountPercent'] = self::PERCENT;
            }
            else {
                $this->_cache['discountPercent'] = 0;
            }
        }
        return $this->_cache['discountPercent'];
    }

    public function isVisible()
    {
        if ($this->calculateDiscountValue() != 0) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

}