<?php

class Checkout_Model_Service_Helper_Brule_Shipment extends Model_Service_Helper_Abstract
{

    protected $_method = NULL;
    protected $_driver = NULL;

    public function setMethod($method)
    {
        $this->_method = $method;
        $this->_driver = $this->_getDriverByMethod($method);
        return $this;
    }

    public function onBeforeSelect(Model_Collection_Interface $shipments, Model_Object_Interface $order)
    {
        if ($shipments->count() == 1) {
            $order->shipment = $shipments->get(0);
        }
    }

    public function onAfterSelect(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if (empty($this->_method)) {
            $this->_throwException('method was not selected');
        }
        $this->_driver->onAfterSelect($shipment, $order);
    }


    public function onBeforePrepare(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if (empty($this->_method)) {
            $this->_throwException('method was not selected');
        }
        $this->_driver->onBeforePrepare($shipment, $order);
    }

    public function onAfterPrepare(Model_Object_Interface $shipment, Model_Object_Interface $order, array $values)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if (empty($this->_method)) {
            $this->_throwException('method was not selected');
        }
        $this->_driver->onAfterPrepare($shipment, $order, $values);
    }

    public function isPrepared(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if (empty($this->_method)) {
            $this->_throwException('method was not selected');
        }
        return $this->_driver->isPrepared($shipment, $order);
    }


    public function onRecalculate(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if (empty($this->_method)) {
            $this->_throwException('method was not selected');
        }
        $this->_driver->onRecalculate($shipment, $order);
    }

    public function getAvailableShipments(Model_Object_Interface $payment, Model_Object_Interface $order)
    {
        $ships = Model_Service::factory('checkout/shipment')->getAll();
        foreach ($ships as $key=>$obj) {
            if ( ! (( (int) $obj->active) AND ( ! $payment
                 OR
                 ( ! empty($obj->allowed_payments) AND ($obj->allowed_payments==$payment->method))
                 OR empty($payment->allowed_shipments)
                 OR in_array($method, $payment->allowed_shipments)))) {
                    $ships->remove($key);
            }
        }
        return $ships;
    }



    protected function _getDriverByMethod($method)
    {
        $iface = 'Model_Service_Helper_Brule_Shipment_'.$this->_filterDashToCamelCase($method);
        $injector = $this->getService()->getInjector();
        if ($injector->hasInjection($iface)) {
            $obj = $injector->getObject($iface);
        }
        else if (($configHelperClass = Model_Service::factory('checkout/brule')->getAll(Checkout_Model_Service_Brule::TYPE_SHIPMENT)->get($method)->classModel)
                 AND ( ! empty($configHelperClass))) {
            $injector->inject($iface, $configHelperClass);
            $obj = $injector->getObject($iface, $this);
        }
        else if (($ifaceCheckout = 'Checkout_'.$iface) AND ($injector->hasInjection($ifaceCheckout))) {
            $obj = $injector->getObject($ifaceCheckout);
        }
        else {
            if ( ! class_exists($ifaceCheckout)) {
                App_Debug::dump(debug_backtrace());
                $this->_throwException('unknown class "'.$ifaceCheckout.'" for shipment brule "method"');
            }
            $obj = new $ifaceCheckout;
        }
        return $obj;
    }


    protected function _filterDashToCamelCase($str)
    {
        $flt = new Zend_Filter_Word_DashToCamelCase();
        $result = $flt->filter($str);
        return $result;
    }




}
