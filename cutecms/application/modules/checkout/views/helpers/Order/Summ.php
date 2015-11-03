<?php

class Checkout_View_Helper_Order_Summ extends Zend_View_Helper_Abstract
{
    
    public function order_Summ($value, $currency = NULL)
    {
        $currService = Model_Service::factory('currency');
        $html = sprintf('%01.2f', $value);
        if ($currency === FALSE) {
            $result = $html;
        }
        else { 
            if ($currency === NULL) {
                $curr = $currService->getCurrent();
            }
            else {
                $curr = $currService->getByCode($currency);
            }
            $result = $curr->signPre.$html.' '.$curr->signPost;
        }
        return $result;
    }
    

}