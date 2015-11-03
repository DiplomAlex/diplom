<?php

class Checkout_View_Helper_Order_Percent extends Zend_View_Helper_Abstract
{

    public function order_Percent($value)
    {
        if ( (float) $value != 0) {
            $html = $value.' %';
        }
        else {
            $html = '';
        }
        return $html;
    }

}