<?php

class Checkout_Controller_Action_Helper_Shipment_Abstract implements Checkout_Controller_Action_Helper_Shipment_Interface
{

    protected $_screenInfoViewScript = 'order/shipment/screen-info.phtml';
    protected $_printInfoViewScript = 'order/shipment/print-info.phtml';

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
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::getPrepareForm()
     */
    public function getPrepareForm(Model_Object_Interface $shipment)
    {
        $form = $this->getInjector()->getObject('Form_Prepare');
        return $form;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::getQuickFormFields()
     */
    public function getQuickFormFields(Model_Object_Interface $shipment)
    {
        $form = $this->getInjector()->getObject('Form_Quick');
        $fields = array();
        foreach ($form->getElements() as $el) {
            $fields[$el->name] = @$shipment->client_requisites[$el->name];
        }
        return $fields;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::extendQuickForm()
     */
    public function extendQuickForm(Zend_Form $form, Model_Object_Interface $shipment)
    {
        $subform = $this->getInjector()->getObject('Form_Quick');
        foreach ($subform->getElements() as $el) {
            $form->addElement($el);
        }
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::extendAdminForm()
     */
    public function extendAdminEditForm(Zend_Form $form, Model_Object_Interface $shipment)
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
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::renderQuickSubform()
     */
    public function renderQuickSubform(Zend_Form $form, Model_Object_Interface $shipment)
    {
        return FALSE;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::renderPrepareForm()
     */
    public function renderPrepareForm(Zend_Form $form, Model_Object_Interface $shipment)
    {
        $this->_controller->view->shipment = $shipment;
        $xhtml = $this->_controller->view->renderForm($form, NULL, array('shipment'=>$shipment));
        return $xhtml;
    }



    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::renderScreenInfo()
     */
    public function renderScreenInfo(Model_Object_Interface $shipment)
    {
        $this->_controller->view->shipment = $shipment;
        $xhtml = $this->_controller->view->render($this->_screenInfoViewScript);
        return $xhtml;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::renderPrintInfo()
     */
    public function renderPrintInfo(Model_Object_Interface $shipment)
    {
        $this->_controller->view->shipment = $shipment;
        $xhtml = $this->_controller->view->render($this->_printInfoViewScript);
        return $xhtml;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::prepareAjaxAction()
     */
    public function prepareAjaxAction(Model_Object_Interface $shipment) 
    {
        return FALSE;
    }

}