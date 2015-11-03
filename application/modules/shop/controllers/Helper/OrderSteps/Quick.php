<?php

class Shop_Controller_Action_Helper_OrderSteps_Quick extends Checkout_Controller_Action_Helper_OrderSteps_Abstract
{

    public function walk(array $params = NULL)
    {
        $service                      = $this->_getOrderService();
        $order                        = $service->getCurrent();
        $params['shipments']          = $service->getAvailableShipments();
        $params['payments']           = $service->getAvailablePayments();
        $helperShipment               = $service->getHelper('BruleShipment');
        $helperPayment                = $service->getHelper('BrulePayment');
        $params['urlShipmentSelect']  = $params['urlQuick'];
        $params['urlShipmentPrepare'] = $params['urlQuick'];
        $params['urlPaymentSelect']   = $params['urlQuick'];
        $params['urlPaymentPrepare']  = $params['urlQuick'];
        $this->_getMacroByCode('shipment-select') ->run($helperPayment, $helperShipment, $order, $params);
        $this->_getMacroByCode('shipment-prepare')->run($helperPayment, $helperShipment, $order, $params);
        $this->_getMacroByCode('payment-select')  ->run($helperPayment, $helperShipment, $order, $params);
        $this->_getMacroByCode('payment-prepare') ->run($helperPayment, $helperShipment, $order, $params);
        $this->_getMacroByCode('confirm')         ->run($helperPayment, $helperShipment, $order, $params);
        $this->_getMacroByCode('payment-process') ->run($helperPayment, $helperShipment, $order, $params);
        $this->_getMacroByCode('finish')          ->run($helperPayment, $helperShipment, $order, $params);
    }

}