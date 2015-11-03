<?php

class Checkout_AdminBruleController extends Zend_Controller_Action
{

    protected $_defaultInjections = array(
        'Form_AdminShipmentEdit' => 'Checkout_Form_AdminShipmentEdit',
    	'Form_AdminPaymentEdit'  => 'Checkout_Form_AdminPaymentEdit'
    );

    public function init()
    {
        $this->_helper->Injector($this->_defaultInjections);
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }

    protected function _getShipmentService()
    {
        $service = Model_Service::factory('checkout/shipment');
        return $service;
    }

    protected function _getPaymentService()
    {
        $service = Model_Service::factory('checkout/payment');
        return $service;
    }

    public function indexShipmentAction()
    {
        $this->view->shipments = $this->_getShipmentService()->getAll();
    }

    public function onOffShipmentAction()
    {
        $method = $this->_getParam('method');
        $on = $this->_getParam('on', 0);
        $service = $this->_getShipmentService();
        $obj = $service->getComplex($method);
        $obj->active = $on;
        $service->saveComplex($obj);
        $this->_forward('index-shipment', 'admin-brule', 'checkout');
    }

    public function sortingShipmentAction()
    {
        $position = $this->_getParam('position');
        $method = $this->_getParam('method');
        $this->_getShipmentService()->changeSorting($method, $position);
        $this->_forward('index-shipment', 'admin-brule', 'checkout');
    }

    public function editShipmentAction()
    {
        if ( ! $cancelUrl = $this->getHelper('ReturnUrl')->get()) {
            $cancelUrl = $this->view->stdUrl(array('method'=>NULL), 'index-shipment');
        }
        if ( ! $submitUrl = $this->getHelper('ReturnUrl')->get()) {
            $submitUrl = $this->view->stdUrl(array('method'=>NULL), 'index-shipment');
        }
        $service = $this->_getShipmentService();
        $method = $this->_getParam('method');
        $payment = $service->getComplex($method);
        $form = $this->getHelper('Injector')->getInjector()->getObject('Form_AdminShipmentEdit');
        $this->getHelper('Shipment')->extendAdminEditForm($form, $payment);
        if ( ! $form->isAnswerPositive()) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Module edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($cancelUrl);
        }
        else if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getParams();
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
            if ($form->isValid($values)) {
                $service->saveFromValues($values);
                $this->getHelper('flashMessenger')->addMessage($this->view->translate('Module saved'));
                $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
            }
            else {
                $this->getHelper('flashMessenger')->addMessage($this->view->translate('Form validation failed'));
            }
        }
        else {
            $values = $service->getEditFormValues($method);
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
        }
    }

    public function indexPaymentAction()
    {
        $this->view->payments = $this->_getPaymentService()->getAll();
    }

    public function onOffPaymentAction()
    {
        $method = $this->_getParam('method');
        $on = $this->_getParam('on', 0);
        $service = $this->_getPaymentService();
        $obj = $service->getComplex($method);
        $obj->active = $on;
        $service->saveComplex($obj);
        $this->_forward('index-payment', 'admin-brule', 'checkout');
    }

    public function sortingPaymentAction()
    {
        $position = $this->_getParam('position');
        $method = $this->_getParam('method');
        $this->_getPaymentService()->changeSorting($method, $position);
        $this->_forward('index-payment', 'admin-brule', 'checkout');
    }

    public function editPaymentAction()
    {
        if ( ! $cancelUrl = $this->getHelper('ReturnUrl')->get()) {
            $cancelUrl = $this->view->stdUrl(array('method'=>NULL), 'index-payment');
        }
        if ( ! $submitUrl = $this->getHelper('ReturnUrl')->get()) {
            $submitUrl = $this->view->stdUrl(array('method'=>NULL), 'index-payment');
        }
        $service = $this->_getPaymentService();
        $method = $this->_getParam('method');
        $payment = $service->getComplex($method);
        $form = $this->getHelper('Injector')->getInjector()->getObject('Form_AdminPaymentEdit');
        $this->getHelper('Payment')->extendAdminEditForm($form, $payment);
        if ( ! $form->isAnswerPositive()) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Module edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($cancelUrl);
        }
        else if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getParams();
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
            if ($form->isValid($values)) {
                $service->saveFromValues($values);
                $this->getHelper('flashMessenger')->addMessage($this->view->translate('Module saved'));
                $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
            }
            else {
                $this->getHelper('flashMessenger')->addMessage($this->view->translate('Form validation failed'));
            }
        }
        else {
            $values = $service->getEditFormValues($method);
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
        }
    }

}