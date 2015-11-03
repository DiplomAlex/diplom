<?php

class Checkout_Model_Service_Helper_Brule_Shipment_Abstract implements Checkout_Model_Service_Helper_Brule_Shipment_Interface
{

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Shipment_Interface::onAfterSelect()
     */
    public function onAfterSelect(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {
        $shipment->status = $this->_getStatuses()->created;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Shipment_Interface::onBeforePrepare()
     */
    public function onBeforePrepare(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {

    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Shipment_Interface::onPrepare()
     */
    public function onAfterPrepare(Model_Object_Interface $shipment, Model_Object_Interface $order, array $values)
    {
        foreach ($values as $key=>$value) {
            if ($shipment->hasElement($key)) {
                $shipment->{$key} = $value;
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Shipment_Interface::isPrepared()
     */
    public function isPrepared(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {
        $result = TRUE;
        if ($result AND ($shipment->status < $this->_getStatuses()->prepared)) {
            $shipment->status = $this->_getStatuses()->prepared;
        }
        else if ($shipment->status > $this->_getStatuses()->created) {
            $shipment->status = $this->_getStatuses()->created;
        }
        return $result;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Shipment_Interface::onRecalculate()
     */
    public function onRecalculate(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {

    }


    /**
     * @return Zend_Config
     */
    protected function _getStatuses()
    {
        return Zend_Registry::get('checkout_config')->shipmentStatus;
    }
}