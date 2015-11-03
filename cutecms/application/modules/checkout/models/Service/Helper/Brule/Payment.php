<?php

class Checkout_Model_Service_Helper_Brule_Payment extends Model_Service_Helper_Abstract
{

    protected $_method = NULL;
    protected $_driver = NULL;

    public function setMethod($method)
    {
        $this->_method = $method;
        $this->_driver = $this->_getDriverByMethod($method);
        return $this;
    }

    public function onBeforeSelect(Model_Collection_Interface $payments, Model_Object_Interface $order)
    {
        if ($payments->count() == 1) {
            $order->payment = $payments->get(0);
        }
    }

    public function onAfterSelect(Model_Object_Interface $payment, Model_Object_Interface $order)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if (empty($this->_method)) {
            $this->_throwException('method was not selected');
        }
        $this->_driver->onAfterSelect($payment, $order);
    }


    public function onBeforePrepare(Model_Object_Interface $payment, Model_Object_Interface $order)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if (empty($this->_method)) {
            $this->_throwException('method was not selected');
        }
        $this->_driver->onBeforePrepare($payment, $order);
    }

    public function onAfterPrepare(Model_Object_Interface $payment, Model_Object_Interface $order, array $values)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if (empty($this->_method)) {
            $this->_throwException('method was not selected');
        }
        $this->_driver->onAfterPrepare($payment, $order, $values);
    }

    public function isPrepared(Model_Object_Interface $payment, Model_Object_Interface $order)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if (empty($this->_method)) {
            $this->_throwException('method was not selected');
        }
        return $this->_driver->isPrepared($payment, $order);
    }


    public function onRecalculate(Model_Object_Interface $payment, Model_Object_Interface $order)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if (empty($this->_method)) {
            $this->_throwException('method was not selected');
        }
        $this->_driver->onRecalculate($payment, $order);
    }

    public function onProcess(Model_Object_Interface $payment, Model_Object_Interface $order)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if (empty($this->_method)) {
            $this->_throwException('method was not selected');
        }
        $this->_driver->onProcess($payment, $order);
    }


    public function getAvailablePayments(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {
        $pays = Model_Service::factory('checkout/payment')->getAll();
        foreach ($pays as $key=>$obj) {
            if ( ! (( (int) $obj->active) AND ( ! $shipment
                 OR
                     ( ! empty($obj->allowed_shipments) AND ($obj->allowed_shipments==$shipment->method))
                     OR empty($shipment->allowed_payments)
                     OR in_array($method, $shipment->allowed_payments)))) {
                         $pays->remove($key);
            }
        }
        return $pays;
    }

    protected function _getDriverByMethod($method)
    {
        $iface = 'Model_Service_Helper_Brule_Payment_'.$this->_filterDashToCamelCase($method);
        $injector = $this->getService()->getInjector();
        if ($injector->hasInjection($iface)) {
            $obj = $injector->getObject($iface, $this);
        }
        else if (($configHelperClass = Model_Service::factory('checkout/brule')->getAll(Checkout_Model_Service_Brule::TYPE_PAYMENT)->get($method)->classModel)
                 AND ( ! empty($configHelperClass))) {
            $injector->inject($iface, $configHelperClass);
            $obj = $injector->getObject($iface, $this);
        }
        else if (($ifaceCheckout = 'Checkout_'.$iface) AND ($injector->hasInjection($ifaceCheckout))) {
            $obj = $injector->getObject($ifaceCheckout, $this);
        }
        else {
            $obj = new $ifaceCheckout($this);
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
