<?php

class Checkout_View_Helper_Order_Total extends Zend_View_Helper_Abstract
{

    public function order_Total(Model_Object_Interface $order, $returnHtml = TRUE, $printMode = FALSE, $module = 'checkout')
    {
        $viewData = Model_Service::factory('checkout/order')->getHelper('BruleTotal')
                                                            ->setOrder($order)->getRows();
        if ($returnHtml) {
        	if ($printMode) {
        		$script = 'print-total.phtml';
        	}
        	else {
        		$script = 'total.phtml';
        	}
            $result = $this->view->partial('order/'.$script, $module, array('data'=>$viewData, 'order'=>$order));
        }
        else {
            $result = $viewData;
        }
        return $result;
    }

}