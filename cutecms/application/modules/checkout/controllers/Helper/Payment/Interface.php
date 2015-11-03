<?php

interface Checkout_Controller_Action_Helper_Payment_Interface
{

    /**
     * get form for payment requisites
     * @param Model_Object_Interface $payment
     * @return Zend_Form
     */
    public function getPrepareForm(Model_Object_Interface $payment);

    /**
     * get populated array of fields for quick form
     * @param Model_Object_Interface $payment
     * @return array (field => value, ...)
     */
    public function getQuickFormFields(Model_Object_Interface $payment);

    /**
     * add fields for payment requisites to quick form
     * @param Zend_Form $form
     * @param Model_Object_Interface $payment
     * @return Checkout_Controller_Action_Helper_Payment_Interface
     */
    public function extendQuickForm(Zend_Form $form, Model_Object_Interface $payment);

    /**
     * add fields for payment requisites to admin edit form
     * @param Zend_Form $form
     * @param Model_Object_Interface $payment
     * @return Checkout_Controller_Action_Helper_Payment_Interface
     */
    public function extendAdminEditForm(Zend_Form $form, Model_Object_Interface $shipment);

    /**
     * process payment with gateway or any other way
     * @param Model_Object_Interface $payment
     * @return string|FALSE
     */
    public function process(Model_Object_Interface $payment);

    /**
     * renders part of quick form
     * @param Zend_Form $form
     * @param Model_Object_Interface $payment
     * @return Checkout_Controller_Action_Helper_Payment_Interface
     */
    public function renderQuickSubform(Zend_Form $form, Model_Object_Interface $payment);

    /**
     * renders preparation form
     * @param Zend_Form $form
     * @param Model_Object_Interface $payment
     * @return Checkout_Controller_Action_Helper_Payment_Interface
     */
    public function renderPrepareForm(Zend_Form $form, Model_Object_Interface $payment);

    /**
     * renders info block for browser
     * @param Zend_Form $form
     * @param Model_Object_Interface $shipment
     * @return Checkout_Controller_Action_Helper_Payment_Interface
     */
    public function renderScreenInfo(Model_Object_Interface $payment);

    /**
     * renders info block for printing blank
     * @param Zend_Form $form
     * @param Model_Object_Interface $shipment
     * @return Checkout_Controller_Action_Helper_Payment_Interface
     */
    public function renderPrintInfo(Model_Object_Interface $payment);

    /**
     * runs needed actions called with ajax by preparation form and returns raw response
     * @param Model_Object_Interface $payment
     * @return string | FALSE
     */
    public function prepareAjaxAction(Model_Object_Interface $payment);
        
}