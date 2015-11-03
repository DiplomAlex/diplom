<?php

class Checkout_Controller_Action_Helper_OrderSteps extends Zend_Controller_Action_Helper_Abstract
{

    public function walk($driverCode, array $params = NULL)
    {
        $this->_getDriverByCode($driverCode)->walk($params);
    }

    protected function _getDriverByCode($code)
    {
        $iface = 'Controller_Action_Helper_OrderSteps_'.$this->_filterDashToCamelCase($code);
        $injector = $this->getActionController()->getHelper('Injector')->getInjector();
        if ($injector->hasInjection($iface)) {
            $obj = $injector->getObject($iface, $this->getActionController());
        }
        else if (($ifaceCheckout = 'Checkout_'.$iface) AND ($injector->hasInjection($ifaceCheckout))) {
            $obj = $injector->getObject($ifaceCheckout, $this->getActionController());
        }
        else {
            $obj = new $ifaceCheckout($this->getActionController());
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