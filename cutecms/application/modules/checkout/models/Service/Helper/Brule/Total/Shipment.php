<?php


/**
 * add shipment value to total summ of order
 */

class Checkout_Model_Service_Helper_Brule_Total_Shipment extends Checkout_Model_Service_Helper_Brule_Total_Abstract
{

    public function calculateDiscountPercent()
    {
        return 0;
    }

    public function calculateDiscountValue()
    {
        if (($this->_order === NULL) OR ( ! $summ = $this->_order->shipment->summ)) {
            return 0;
        }
        else {
            return $summ;
        }
    }

    public function calculateTotal()
    {
        return $this->getPrevSumm() + $this->calculateDiscountValue();
    }

    public function getOutputSumm()
    {
        return $this->calculateDiscountValue();
    }

    public function isVisible()
    {
        if ($this->_order === NULL) {
            return FALSE;
        }
        return TRUE;
        $summ = $this->_order->shipment->summ;
        if ($summ > 0) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }


}