<?php

class Checkout_Controller_Action_Helper_OrderSteps_Abstract implements Checkout_Controller_Action_Helper_OrderSteps_Interface
{

    protected $_controller = NULL;

    public function __construct(Zend_Controller_Action $controller)
    {
        $this->_controller = $controller;
    }

    public function walk(array $params = NULL)
    {

    }

    protected function _getMacroByCode($code)
    {
        $iface = 'Controller_Action_Helper_OrderSteps_Macro_'.$this->_filterDashToCamelCase($code);
        $injector = $this->_controller->getHelper('Injector')->getInjector();
        if ($injector->hasInjection($iface)) {
            $obj = $injector->getObject($iface, $this->_controller);
        }
        else if (($ifaceCheckout = 'Checkout_'.$iface) AND ($injector->hasInjection($ifaceCheckout))) {
            $obj = $injector->getObject($ifaceCheckout, $this->_controller);
        }
        else {
            $obj = new $ifaceCheckout($this->_controller);
        }
        return $obj;
    }

    protected function _filterDashToCamelCase($str)
    {
        $flt = new Zend_Filter_Word_DashToCamelCase();
        $result = $flt->filter($str);
        return $result;
    }

    protected function _getOrderService()
    {
        return Model_Service::factory('checkout/order');
    }

}