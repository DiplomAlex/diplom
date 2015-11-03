<?php

class Checkout_Model_Object_Payment extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'method',
            'status',
            'active',
            'is_prepaid', 'is_online_paid',
            'title', 'description',
            'date_added', 'date_changed', 'adder_id', 'changer_id',
            'client_name', 'client_address', 'client_id', 'client_login', 'client_email', 'client_requisites',
            'seller_name', 'seller_address', 'seller_id', 'seller_login', 'seller_email', 'seller_requisites',
            'summ',
            'params', /* array */
            'allowed_shipments', /* array */
        ));
    }

    public function isPayed()
    {
        return ((int) $this->_elements['status'] >= (int) $this->_getStatuses()->payed);
    }

    public function isPrepared()
    {
        return ((int) $this->_elements['status'] >= (int) $this->_getStatuses()->prepared);
    }

    public function isPrepaid()
    {
        return ((bool) $this->_elements['is_prepaid']);
    }

    public function isOnlinePaid()
    {
        return ((bool) $this->_elements['is_online_paid']);
    }
        
    protected function _getStatuses()
    {
        return Zend_Registry::get('checkout_config')->paymentStatus;
    }

}