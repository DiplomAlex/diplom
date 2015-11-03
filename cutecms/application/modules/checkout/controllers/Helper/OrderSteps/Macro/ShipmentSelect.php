<?php

class Checkout_Controller_Action_Helper_OrderSteps_Macro_ShipmentSelect extends Checkout_Controller_Action_Helper_OrderSteps_Macro_Abstract
{

    public function run(Model_Service_Helper_Interface $paymentHelper, Model_Service_Helper_Interface $shipmentHelper, Model_Object_Interface $order, array $params = NULL)
    {
        if ($params['shipments'] AND empty($order->shipment->method)) {
            $shipmentHelper->onBeforeSelect($params['shipments'], $order);
        }
        if ($params['shipments'] AND empty($order->shipment->method)) {
            if (empty($params['urlShipmentSelect'])) {
                throw new Zend_Controller_Exception('"urlShipmentSelect" param is empty in '.__CLASS__.' #'.__LINE__);
            }
            $this->_controller->getHelper('Redirector')->gotoUrlAndExit($params['urlShipmentSelect']);
        }        
        if ( ! empty($order->shipment->method)) {
            $shipmentHelper->onAfterSelect($order->shipment, $order);
        }
    }


}