<?php

class Checkout_Controller_Action_Helper_Payment extends Zend_Controller_Action_Helper_Abstract
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

    public function getPrepareForm(Model_Object_Interface $payment)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if (empty($this->_method)) {
            throw new Zend_Controller_Action_Exception('method should be selected for preparation form ('.__CLASS__.'::'.__FUNCTION__.')');
        }
        $form = $this->_driver->getPrepareForm($payment);
        return $form;
    }

    public function getSelectForm(Model_Object_Interface $payment = NULL)
    {
        if ($payment AND ($this->_method != $payment->method)) {
            $this->setMethod($payment->method);
        }
        $form = $this->getActionController()->getInjector()->getObject('Form_Payment');
        $methods = array();
        $availPays = Model_Service::factory('checkout/order')->getAvailablePayments();
        foreach ($availPays as $pay) {
            $methods[$pay->method] = $pay->title;
        }
        $form->method->addMultiOptions($methods);
        if ($payment AND $payment->method) {
            $form->method->setValue($payment->method);
        }
        return $form;

    }

    public function process(Model_Object_Interface $payment)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if (empty($this->_method)) {
            throw new Zend_Controller_Action_Exception('method should be selected for processing ('.__CLASS__.'::'.__FUNCTION__.')');
        }
        $result = $this->_driver->process($payment);
        return $result;
    }

    public function extendQuickForm(Zend_Form $form, Model_Object_Interface $payment)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->extendQuickForm($form, $payment);
        }
    }

    public function getQuickFormFields(Model_Object_Interface $payment)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if ( ! empty($this->_method)) {
            $result = $this->_driver->getQuickFormFields($payment);
        }
        else {
            $result = array();
        }
        return $result;
    }

    public function extendAdminEditForm(Zend_Form $form, Model_Object_Interface $payment)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->extendAdminEditForm($form, $payment);
        }
    }

    public function renderQuickSubform(Zend_Form $form, Model_Object_Interface $payment)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->renderQuickSubform($form, $payment);
        }
    }

    public function renderPrepareForm(Zend_Form $form, Model_Object_Interface $payment)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->renderPrepareForm($form, $payment);
        }
    }


    public function renderScreenInfo(Model_Object_Interface $payment)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->renderScreenInfo($payment);
        }
    }


    public function renderPrintInfo(Model_Object_Interface $payment)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->renderPrintInfo($payment);
        }
    }

    public function prepareAjaxAction(Model_Object_Interface $payment)
    {
        if ($this->_method != $payment->method) {
            $this->setMethod($payment->method);
        }
        if ( ! empty($this->_method)) {
            return $this->_driver->prepareAjaxAction($payment);
        }
    }
        


    protected function _getDriverByMethod($method)
    {  
        $iface = 'Controller_Action_Helper_Payment_'.$this->_filterDashToCamelCase($method);
        $injector = $this->getActionController()->getHelper('Injector')->getInjector();
        if ($injector->hasInjection($iface)) {
            $obj = $injector->getObject($iface, $this);
        }
        else if (($configHelperClass = Model_Service::factory('checkout/brule')->getAll(Checkout_Model_Service_Brule::TYPE_PAYMENT)->get($method)->classController)
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
                var_dump(Model_Service::factory('checkout/order')->getPayment());
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