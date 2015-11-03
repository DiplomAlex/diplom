<?php

class Checkout_Model_Object_Shipment extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'method',
            'status',
            'active',
        	'title', 'description',
            'date_added', 'date_changed', 'adder_id', 'changer_id',
            'client_name', 'client_address', 'client_id', 'client_login', 'client_email', 'client_requisites',
            'seller_name', 'seller_address', 'seller_id', 'seller_login', 'seller_email', 'seller_requisites',
            'summ',
            'params', /* array of shipment params, i.e. array('weights'=>array('netto'=>, 'brutto'=>,),
            												  'sizes'=>array('height'=>, 'width'=>, 'depth'=>,)) */
            'allowed_payments', /* array of strings - payments methods */
        ));
    }

    public function isShipped()
    {
        return ((int) $this->_elements['status'] >= (int) $this->_getStatuses()->shipped);
    }

    public function isPrepared()
    {
        return ((int) $this->_elements['status'] >= (int) $this->_getStatuses()->prepared);
    }

    protected function _getStatuses()
    {
        return Zend_Registry::get('checkout_config')->shipmentStatus;
    }

}