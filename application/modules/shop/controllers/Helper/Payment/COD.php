<?php

class Shop_Controller_Action_Helper_Payment_COD extends Checkout_Controller_Action_Helper_Payment_Abstract
{

    protected $_screenInfoViewScript = 'order/payment/c-o-d/screen-info.phtml';
    protected $_printInfoViewScript = 'order/payment/c-o-d/print-info.phtml';


    protected $_defaultInjections = array(
        'Form_Prepare' => 'Shop_Form_Payment_CODPrepare',
        'Form_Quick' => 'Shop_Form_Payment_CODPrepare',
    	'Form_AdminEdit' => 'Shop_Form_Payment_CODAdminEdit',
    );

    public function process(Model_Object_Interface $payment)
    {
        return FALSE;
    }

}