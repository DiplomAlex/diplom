<?php


/**
 * just calculate total summ as it is
 */

class Checkout_Model_Service_Helper_Brule_Total_TotalSumm extends Checkout_Model_Service_Helper_Brule_Total_Abstract
{

    public function calculateDiscountPercent()
    {
        return 0;
    }

    public function isVisible()
    {
        return TRUE;
    }


}