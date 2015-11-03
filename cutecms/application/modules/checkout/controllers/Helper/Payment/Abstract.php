<?php

class Checkout_Controller_Action_Helper_Payment_Abstract implements Checkout_Controller_Action_Helper_Payment_Interface
{



    protected $_screenInfoViewScript = 'order/payment/screen-info.phtml';
    protected $_printInfoViewScript = 'order/payment/print-info.phtml';


    /**
     * @var Zend_Controller_Action_Helper_Abstract
     */
    protected $_helper = NULL;

    /**
     * @var Zend_Controller_Action
     */
    protected $_controller = NULL;

    /**
     * @var array
     */
    protected $_defaultInjections = array(
        'Form_Prepare',
        'Form_Quick',
    	'Form_AdminEdit',
    );

    /**
     * @var App_DIContainer
     */
    protected $_injector = NULL;

    /**
     * @return App_DIContainer
     */
    public function getInjector()
    {
        if ($this->_injector === NULL) {
            $this->_injector = new App_DIContainer();
            foreach ($this->_defaultInjections as $iface => $class) {
                $this->_injector->inject($iface, $class);
            }
        }
        return $this->_injector;
    }

    /**
     * @param Zend_Controller_Action_Helper_Abstract $helper
     */
    public function __construct(Zend_Controller_Action_Helper_Abstract $helper)
    {
        $this->_helper = $helper;
        $this->_controller = $helper->getActionController();
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Payment_Interface::getPrepareForm()
     */
    public function getPrepareForm(Model_Object_Interface $payment)
    {
        $form = $this->getInjector()->getObject('Form_Prepare');
        return $form;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Payment_Interface::getQuickFormFields()
     */
    public function getQuickFormFields(Model_Object_Interface $payment)
    {
        $form = $this->getInjector()->getObject('Form_Quick');
        $fields = array();
        foreach ($form->getElements() as $el) {
            $fields[$el->name] = @$payment->client_requisites[$el->name];
        }
        return $fields;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Payment_Interface::extendQuickForm()
     */
    public function extendQuickForm(Zend_Form $form, Model_Object_Interface $payment)
    {
        $subform = $this->getInjector()->getObject('Form_Quick');
        foreach ($subform->getElements() as $el) {
            $form->addElement($el);
        }
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Payment_Interface::extendAdminForm()
     */
    public function extendAdminEditForm(Zend_Form $form, Model_Object_Interface $payment)
    {
        if ($this->getInjector()->hasInjection('Form_AdminEdit')) {
            $subform = $this->getInjector()->getObject('Form_AdminEdit');
            foreach ($subform->getElements() as $el) {
                $form->addElement($el);
            }
        }
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Payment_Interface::process()
     */
    public function process(Model_Object_Interface $payment)
    {
        $this->_getOrderService()->getHelper('BrulePayment')->process($payment, $this->_getOrderService()->getCurrent());
        $result = FALSE;
        return $result;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Payment_Interface::renderQuickSubform()
     */
    public function renderQuickSubform(Zend_Form $form, Model_Object_Interface $payment)
    {
        return FALSE;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Payment_Interface::renderPrepareForm()
     */
    public function renderPrepareForm(Zend_Form $form, Model_Object_Interface $payment)
    {
        $this->_controller->view->payment = $payment;
        $xhtml = $this->_controller->view->renderForm($form, NULL, array('payment'=>$payment));
        return $xhtml;
    }


    protected function _getOrderService()
    {
        return Model_Service::factory('checkout/order');
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Payment_Interface::renderScreenInfo()
     */
    public function renderScreenInfo(Model_Object_Interface $payment)
    {
        $this->_controller->view->payment = $payment;
        $xhtml = $this->_controller->view->render($this->_screenInfoViewScript);
        return $xhtml;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Payment_Interface::renderPrintInfo()
     */
    public function renderPrintInfo(Model_Object_Interface $payment)
    {
        $this->_controller->view->payment = $payment;
        $xhtml = $this->_controller->view->render($this->_printInfoViewScript);
        return $xhtml;
    }
    
    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Payment_Interface::prepareAjaxAction()
     */
    public function prepareAjaxAction(Model_Object_Interface $payment)
    {
        return FALSE;
    }
          


}