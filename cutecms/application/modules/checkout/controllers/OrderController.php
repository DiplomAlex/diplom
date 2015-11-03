<?php

class Checkout_OrderController extends Zend_Controller_Action
{

    protected $_defaultInjections = array(
        'Form_Cancel'  => 'App_Form_Question',
        'Form_Confirm' => 'App_Form_Question',
        'Form_Shipment'=> 'Checkout_Form_OrderShipment',
        'Form_Payment' => 'Checkout_Form_OrderPayment',
    );

    protected $_injector = NULL;

    /**
     * @return App_DIContainer
     */
    public function getInjector()
    {
        if (($this->_injector === NULL) AND ( ! $this->_injector = $this->_getParam('injector'))) {
            $this->_injector = new App_DIContainer($this);
            foreach ($this->_defaultInjections as $interface=>$class) {
                $this->_injector->inject($interface, $class);
            }
        }
        return $this->_injector;
    }

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        if ($view = $this->_getParam('view', FALSE)) {
            $this->view = $view;
            $this->getHelper('ViewRenderer')->setView($view);
        }
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        }
        else {
	        $this->view->layout()->setLayout($this->_getLayoutName());
        }
    }

    /**
     * Enter point
     */
    public function startAction()
    {
        $service = $this->_getService();
        $service->resetCurrent();
        $this->stepAction();
    }

    /**
     * required steps of ordering process:
     * 1) select shipment type and parameters (address, etc.)
     * 2) select payment method and parameters (requisites, etc.)
     * 3) recalculate total summ according to selected shipment show total order and confirm it
     * 4) if order should be payed online - process the payment
     * 5)
     */
    public function stepAction()
    {
        $urls = $this->_getUrls();
        if ($this->_isQuickOrder()) {
            $this->getHelper('OrderSteps')->walk('quick', $urls);
        }
        else {
            if ($this->_isPaymentBeforeShipment()) {
                $this->getHelper('OrderSteps')->walk('payment-before-shipment', $urls);
            }
            else {
                $this->getHelper('OrderSteps')->walk('shipment-before-payment', $urls);
            }
        }
    }

    /**
     * @TODO - needs refactoring
     * */
    public function preorderAction()
    {
        if ($this->_getParam('submit') > 0) {
            $this->getHelper('Redirector')->gotoUrlAndExit($this->_getUrlStart());
        }
        else {
            $preorderService = Model_Service::factory('checkout/preorder');
            $preorder = $preorderService->loadCurrent($this->_getParam('id'));
            $this->view->preorder = $preorder;
        }
    }


    /**
     * quick order form
     */
    public function quickAction()
    {
        $service = $this->_getService();
        $order = $service->getCurrent();
        $form = $this->getInjector()->getObject('Form_Quick');
        $this->getHelper('Shipment')->extendQuickForm($form, $service->getShipment());
        $this->getHelper('Payment')->extendQuickForm($form, $service->getPayment());
        $fieldsShipment = $this->getHelper('Shipment')->getQuickFormFields($service->getShipment());
        $fieldsPayment = $this->getHelper('Payment')->getQuickFormFields($service->getPayment());
        $values = array_merge($fieldsShipment, $fieldsPayment, $this->getRequest()->getParams());
        $form->populate($values);
        if (($isPost = $this->getRequest()->isPost()) AND $form->isAnswerPositive()) {
            if ($form->isValid()) {
                $values['method'] = $values['shipment_method'];
                $service->setShipmentFromValues($values);
                $service->getHelper('BruleShipment')->onAfterPrepare($order->shipment, $order, $values);
                $values['method'] = $values['payment_method'];
                $service->setPaymentFromValues($values);
                $service->getHelper('BrulePayment')->onAfterPrepare($order->payment, $order, $values);
                $this->stepAction();
            }
        }
        else if ($isPost) {
            $this->getHelper('Redirector')->gotoUrlAndExit($this->_getUrlCancel());
        }
        $this->view->form = $form;
        $this->view->order = $order;
        $this->view->shipmentHtml = $this->getHelper('Shipment')->renderQuickSubform($form);
        $this->view->paymentHtml  = $this->getHelper('Payment') ->renderQuickSubform($form);
    }

    /**
     * cancel ordering
     */
    public function cancelAction()
    {
        $form = $this->getInjector()->getObject('Form_Cancel')->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                $service = $this->_getService();
                $service->resetCurrent(FALSE);
                $this->view->cancelled = TRUE;
            }
            else {
                $this->stepAction();
            }
        }
        else {
            $this->view->cancelled = FALSE;
            $this->view->form = $form;
        }
    }


    /**
     * shipment selection form
     */
    public function shipmentAction()
    {
        $service = $this->_getService();
        $form = $this->getHelper('Shipment')->getSelectForm($service->getShipment());
        if ($this->getRequest()->isPost()) {
            if ($form->IsAnswerPositive()) {
                $service->setShipmentFromValues($this->getRequest()->getParams());
                $this->stepAction();
            }
            else {
                $this->getHelper('Redirector')->gotoUrlAndExit($this->_getUrlCancel());
            }
        }
        else {
            $this->view->form = $form;
            $this->view->order = $service->getCurrent();
        }
    }

    /**
     * shipment requisites input form
     */
    public function shipmentPrepareAction()
    {
        $service = $this->_getService();
        $order = $service->getCurrent();
        $service->getHelper('BruleShipment')->onBeforePrepare($order->shipment, $order);
        $form = $this->getHelper('Shipment')->getPrepareForm($order->shipment);
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $form->populate($params);
            if ( ! $form->IsAnswerPositive()) {
                $this->getHelper('Redirector')->gotoUrlAndExit($this->_getUrlCancel());
            }
            if ($form->isValid($params)) {
                $service->setShipmentFromValues($params);
                $service->getHelper('BruleShipment')->onAfterPrepare($order->shipment, $order, $params);
                $this->stepAction();
            }
        }
        $this->view->form = $form;
        $this->view->order = $service->getCurrent();
        $this->view->innerHtml = $this->getHelper('Shipment')->renderPrepareForm($form, $service->getShipment());
    }
    
    public function shipmentPrepareAjaxAction()
    {
        $this->view->layout()->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        $service = $this->_getService();
        $order = $service->getCurrent();        
        if ($response = $this->getHelper('Shipment')->prepareAjaxAction($order->shipment)) {
            echo $response;
        }
    }
    

    /**
     * payment selection form
     */
    public function paymentAction()
    {
        $service = $this->_getService();
        $curr = $service->getCurrent()->payment;
        $form = $this->getHelper('Payment')->getSelectForm($curr);
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer() != 'cancel') {
                $service->setPaymentFromValues($this->getRequest()->getParams());
                $this->stepAction();
            }
            else {
                $this->getHelper('Redirector')->gotoUrlAndExit($this->_getUrlCancel());
            }
        }
        else {
            $this->view->form = $form;
            $this->view->order = $service->getCurrent();
        }
    }

    /**
     * payment requisites form
     */
    public function paymentPrepareAction()
    {
        $service = $this->_getService();
        $order = $service->getCurrent();
        $service->getHelper('BrulePayment')->onBeforePrepare($order->payment, $order);
        $form = $this->getHelper('Payment')->getPrepareForm($order->payment);
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $form->populate($params);
            if ( ! $form->IsAnswerPositive()) {
                $this->getHelper('Redirector')->gotoUrlAndExit($this->_getUrlCancel());
            }
            if ($form->isValid($params)) {
                $service->setPaymentFromValues($params);
                $service->getHelper('BrulePayment')->onAfterPrepare($order->payment, $order, $params);
                $this->stepAction();
            }
        }
        $this->view->form = $form;
        $this->view->order = $service->getCurrent();
        $this->view->innerHtml = $this->getHelper('Payment')->renderPrepareForm($form, $service->getPayment());
    }
    
    public function paymentPrepareAjaxAction()
    {
        $this->view->layout()->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        $service = $this->_getService();
        $order = $service->getCurrent();
        if ($response = $this->getHelper('Payment')->prepareAjaxAction()) {
            echo $response;
        }
    }

    /**
     * payment processing - sending to payment gateway and recieving data back
     */
    public function paymentProcessAction()
    {
        $service = $this->_getService();
        $curr = $service->getCurrent()->payment;
        if ($innerHtml = $this->getHelper('Payment')->process($curr)){
            $this->view->innerHtml = $innerHtml;
        }
        else {
            $this->stepAction();
        }
    }

    /**
     * confirmation of fully prepared order
     */
    public function confirmAction()
    {
        $service = $this->_getService();
        $form = $this->getInjector()->getObject('Form_Confirm')->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->isAnswerPositive()) {
                $service->confirmCurrent();
                Model_Service::factory('checkout/cart')->reset();
                $this->stepAction();
            }
            else {
                $this->getHelper('Redirector')->gotoUrlAndExit($this->_getUrlCancel());
            }
        }
        else {
            $this->view->form = $form;
            $this->view->order = $service->getCurrent();
            $this->view->shipmentInfoHtml = $this->getHelper('Shipment')->renderScreenInfo($service->getShipment());
            $this->view->paymentInfoHtml  = $this->getHelper('Payment') ->renderScreenInfo($service->getPayment());
        }
    }

    /**
     * final page - "thnx. you order is complete"
     */
    public function finishAction()
    {
        $service = $this->_getService();
        $order = $service->getCurrent();
        $this->view->order = $order;
    }

    /**
     * show printable blank
     */
    public function printAction()
    {   //print_r(asdasd);exit;
        $this->view->layout()->setLayout('blank');
	
        $id = $this->_getParam('id');
	
        if ($this->_getParam('preorder', FALSE)) {
            $service = Model_Service::factory('checkout/preorder');
        }
        else {
            $service = $this->_getService();
        }
        if ( ! empty($id)) {
            $order = $service->getComplex($id);
        }
        else {
            $order = $service->getCurrent();
        }
        $this->view->shipmentInfoHtml = $this->getHelper('Shipment')->renderPrintInfo($order->shipment);
        $this->view->paymentInfoHtml  = $this->getHelper('Payment') ->renderPrintInfo($order->payment);        
        $this->view->order = $order;
    }

    /**
     * list of previos orders
     *
     * @TODO - add list of preorders
     *
     */
    public function myOrdersAction()
    {
        $this->view->orders = $this->_getService()->paginatorGetAllByUser(
            NULL,
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        
        $this->view->preorders = Model_Service::factory('checkout/preorder')->paginatorGetAllByUser(
            NULL,
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        
    }

    /**
     * previous order detailed page
     *
     * @TODO - add the same page for preorder
     *
     */
    public function myOrderViewAction()
    {
        $id = $this->_getParam('id');
        $service = $this->_getService();
        $order = $service->getComplex($id);
        $this->view->order = $order;
        $this->view->shipmentInfoHtml = $this->getHelper('Shipment')->renderScreenInfo($order->shipment);
        $this->view->paymentInfoHtml  = $this->getHelper('Payment') ->renderScreenInfo($order->payment);        
        
    }

    protected function _getService()
    {
        $service = Model_Service::factory('checkout/order');
        return $service;
    }


    /**
     * @return array
     */
    protected function _getUrls()
    {
        $urls = array(
            'urlStart' => $this->_getUrlStart(),
            'urlFinish' => $this->_getUrlFinish(),
        	'urlConfirm' => $this->_getUrlConfirm(),
        	'urlCancel' => $this->_getUrlCancel(),
        	'urlQuick' => $this->_getUrlQuick(),
            'urlShipmentSelect' => $this->_getUrlShipmentSelect(),
        	'urlShipmentPrepare' => $this->_getUrlShipmentPrepare(),
        	'urlPaymentSelect' => $this->_getUrlPaymentSelect(),
        	'urlPaymentPrepare' => $this->_getUrlPaymentPrepare(),
        	'urlPaymentProcess' => $this->_getUrlPaymentProcess(),
        );
        return $urls;
    }

    /* url getters */
    protected function _getUrlStart()
    {
        if ( ! $url = $this->_getParam('urlStart')) {
            $url = $this->view->stdUrl(NULL, 'start', 'order', 'checkout');
        }
        return $url;
    }

    protected function _getUrlCancel()
    {
        if ( ! $url = $this->_getParam('urlCancel')) {
            $url = $this->view->stdUrl(NULL, 'cancel', 'order', 'checkout');
        }
        return $url;
    }

    protected function _getUrlShipmentSelect()
    {
        if ( ! $url = $this->_getParam('urlShipmentSelect')) {
            $url = $this->view->stdUrl(NULL, 'shipment', 'order', 'checkout');
        }
        return $url;
    }

    protected function _getUrlPaymentSelect()
    {
        if ( ! $url = $this->_getParam('urlPaymentSelect')) {
            $url = $this->view->stdUrl(NULL, 'payment', 'order', 'checkout');
        }
        return $url;
    }

    protected function _getUrlShipmentPrepare()
    {
        if ( ! $url = $this->_getParam('urlShipmentPrepare')) {
            $url = $this->view->stdUrl(NULL, 'shipment-prepare', 'order', 'checkout');
        }
        return $url;
    }

    protected function _getUrlPaymentPrepare()
    {
        if ( ! $url = $this->_getParam('urlPaymentPrepare')) {
            $url = $this->view->stdUrl(NULL, 'payment-prepare', 'order', 'checkout');
        }
        return $url;
    }

    protected function _getUrlPaymentProcess()
    {
        if ( ! $url = $this->_getParam('urlPaymentProcess')) {
            $url = $this->view->stdUrl(NULL, 'payment-process', 'order', 'checkout');
        }
        return $url;
    }

    protected function _getUrlConfirm()
    {
        if ( ! $url = $this->_getParam('urlConfirm')) {
            $url = $this->view->stdUrl(NULL, 'confirm', 'order', 'checkout');
        }
        return $url;
    }

    protected function _getUrlFinish()
    {
        if ( ! $url = $this->_getParam('urlFinish')) {
            $url = $this->view->stdUrl(NULL, 'finish', 'order', 'checkout');
        }
        return $url;
    }

    protected function _getUrlQuick()
    {
        if ( ! $url = $this->_getParam('urlQuick')) {
            $url = $this->view->stdUrl(NULL, 'quick', 'order', 'checkout');
        }
        return $url;
    }

    protected function _getLayoutName()
    {
        if ( ! $name = $this->_getParam('layoutName')) {
            $name = 'layout';
        }
        return $name;
    }

    protected function _isPaymentBeforeShipment()
    {
        $result = (bool) $this->_getParam('payment-before-shipment', (int) Zend_Registry::get('checkout_config')->order->paymentBeforeShipment);
        return $result;
    }

    protected function _isQuickOrder()
    {
        $result = (bool) $this->_getParam('quick-order', (int) Zend_Registry::get('checkout_config')->order->quick);
        return $result;
    }


}