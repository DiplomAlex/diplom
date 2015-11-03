<?php

class Checkout_Controller_Action_Helper_OrderSteps_Macro_ShipmentPrepare extends Checkout_Controller_Action_Helper_OrderSteps_Macro_Abstract
{

    public function run(Model_Service_Helper_Interface $paymentHelper, Model_Service_Helper_Interface $shipmentHelper, Model_Object_Interface $order, array $params = NULL)
    {
        if ( ! empty($order->shipment->method) AND ! $shipmentHelper->isPrepared($order->shipment, $order)) {
            if (empty($params['urlShipmentPrepare'])) {
                throw new Zend_Controller_Exception('"urlShipmentPrepare" param is empty in '.__CLASS__.' #'.__LINE__);
            }            
            $this->_controller->getHelper('Redirector')->gotoUrlAndExit($params['urlShipmentPrepare']);
        }
    }

}