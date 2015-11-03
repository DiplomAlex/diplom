<?php

class Checkout_Controller_Action_Helper_Shipment_Transport extends Checkout_Controller_Action_Helper_Shipment_Abstract
{

    protected $_defaultInjections = array(
        'Form_Prepare' => 'Checkout_Form_Shipment_TransportPrepare',
        'Form_Quick' => 'Checkout_Form_Shipment_TransportPrepare',
    	'Form_AdminEdit' => 'Checkout_Form_Shipment_TransportAdminEdit',
    );

    protected $_screenInfoViewScript = 'order/shipment/transport/screen-info.phtml';
    protected $_printInfoViewScript = 'order/shipment/transport/print-info.phtml';
    protected $_prepareFormViewScript = 'order/shipment/transport/form-prepare.phtml';
    
    
    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::getPrepareForm()
     */
    public function getPrepareForm(Model_Object_Interface $shipment)
    {
        $form = $this->getInjector()->getObject('Form_Prepare');
        if (@$shipment->params['ship_to'] == Checkout_Form_Shipment_TransportPrepare::SHIPMENT_TO_CLIENT) {
            $form->params__ship_address->setRequired(TRUE);
        }
        return $form;
    }
    
    

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Interface::renderPrepareForm()
     */
    public function renderPrepareForm(Zend_Form $form, Model_Object_Interface $shipment)
    {
        $this->_controller->view->shipment = $shipment;
        $xhtml = $this->_controller->view->renderForm($form, 'order/shipment/transport/form-prepare.phtml', array('shipment'=>$shipment));
        return $xhtml;
    }

    /**
     * (non-PHPdoc)
     * @see Checkout_Controller_Action_Helper_Shipment_Abstract::prepareAjaxAction()
     */
    public function prepareAjaxAction(Model_Object_Interface $shipment)
    {
        $params = $this->_controller->getRequest()->getParams();
        if ($params['act'] == 'get-companies') {
            $list = App_Event::factory('Checkout_Model_Service_Helper_Brule_Shipment_Transport__getCompaniesList', array($params['city']))->dispatch()->getResponse();
            if (empty($list)) {
                $list = array();
            }
            $result = Zend_Json::encode($list);
        }
        else {
            $result = FALSE;
        }
        return $result;
    }

}