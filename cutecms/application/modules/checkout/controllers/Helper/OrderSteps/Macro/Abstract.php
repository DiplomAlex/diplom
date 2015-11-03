<?php

class Checkout_Controller_Action_Helper_OrderSteps_Macro_Abstract implements Checkout_Controller_Action_Helper_OrderSteps_Macro_Interface
{

    protected $_controller = NULL;

    public function run(Model_Service_Helper_Interface $paymentHelper, Model_Service_Helper_Interface $shipmentHelper, Model_Object_Interface $order, array $params = NULL)
    {

    }

    public function __construct(Zend_Controller_Action $controller)
    {
        $this->_controller = $controller;
    }

}