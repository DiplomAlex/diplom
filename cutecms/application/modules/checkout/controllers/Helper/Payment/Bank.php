<?php

class Checkout_Controller_Action_Helper_Payment_Bank extends Checkout_Controller_Action_Helper_Payment_Abstract
{

    protected $_screenInfoViewScript = 'order/payment/bank/screen-info.phtml';
    protected $_printInfoViewScript = 'order/payment/bank/print-info.phtml';


    protected $_defaultInjections = array(
        'Form_Prepare' => 'Checkout_Form_Payment_BankPrepare',
        'Form_Quick' => 'Checkout_Form_Payment_BankPrepare',
    	'Form_AdminEdit' => 'Checkout_Form_Payment_BankAdminEdit',
    );

    public function process(Model_Object_Interface $payment)
    {
        return FALSE;
    }

}