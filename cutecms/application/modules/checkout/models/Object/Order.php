<?php

class Checkout_Model_Object_Order extends Model_Object_Abstract
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
            'client_id', 'client_login', 'client_name', 'client_email', 'client_comment',
            'total',
            'items', 'items_xml',
            'brules', 'brules_xml',
            'shipment', 'shipment_xml',
            'payment', 'payment_xml',
            'is_confirmed',
            'currency',
            'site_id',
            'send_mail_to_client',
            'guid',
            'export'
        ));
    }

/*****************items***************/
    public function getItems()
    {
        /** @var $service Checkout_Model_Service_Order */
        $service = Model_Service::factory('checkout/order');
        $val = $this->_elements['items'];
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        } else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Item');
        } else {
            $newVal = $service->parseItemsFromXML($val);
        }
        $this->_elements['items'] = $newVal;
       return $this->_elements['items'];
    }


    public function setItems($val)
    {
        /** @var $service Checkout_Model_Service_Order */
        $service = Model_Service::factory('checkout/order');
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        } else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Item');
        } else {
            $newVal = $service->parseItemsFromXML($val);
            /*App_Debug::dump($newVal[0]['attributes']);*/
        }
        $this->_elements['items'] = $newVal;
       return $this;
    }

/********brules**********************/

    public function getBrules()
    {
        $service = Model_Service::factory('checkout/order');
        $val = $this->_elements['brules'];
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        } else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Brule');
        } else {
            $newVal = $service->parseBrulesFromXML($val);
        }
        $this->_elements['brules'] = $newVal;
       return $this->_elements['brules'];
    }


    public function setBrules($val)
    {
        $service = Model_Service::factory('checkout/order');
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        } else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Collection_Brule');
        } else {
            $newVal = $service->parseBrulesFromXML($val);
        }
        $this->_elements['brules'] = $newVal;
       return $this;
    }

/*******shipment*****************/

    public function getShipment()
    {
        $service = Model_Service::factory('checkout/order');
        $val = $this->_elements['shipment'];
        if ($val instanceof Model_Object_Interface) {
            $newVal = $val;
        } else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Object_Shipment');
        } else {
            $newVal = $service->parseShipmentFromXML($val);
        }
        $this->_elements['shipment'] = $newVal;
       return $this->_elements['shipment'];
    }


    public function setShipment($val)
    {
        $service = Model_Service::factory('checkout/order');
        if ($val instanceof Model_Object_Interface) {
            $newVal = $val;
        } else {
            if (empty($val)) {
                $newVal = $this->getInjector()->getObject('Model_Object_Shipment');
            } else {
                $newVal = $service->parseShipmentFromXML($val);
            }
        }
        $this->_elements['shipment'] = $newVal;
        return $this;
    }

/**************payment*********************/

    public function getPayment()
    {
        /** @var $service Checkout_Model_Service_Order */
        $service = Model_Service::factory('checkout/order');
        $val = $this->_elements['payment'];
        if ($val instanceof Model_Object_Interface) {
            $newVal = $val;
        } else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Object_Payment');
        } else {
            $newVal = $service->parsePaymentFromXML($val);
        }
        $this->_elements['payment'] = $newVal;
       return $this->_elements['payment'];
    }


    public function setPayment($val)
    {
        $service = Model_Service::factory('checkout/order');
        if ($val instanceof Model_Object_Interface) {
            $newVal = $val;
        } else if (empty($val)) {
            $newVal = $this->getInjector()->getObject('Model_Object_Payment');
        } else {
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

    public function getClient_name()
    {
        $name = $this->_elements['client_name'];
        if (empty($name)) {
            if (is_object($this->shipment)) {
                $name = $this->shipment->client_name;
                if (empty($name)) {
                    $name = @$this->shipment->client_name->client_requisites['name'];
                }
            } else if (is_object($this->payment)) {
                $name = $this->payment->client_name;
                if (empty($name)) {
                    $name = @$this->payment->client_name->client_requisites['name'];
                }
            }
        }

        return $name;
    }

    public function getClient_email()
    {
        $email = $this->_elements['client_email'];
        if (empty($email)) {
            if (is_object($this->shipment)) {
                $email = $this->shipment->client_email;
                if (empty($email)) {
                    $email = @$this->shipment->client_email->client_requisites['email'];
                }
            } else {
                if (is_object($this->payment)) {
                    $email = $this->payment->client_email;
                    if (empty($email)) {
                        $email = @$this->payment->client_email->client_requisites['email'];
                    }
                }
            }
        }

        return $email;
    }

    /**
     * Return or set user guid
     *
     * @return mixed
     * @throws Model_Service_Exception
     */
    public function getGuid()
    {
        /** @var $service Checkout_Model_Service_Order */
        $service = Model_Service::factory('checkout/order');
        if (null == $this->_elements['guid']) {
            $this->_elements['guid'] = App_Uuid::get();
            $service->getMapper()->setOrderGuid($this->_elements['guid'], $this->_elements['id']);
        }

        return $this->_elements['guid'];
    }

}