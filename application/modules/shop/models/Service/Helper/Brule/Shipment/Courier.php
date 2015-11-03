<?php

class Shop_Model_Service_Helper_Brule_Shipment_Courier extends Checkout_Model_Service_Helper_Brule_Shipment_Abstract
{
    
    const MIN_SUMM = 0;

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Shipment_Abstract::onAfterSelect()
     */
    public function onAfterSelect(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {
        if (is_null($shipment->status)) {
            $shipment->status = $this->_getStatuses()->created;
        }
        $empty = TRUE;
        foreach ($shipment->client_requisites as $val) {
            if ( ! empty($val)) {
                $empty = FALSE;
                break;
            }
        }
        if ($empty) {
            $user = Model_Service::factory('user')->getCurrent();
            $shipment->client_requisites = array(
                'id' => $user->id,
                'name' => $user->name,
                'address' => $user->address,
                'phone' => $user->tel,
            );
            $order->client_id = $user->id;
            $order->client_name = $user->name;
            $order->client_login = $user->login;
            $order->client_email = $user->email;
        }
        else {
            $order->client_id = $shipment->client_requisites['id'];
            $order->client_name = $shipment->client_requisites['name'];
            if ($order->client_id) {
                $user = Model_Service::factory('user')->getComplex($order->client_id);
                $order->client_login = $user->login;
                $order->client_email = $user->email;
            }
        }
        if ( (float) $shipment->summ == 0) {
            $shipment->summ = self::MIN_SUMM;
        }
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
     * @see Checkout_Model_Service_Helper_Brule_Shipment_Abstract::isPrepared()
     */
    public function isPrepared(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {
        if ($shipment->status == $this->_getStatuses()->prepared) {
            $result = TRUE;
        }
        else {
            $sellerRqs = array(/*'name', 'address', 'phone'*/);
            $clientRqs = array('name', 'address', 'phone', 'email');
            $params    = array();
            $result = TRUE;
            foreach ($sellerRqs as $rq) {
                if (empty($shipment->seller_requisites[$rq])) {
                    $result = FALSE;
                    break;
                }
            }
            foreach ($clientRqs as $rq) {
                if (empty($shipment->client_requisites[$rq])) {
                    $result = FALSE;
                    break;
                }
            }
            foreach ($params as $p) {
                if (empty($shipment->params[$p])) {
                    $result = FALSE;
                    break;
                }
            }
        }
        return $result;
    }


}