<?php

class Checkout_Model_Service_Helper_Brule_Payment_Abstract implements Checkout_Model_Service_Helper_Brule_Payment_Interface
{

    /**
     * @var Model_Service_Helper_Abstract
     */
    protected $_helper = NULL;

    /**
     * @param Model_Service_Helper_Abstract $helper
     */
    public function __construct(Model_Service_Helper_Abstract $helper)
    {
        $this->_helper = $helper;
    }


    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Payment_Interface::onAfterSelect()
     */
    public function onAfterSelect(Model_Object_Interface $payment, Model_Object_Interface $order)
    {
        $payment->status = $this->_getStatuses()->created;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Payment_Interface::onBeforePrepare()
     */
    public function onBeforePrepare(Model_Object_Interface $payment, Model_Object_Interface $order)
    {

    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Payment_Interface::onAfterPrepare()
     */
    public function onAfterPrepare(Model_Object_Interface $payment, Model_Object_Interface $order, array $values)
    {
        foreach ($values as $key=>$value) {
            if ($payment->hasElement($key)) {
                $payment->{$key} = $value;
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Payment_Interface::isPrepared()
     */
    public function isPrepared(Model_Object_Interface $payment, Model_Object_Interface $order)
    {
        $result = TRUE;
        return $result;
    }


    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Payment_Interface::onRecalculate()
     */
    public function onRecalculate(Model_Object_Interface $payment, Model_Object_Interface $order)
    {

    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Model_Service_Helper_Brule_Payment_Interface::onProcess()
     */
    public function onProcess(Model_Object_Interface $payment, Model_Object_Interface $order)
    {

    }

    /**
     * @return Zend_Config
     */
    protected function _getStatuses()
    {
        return Zend_Registry::get('checkout_config')->paymentStatus;
    }

}