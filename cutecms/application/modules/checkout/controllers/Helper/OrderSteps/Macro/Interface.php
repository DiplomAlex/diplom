<?php

interface Checkout_Controller_Action_Helper_OrderSteps_Macro_Interface
{

    /**
     * runs the macro
     * @param Model_Service_Helper_Interface $paymentHelper
     * @param Model_Service_Helper_Interface $shipmentHelper
     * @param Model_Object_Interface $order
     * @param array $params
     */
    public function run(Model_Service_Helper_Interface $paymentHelper, Model_Service_Helper_Interface $shipmentHelper, Model_Object_Interface $order, array $params = NULL);

}