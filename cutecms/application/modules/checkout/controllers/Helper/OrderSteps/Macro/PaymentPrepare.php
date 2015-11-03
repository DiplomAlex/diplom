<?php

class Checkout_Controller_Action_Helper_OrderSteps_Macro_PaymentPrepare extends Checkout_Controller_Action_Helper_OrderSteps_Macro_Abstract
{

    public function run(Model_Service_Helper_Interface $paymentHelper, Model_Service_Helper_Interface $shipmentHelper, Model_Object_Interface $order, array $params = NULL)
    {
        if ( ! empty($order->payment->method) AND ! $paymentHelper->isPrepared($order->payment, $order)) {
            if (empty($params['urlPaymentPrepare'])) {
                throw new Zend_Controller_Exception('"urlPaymentPrepare" param is empty in '.__CLASS__.' #'.__LINE__);
            }                        
            $this->_controller->getHelper('Redirector')->gotoUrlAndExit($params['urlPaymentPrepare']);
        }

    }

}