<?php

class Shop_Controller_Action_Helper_Shipment_Courier extends Checkout_Controller_Action_Helper_Shipment_Abstract
{

    protected $_defaultInjections = array(
        'Form_Prepare' => 'Shop_Form_Shipment_CourierPrepare',
        'Form_Quick' => 'Shop_Form_Shipment_CourierPrepare',
    	'Form_AdminEdit' => 'Shop_Form_Shipment_CourierAdminEdit',
    );

    protected $_screenInfoViewScript = 'order/shipment/courier/screen-info.phtml';
    protected $_printInfoViewScript = 'order/shipment/courier/print-info.phtml';
    protected $_prepareFormViewScript = 'order/shipment/courier/form-prepare.phtml';
    protected $_miniFormViewScript = 'order/shipment/courier/mini-form.phtml';
    
    
    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::getPrepareForm()
     */
    public function getPrepareForm(Model_Object_Interface $shipment)
    {
        $form = $this->getInjector()->getObject('Form_Prepare');
        /*if (@$shipment->params['ship_to'] == Checkout_Form_Shipment_TransportPrepare::SHIPMENT_TO_CLIENT) {
            $form->params__ship_address->setRequired(TRUE);
        }*/
        return $form;
    }
    
    public function getQuickFormFields(Model_Object_Interface $shipment)
    {
        $form = $this->getInjector()->getObject('Form_Quick');
        $fields = array();
        $arrayFields = array('client_requisites', 'params', 'seller_requisites');
        foreach ($form->getElements() as $el) {
            $name = $el->getName();
            if ($shipment->hasElement($name)) {
                $fields[$name] = $shipment->{$name};
            }
            else {
                foreach ($arrayFields as $arrayField) {
                    $prefLen = strlen($arrayField)+2;
                    $prefName = substr($name, 0, $prefLen);
                    if (($prefName) == $arrayField.'__') {
                        $subName = substr($name, $prefLen);
                        if (is_array($shipment->{$arrayField}) AND array_key_exists($subName, $shipment->{$arrayField})) {
                            $fields[$name] = $shipment->{$arrayField}[$subName];
                        }
                    }
                }
            }
        }
        return $fields;
    }
    
    
    

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::renderPrepareForm()
     */
    public function renderPrepareForm(Zend_Form $form, Model_Object_Interface $shipment)
    {
        $this->_controller->view->shipment = $shipment;
        $xhtml = $this->_controller->view->renderForm($form, 'order/shipment/courier/form-prepare.phtml', array('shipment'=>$shipment));
        return $xhtml;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Abstract::prepareAjaxAction()
     */
    public function prepareAjaxAction(Model_Object_Interface $shipment)
    {
        $params = $this->_controller->getRequest()->getParams();
        if ($params['act'] == 'get-mini-form') {
            $this->_controller->view->miniForm = $this->getPrepareForm($shipment);
            $result = $this->_controller->view->render($this->_miniFormViewScript);
        }
        else {
            $result = FALSE;
        }
        return $result;
    }

}