<?php

class Checkout_Controller_Action_Helper_OrderSteps_Macro_PaymentProcess extends Checkout_Controller_Action_Helper_OrderSteps_Macro_Abstract
{

    public function run(Model_Service_Helper_Interface $paymentHelper, Model_Service_Helper_Interface $shipmentHelper, Model_Object_Interface $order, array $params = NULL)
    {
        if (    $paymentHelper->isPrepared($order->payment, $order) 
            AND $order->payment->isOnlinePaid()
            AND ! $order->payment->isPayed()
            ) {
            if (empty($params['urlPaymentProcess'])) {
                throw new Zend_Controller_Exception('"urlPaymentProcess" param is empty in '.__CLASS__.' #'.__LINE__);
            }                            
            $this->_controller->getHelper('Redirector')->gotoUrlAndExit($params['urlPaymentProcess']);
        }
    }


}