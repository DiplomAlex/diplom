<?php

interface Checkout_Controller_Action_Helper_Shipment_Interface
{

    /**
     * get form for shipment requisites
     * @param Model_Object_Interface $shipment
     * @return Zend_Form
     */
    public function getPrepareForm(Model_Object_Interface $shipment);

    /**
     * get populated array of fields for quick form
     * @param Model_Object_Interface $shipment
     * @return array (field => value, ...)
     */
    public function getQuickFormFields(Model_Object_Interface $shipment);

    /**
     * add fields for shipment requisites to quick form
     * @param Zend_Form $form
     * @param Model_Object_Interface $shipment
     * @return Checkout_Controller_Action_Helper_Shipment_Interface
     */
    public function extendQuickForm(Zend_Form $form, Model_Object_Interface $shipment);

    /**
     * add fields for shipment requisites to admin edit form
     * @param Zend_Form $form
     * @param Model_Object_Interface $shipment
     * @return Checkout_Controller_Action_Helper_Shipment_Interface
     */
    public function extendAdminEditForm(Zend_Form $form, Model_Object_Interface $shipment);

    /**
     * renders part of quick form
     * @param Zend_Form $form
     * @param Model_Object_Interface $shipment
     * @return Checkout_Controller_Action_Helper_Shipment_Interface
     */
    public function renderQuickSubform(Zend_Form $form, Model_Object_Interface $shipment);

    /**
     * renders preparation form
     * @param Zend_Form $form
     * @param Model_Object_Interface $shipment
     * @return Checkout_Controller_Action_Helper_Shipment_Interface
     */
    public function renderPrepareForm(Zend_Form $form, Model_Object_Interface $shipment);

    /**
     * renders info block for browser
     * @param Model_Object_Interface $shipment
     * @return Checkout_Controller_Action_Helper_Shipment_Interface
     */
    public function renderScreenInfo(Model_Object_Interface $shipment);

    /**
     * renders info block for printing blank
     * @param Model_Object_Interface $shipment
     * @return Checkout_Controller_Action_Helper_Shipment_Interface
     */
    public function renderPrintInfo(Model_Object_Interface $shipment);

    /**
     * runs needed actions called with ajax by preparation form and returns raw response
     * @param Model_Object_Interface $shipment
     * @return string | FALSE
     */
    public function prepareAjaxAction(Model_Object_Interface $shipment);
    
}
