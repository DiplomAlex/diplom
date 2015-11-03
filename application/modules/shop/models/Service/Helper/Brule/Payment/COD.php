<?php

class Shop_Model_Service_Helper_Brule_Payment_COD extends Checkout_Model_Service_Helper_Brule_Payment_Abstract
{

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Payment_Abstract::onAfterSelect()
     */
    public function onAfterSelect(Model_Object_Interface $payment, Model_Object_Interface $order)
    {
        $payment->is_prepaid = TRUE;
        $payment->is_online_paid = FALSE;
        $user = Model_Service::factory('user')->getCurrent();
        $payment->client_id = $user->id;
        $payment->client_login = $user->login;
        $payment->client_name = $user->name;
        $payment->client_email = $user->email;
        $payment->client_requisites = array(
            'id' => $user->id,
            'name' => $user->name,
            'address' => $user->address,
            'phone' => $user->tel,
        );
        $payment->status = $this->_getStatuses()->prepared;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Payment_Abstract::isPrepared()
     */
    public function isPrepared(Model_Object_Interface $payment, Model_Object_Interface $order)
    {
        if ($payment->status == $this->_getStatuses()->prepared) {
            $result = TRUE;
        }
        else {
            $sellerRqs = array('name',/* 'bank', 'inn', 'kpp', 'bik', 'rs', 'ks'*/);
            $clientRqs = array('name'/*, 'bank', 'inn', 'kpp'*/);
            $result = TRUE;
            foreach ($sellerRqs as $rq) {
                if (empty($payment->seller_requisites[$rq])) {
                    $result = FALSE;
                    break;
                }
            }
            foreach ($clientRqs as $rq) {
                if (empty($payment->client_requisites[$rq])) {
                    $result = FALSE;
                    break;
                }
            }
        }
        return $result;
    }

}