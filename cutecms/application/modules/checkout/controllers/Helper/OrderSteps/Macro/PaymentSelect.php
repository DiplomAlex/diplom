<?php

class Checkout_Controller_Action_Helper_OrderSteps_Macro_PaymentSelect extends Checkout_Controller_Action_Helper_OrderSteps_Macro_Abstract
{

    public function run(Model_Service_Helper_Interface $paymentHelper, Model_Service_Helper_Interface $shipmentHelper, Model_Object_Interface $order, array $params = NULL)
    {
        if ($params['payments'] AND empty($order->payment->method)) {
            $paymentHelper->onBeforeSelect($params['payments'], $order);
        }
        if ($params['payments'] AND empty($order->payment->method)) {
            if (empty($params['urlPaymentSelect'])) {
                throw new Zend_Controller_Exception('"urlPaymentSelect" param is empty in '.__CLASS__.' #'.__LINE__);
            }                        
            $this->_controller->getHelper('Redirector')->gotoUrlAndExit($params['urlPaymentSelect']);
        }
        if ( ! empty($order->payment->method)) {
            $paymentHelper->onAfterSelect($order->payment, $order);
        }
    }

}