<?php


/**
 * makes discount if total summ of order reaches some value
 */

class Checkout_Model_Service_Helper_Brule_Total_DiscountBySumm extends Checkout_Model_Service_Helper_Brule_Total_Abstract
{

    const MIN_SUMM = 50;
    const PERCENT = 10;

    public function calculateDiscountPercent()
    {
        if ($this->_cache['discountPercent'] === NULL) {
            $summ = $this->getPrevSumm();
            if ($summ > self::MIN_SUMM) {
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