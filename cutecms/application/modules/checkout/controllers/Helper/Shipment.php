<?php

class Checkout_Controller_Action_Helper_Shipment extends Zend_Controller_Action_Helper_Abstract
{

    protected $_method = NULL;
    protected $_driver = NULL;

    public function setMethod($method)
    {
        if (empty($method)) {
            throw new Zend_Controller_Action_Exception('empty "method"');
        }
        $this->_method = $method;
        $this->_driver = $this->_getDriverByMethod($method);
        return $this;
    }

    public function getPrepareForm(Model_Object_Interface $shipment)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if (empty($this->_method)) {
            throw new Zend_Controller_Action_Exception('method should be selected for preparation form ('.__CLASS__.'::'.__FUNCTION__.')');
        }
        $form = $this->_driver->getPrepareForm($shipment);
        return $form;
    }

    public function getSelectForm(Model_Object_Interface $shipment = NULL)
    {
        if ($shipment AND ($this->_method != $shipment->method)) {
            $this->setMethod($shipment->method);
        }
        $form = $this->getActionController()->getInjector()->getObject('Form_Shipment');
        $methods = array();
        $availShips = Model_Service::factory('checkout/order')->getAvailableShipments();
        foreach ($availShips as $ship) {
            $methods[$ship->method] = $ship->title;
        }
        $form->method->addMultiOptions($methods);
        if ($shipment AND $shipment->method) {
            $form->method->setValue($shipment->method);
        }
        return $form;
    }

    public function extendQuickForm(Zend_Form $form, Model_Object_Interface $shipment)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->extendQuickForm($form, $shipment);
        }
    }

    public function getQuickFormFields(Model_Object_Interface $shipment)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if ( ! empty($this->_method)) {
            $result = $this->_driver->getQuickFormFields($shipment);
        }
        else {
            $result = array();
        }
        return $result;
    }

    public function extendAdminEditForm(Zend_Form $form, Model_Object_Interface $shipment)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->extendAdminEditForm($form, $shipment);
        }
    }

    public function renderQuickSubform(Zend_Form $form, Model_Object_Interface $shipment)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->renderQuickSubform($form, $shipment);
        }
    }

    public function renderPrepareForm(Zend_Form $form, Model_Object_Interface $shipment)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->renderPrepareForm($form, $shipment);
        }
    }


    public function renderScreenInfo(Model_Object_Interface $shipment)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->renderScreenInfo($shipment);
        }
    }


    public function renderPrintInfo(Model_Object_Interface $shipment)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->renderPrintInfo($shipment);
        }
    }

    public function prepareAjaxAction(Model_Object_Interface $shipment)
    {
        if ($this->_method != $shipment->method) {
            $this->setMethod($shipment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->prepareAjaxAction($shipment);
        }
    }
    
    
    protected function _getDriverByMethod($method)
    {
        $iface = 'Controller_Action_Helper_Shipment_'.$this->_filterDashToCamelCase($method);
        $injector = $this->getActionController()->getHelper('Injector')->getInjector();
        if ($injector->hasInjection($iface)) {
            $obj = $injector->getObject($iface, $this);
        }
        else if (($configHelperClass = Model_Service::factory('checkout/brule')->getAll(Checkout_Model_Service_Brule::TYPE_SHIPMENT)->get($method)->classController)
                 AND ( ! empty($configHelperClass))) {
            $injector->inject($iface, $configHelperClass);
            $obj = $injector->getObject($iface, $this);
        }
        else if (($ifaceCheckout = 'Checkout_'.$iface) AND ($injector->hasInjection($ifaceCheckout))) {
            $obj = $injector->getObject($ifaceCheckout, $this);
        }
        else {
            if (empty($method)) {
                debug_print_backtrace();
                var_dump(Model_Service::factory('checkout/order')->getShipment());
                exit;
            }
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