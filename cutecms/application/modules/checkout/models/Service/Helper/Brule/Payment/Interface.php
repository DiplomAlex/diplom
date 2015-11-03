<?php

interface Checkout_Model_Service_Helper_Brule_Payment_Interface
{

    /**
     * executes after payment was selected by user
     * @param Model_Object_Interface $shipment
     * @param Model_Object_Interface $order
     * @param array $values
     */
    public function onAfterSelect(Model_Object_Interface $payment, Model_Object_Interface $order);


    /**
     * executes before user inputs payment requisites
     * @param Model_Object_Interface $payment
     * @param Model_Object_Interface $order
     * @param array $values
     */
    public function onBeforePrepare(Model_Object_Interface $payment, Model_Object_Interface $order);

    /**
     * executes after user inputs payment requisites
     * @param Model_Object_Interface $payment
     * @param Model_Object_Interface $order
     * @param array $values
     */
    public function onAfterPrepare(Model_Object_Interface $payment, Model_Object_Interface $order, array $values);

    /**
     * checks is module is fully prepared for processing
     * @param Model_Object_Interface $shipment
     * @param Model_Object_Interface $order
     * @return bool
     */
    public function isPrepared(Model_Object_Interface $shipment, Model_Object_Interface $order);

    /**
     * executes when order content changes and total summ is recalculating
     * @param Model_Object_Interface $payment
     * @param Model_Object_Intreface $order
     */
    public function onRecalculate(Model_Object_Interface $payment, Model_Object_Interface $order);

    /**
     * executes when payment is processing
     * @param Model_Object_Interface $payment
     * @param Model_Object_Intreface $order
     */
    public function onProcess(Model_Object_Interface $payment, Model_Object_Interface $order);

}