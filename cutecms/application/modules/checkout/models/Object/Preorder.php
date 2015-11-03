<?php

class Checkout_Model_Object_Preorder extends Model_Object_Abstract
{

    protected $_defaultInjections = array(
        'Model_Collection_Item'     => 'Checkout_Model_Collection_CartItem',
        'Model_Collection_Brule'    => 'Checkout_Model_Collection_Brule',
        'Model_Collection_Shipment' => 'Checkout_Model_Collection_Shipment',
        'Model_Collection_Payment'  => 'Checkout_Model_Collection_Payment',
        'Model_Object_Brule'        => 'Checkout_Model_Object_Brule',
        'Model_Object_Shipment'     => 'Checkout_Model_Object_Shipment',
        'Model_Object_Payment'      => 'Checkout_Model_Object_Payment',
    );

    public function init()
    {
        $this->addElements(array(
            'id',
            'status',
            'date_added', 'date_changed',
            'adder_id', 'changer_id',
            'client_id', 'client_login', 'client_name',
            'total',
            'items', 'items_xml',
            'brules', 'brules_xml',
            'shipment', 'shipment_xml',
            'payment', 'payment_xml',
            'is_confirmed',
            'currency',
            'site_id',
        ));
    }

/*****************items***************/
    public function getItems()
    {
        $service = Model_Service::factory('checkout/preorder');
        $val = $this->_elements['items'];
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Item');
        }
        else {
            $newVal = $service->parseItemsFromXML($val);
        }
        $this->_elements['items'] = $newVal;
       return $this->_elements['items'];
    }


    public function setItems($val)
    {
        $service = Model_Service::factory('checkout/preorder');
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Item');
        }
        else {
            $newVal = $service->parseItemsFromXML($val);
            /*App_Debug::dump($newVal[0]['attributes']);*/
        }
        $this->_elements['items'] = $newVal;
       return $this;
    }

/********brules**********************/

    public function getBrules()
    {
        $service = Model_Service::factory('checkout/preorder');
        $val = $this->_elements['brules'];
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Brule');
        }
        else {
            $newVal = $service->parseBrulesFromXML($val);
        }
        $this->_elements['brules'] = $newVal;
       return $this->_elements['brules'];
    }


    public function setBrules($val)
    {
        $service = Model_Service::factory('checkout/preorder');
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Brule');
        }
        else {
            $newVal = $service->parseBrulesFromXML($val);
        }
        $this->_elements['brules'] = $newVal;
       return $this;
    }

/*******shipment*****************/

    public function getShipment()
    {
        $service = Model_Service::factory('checkout/preorder');
        $val = $this->_elements['shipment'];
        if ($val instanceof Model_Object_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Object_Shipment');
        }
        else {
            $newVal = $service->parseShipmentFromXML($val);
        }
        $this->_elements['shipment'] = $newVal;
       return $this->_elements['shipment'];
    }


    public function setShipment($val)
    {
        $service = Model_Service::factory('checkout/preorder');
        if ($val instanceof Model_Object_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Object_Shipment');
        }
        else {
            $newVal = $service->parseShipmentFromXML($val);
        }
        $this->_elements['shipment'] = $newVal;
       return $this;
    }

/**************payment*********************/

    public function getPayment()
    {
        $service = Model_Service::factory('checkout/preorder');
        $val = $this->_elements['payment'];
        if ($val instanceof Model_Object_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Object_Payment');
        }
        else {
            $newVal = $service->parsePaymentFromXML($val);
        }
        $this->_elements['payment'] = $newVal;
       return $this->_elements['payment'];
    }


    public function setPayment($val)
    {
        $service = Model_Service::factory('checkout/preorder');
        if ($val instanceof Model_Object_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Object_Payment');
        }
        else {
            $newVal = $service->parsePaymentFromXML($val);
        }
        $this->_elements['payment'] = $newVal;
       return $this;
    }


    /************************************************************/
    public function isConfirmed()
    {
        return $this->is_confirmed;
    }

    public function isPayed()
    {
        return (bool) $this->payment->is_payed;
    }

}