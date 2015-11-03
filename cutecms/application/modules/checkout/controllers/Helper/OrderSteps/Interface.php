<?php

interface Checkout_Controller_Action_Helper_OrderSteps_Interface
{

    /**
     * walk through the steps
     * @param array $params
     */
    public function walk(array $params = NULL);

}