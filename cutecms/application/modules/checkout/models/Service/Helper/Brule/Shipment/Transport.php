<?php

class Checkout_Model_Service_Helper_Brule_Shipment_Transport extends Checkout_Model_Service_Helper_Brule_Shipment_Abstract
{

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Shipment_Abstract::onAfterSelect()
     */
    public function onAfterSelect(Model_Object_Interface $shipment, Model_Object_Interface $order)
    {
        if (is_null($shipment->status)) {
            $shipment->status = $this->_getStatuses()->created;
        }
        if (empty($shipment->client_requisites['id'])) {
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
        if ($shipment->params['tr_company_id']) {
            $arr = $shipment->params;
            $arr['tr_company_name'] = Model_Service::factory('user')->get($shipment->params['tr_company_id'])->name;
            $shipment->params = $arr;
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
            $sellerRqs = array('name', 'address', 'inn', 'kpp', 'phone');
            $clientRqs = array('name'/*, 'address', 'inn', 'kpp', 'phone'*/);
            $params    = array('tr_company_id', 'tr_company_name', 'ship_to'/*, 'ship_address'*/);
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