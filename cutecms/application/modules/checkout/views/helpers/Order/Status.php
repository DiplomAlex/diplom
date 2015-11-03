<?php

class Checkout_View_Helper_Order_Status extends Zend_View_Helper_Abstract
{

    public function order_Status($order)
    {
        if ($order instanceof Model_Object_Interface) {
            $status = $order->status;
        }
        else {
            $status = $order;
        }
        $code = Model_Service::factory('checkout/order')->getStatusCodeByValue($status);
        $result = $this->view->translate('orderStatus.'.$code);
        return $result;
    }

}