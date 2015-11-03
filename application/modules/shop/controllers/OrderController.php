<?php

class Shop_OrderController extends Zend_Controller_Action
{
	protected $_itemService = NULL;
    protected $_categoryService = NULL;
    protected $_session = NULL;
    
    protected $_defaultInjections = array(
        'Form_Cancel'  => 'App_Form_Question',
        'Form_Confirm' => 'App_Form_Question',
        'Form_Shipment'=> 'Checkout_Form_OrderShipment',
        'Form_Payment' => 'Checkout_Form_OrderPayment',
        'Form_Quick'   => 'Shop_Form_OrderQuick',
        'Controller_Action_Helper_OrderSteps_Quick' => 'Shop_Controller_Action_Helper_OrderSteps_Quick',    
    );
    
    protected $_forwardParams = NULL;
    
    protected function _session()
    {
		if ($this->_session === NULL) {
			$this->_session = new Zend_Session_Namespace(__CLASS__);
		}
		return $this->_session;
    }

	protected function _getCategoryService()
    {
        if ($this->_categoryService === NULL) {
            $this->_categoryService = Model_Service::factory('catalog/category');
            $site = Model_Service::factory('site')->getCurrent();
            $this->_categoryService->getHelper('Multisite')->setCurrentSiteId($site->id);
        }
        return $this->_categoryService;
    }	
    
    protected function _getService()
    {
        $service = Model_Service::factory('checkout/order');
        return $service;
    }    
	
    public function init()
    {
        $Autorixated = Model_Service::factory('user')->isAuthorized();
        if($Autorixated){
			$this->view->flag_right_colum = 2;
			App_Event::factory('Shop_Controller__init', array($this))->dispatch();
			$this->_helper->Injector($this->_defaultInjections);
			if ($this->getRequest()->isXmlHttpRequest()) {
				$this->view->layout()->disableLayout();
			}else {
				$this->view->menu()->addMenuBreadcrumbsPage(array(
					'label' => $this->view->translate('Корзина'),
					'route' => 'shop-order-start',
				));
			}
        }else{
            $this->getHelper('Redirector')->gotoUrlAndExit("auth/login");
        }		
    }
        
    public function statusAction()
    {
        $Autorixated = Model_Service::factory('user')->isAuthorized();
        if($Autorixated){
            $this->view->flag_right_colum = 2;
        }else{
            $this->getHelper('Redirector')->gotoUrlAndExit("auth/login");
        }
	}
    
    public function getInjector()
    {
        return $this->getHelper('Injector')->getInjector();
    }

    public function preorderAction()
    {
        $this->_forward('preorder', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function startAction()
    {
        $this->_forward('start', 'order', 'checkout', $this->_getForwardParams());
        die();
    }

    public function cancelAction()
    {
        $this->_forward('cancel', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function shipmentAction()
    {
        $this->_forward('shipment', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function shipmentPrepareAction()
    {
        $this->_forward('shipment-prepare', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function shipmentPrepareAjaxAction()
    {
        $this->_forward('shipment-prepare-ajax', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function paymentAction()
    {
        $this->_forward('payment', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function paymentPrepareAction()
    {
        $this->_forward('payment-prepare', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function paymentPrepareAjaxAction()
    {
        $this->_forward('payment-prepare-ajax', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function paymentProcessAction()
    {
        $this->_forward('payment-process', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function confirmAction()
    {	
        $this->_getService()->confirmCurrent();
        Model_Service::factory('checkout/cart')->reset();
        $this->_forward('step', 'order', 'checkout', $this->_getForwardParams());
    }

    public function printAction()
    {	
	    $this->_forward('print', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function myOrdersAction()
    {
        $this->view->menu()->addMenuBreadcrumbsPage(array(
            'label' => $this->view->translate('Ваши заказы'),
            'route' => 'shop-my_orders',
        ));        
        $this->_forward('my-orders', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function myOrderViewAction()
    {
        $this->view->menu()->addMenuBreadcrumbsPage(array(
            'label' => $this->view->translate('История заказов'),
            'route' => 'shop-my_orders',
        ));        
        $this->_forward('my-order-view', 'order', 'checkout', $this->_getForwardParams());
    }
    
    public function quickAction()
    {
        if ($this->getRequest()->isPost()) {
            /** @var $form Shop_Form_OrderQuick */
            $form = $this->getInjector()->getObject('Form_Quick');

            $params = $this->getRequest()->getParams();
            $form->populate($params);

            $isValid = $form->isValid($params);

            $values = array();
            foreach ($form->getElements() as $element) {
                $values[$element->getName()] = $element->getValue();
            }

            if ($isValid) {
                try {
                    /** @var $userService Model_Service_User */
                    $userService = Model_Service::factory('user');
                    $user = $userService->getCurrent();
                    /** @var $orderService Checkout_Model_Service_Order */
                    $orderService = Model_Service::factory('checkout/order');
                    $orderService->resetCurrent();

                    $order = $orderService->getCurrent();
                    $order->client_email = $user->email;
                    $order->client_name = $user->name;
                    $order->guid = App_Uuid::get();

                    $order->client_comment = $params['message'] . "(тел.: " . $params['telephone'] . ")";

                    $orderService->confirmCurrent();
                    if ($order->items->count() < 1) {
                        $this->_helper->json(
                            array(
                                'success' => true,
                                'valid'   => true,
                                'errors'  => array('items' => array('isEmpty' => $this->view->translate('items.isEmpty'))),
                                'values'  => $values
                            )
                        );
                    }

                    $this->_helper->json(
                        array(
                            'success' => true,
                            'cart' => $this->view->box_ShoppingCart(),
                            'pre_cart' => $this->view->box_ShoppingCart('box/pre-shopping-cart.phtml'),
                            'valid' => true,
                            'values' => $values
                        )
                    );
                } catch (Exception $e) {
                    $this->_helper->json(array('success' => false));
                }
            } else {
                $errors = array();
                foreach ($form->getMessages() as $key => $messages) {
                    foreach ($messages as $k => $message) {
                        $errors[$key][] = $this->view->translate(
                            $key . '.' . $k
                        );
                    }
                }

                $this->_helper->json(
                    array('success' => true, 'valid' => false,
                          'errors'  => $errors, 'values' => $values)
                );
            }
        }

        $this->_helper->json(array('success' => false));

        /*$service = $this->_getService();
        $order = $service->getCurrent();
        $userService = Model_Service::factory('user');
        $this->view->userIsAuthorized = $userService->isAuthorized();
        $this->view->showForm = ($userService->isAuthorized() OR $this->_getParam('noregister', FALSE));
        $this->getRequest()->setParam('show-full-form', $this->view->showForm);
        $form = $this->getInjector()->getObject('Form_Quick');
        $this->getHelper('Shipment')->extendQuickForm($form, $service->getShipment());
        $this->getHelper('Payment')->extendQuickForm($form, $service->getPayment());
        $fieldsShipment = $this->getHelper('Shipment')->getQuickFormFields($service->getShipment());
        $fieldsPayment = $this->getHelper('Payment')->getQuickFormFields($service->getPayment());
        $values = array_merge($fieldsShipment, $fieldsPayment, $this->getRequest()->getParams());
        $form->populate($values);
        $form->isValid($values);
        if (($isPost = $this->getRequest()->isPost()) AND $form->isAnswerPositive()) {
                if ($order->shipment AND $order->shipment->method) {
                    $values['method'] = $order->shipment->method;
                }
                else {
                    $values['method'] = $values['shipment_method'];
                }
                $service->setShipmentFromValues($values);
                if ($values['method']) {
                    $service->getHelper('BruleShipment')->onAfterPrepare($order->shipment, $order, $values);
                }
                if ($order->payment AND $order->payment->method) {
                    $values['method'] = $order->payment->method;
                }
                else {
                    $values['method'] = $values['payment_method'];
                }
                $service->setPaymentFromValues($values);
                if ($values['method']) {
                    $service->getHelper('BrulePayment')->onAfterPrepare($order->payment, $order, $values);
                }*/
        /*$this->stepAction();*/
        /*$this->_forward('step', 'order', 'checkout', $this->_getForwardParams());
 }
 else if ($isPost) {
     $this->getHelper('Redirector')->gotoUrlAndExit($this->_getUrlCancel());
 }
 $this->view->form = $form;
 $this->view->order = $order;*/

        /*$this->view->shipmentHtml = $this->getHelper('Shipment')->renderQuickSubform($form, $service->getShipment());
        $this->view->paymentHtml  = $this->getHelper('Payment') ->renderQuickSubform($form, $service->getPayment());
        $this->getRequest()->setParam('act', 'get-mini-form');
        $this->view->addonShipmentHtml = $this->getHelper('Shipment')->prepareAjaxAction($service->getShipment());*/
        $this->view->headTitle('Подтверждение заказа');
        $this->view->items = Model_Service::factory('checkout/cart')->getAll();
    }
    
    public function ajaxItemDeleteAction()
    {
        $hash = $this->_getParam('hash');
        $cartService = Model_Service::factory('checkout/cart');
        $cartService->remove($hash);
        $orderService = $this->_getService();
        $shipment = $orderService->getShipment();
        $payment = $orderService->getPayment();
        $orderService->resetCurrent();
        $orderService->setShipment($shipment);
        $orderService->setPayment($payment);
        $this->getHelper('ViewRenderer')->setNoRender();
        echo 'ok';
    }

    public function ajaxItemUpdateAction()
    {
        $hash = $this->_getParam('hash');
        $qty = $this->_getParam('qty');
        $cartService = Model_Service::factory('checkout/cart');
        $cartService->recalculate(array($hash=>$qty));
        $orderService = $this->_getService();
        $shipment = $orderService->getShipment();
        $payment = $orderService->getPayment();
        $orderService->resetCurrent();
        $orderService->setShipment($shipment);
        $orderService->setPayment($payment);
        $this->getHelper('ViewRenderer')->setNoRender();
        echo 'ok';
    }    
    
    protected function _getForwardParams()
    {
        if ($this->_forwardParams === NULL) {
            $this->_forwardParams = array(
                // 'urlStart' => $this->view->url(array(), 'shop-order-start'),
                // 'urlCancel' => $this->view->url(array(), 'shop-order-cancel'),
                // 'urlShipmentSelect' => $this->view->url(array(), 'shop-order-shipment'),
            	// 'urlShipmentPrepare' => $this->view->url(array(), 'shop-order-shipment-prepare'),
                // 'urlPaymentSelect' => $this->view->url(array(), 'shop-order-payment'),
            	// 'urlPaymentPrepare' => $this->view->url(array(), 'shop-order-payment-prepare'),
                // 'urlPaymentProcess' => $this->view->url(array(), 'shop-order-payment-process'),
                // 'urlConfirm' => $this->view->url(array(), 'shop-order-confirm'),
                // 'urlFinish' => $this->view->url(array(), 'shop-order-finish'),
            	// 'urlQuick' => $this->view->url(array(), 'shop-order-quick'),
                
                'urlStart' => '/order/start',
                'urlFinish' => '/order/finish',
                'urlConfirm' => '/order/confirm',
                'urlCancel' => '/order/cancel',
                'urlQuick' => '/order/quick',
                'urlShipmentSelect' => '/order/shipment',
                'urlShipmentPrepare' => '/order/shipment-prepare',
                'urlPaymentSelect' => '/order/payment',
                'urlPaymentPrepare' => '/order/payment-prepare',
                'urlPaymentProcess' => '/order/payment-process',
        
                'layoutName' => 'layout',
                'injector' => $this->getHelper('Injector')->getInjector(),
                'quick-order' => TRUE,
            );
        }
        return $this->_forwardParams;
    }	
	
	//==============================================	QIWI 	==============================================
	
	public function createbillAction()
    {	
		$params = $this->getRequest()->getParams();
		
		$phone = $params[phone];
		$amount = $params[amount];
		$txn_id = $params[txn_id];
		$comment = $params[comment];
		
		$service = new App_Service_IShopServerWSService_Soap();
		$bill = $service->createBill($phone, $amount, $txn_id, $comment);
		if($bill == 0){ Model_Service::factory('checkout/order')->updateStatus($txn_id,6);}
		$this->view->result = $bill;
	}
	
	public function finishAction()
    {		
        $order = Model_Service::factory('checkout/order')->confirmCurrent();
        die();
	}
	
	public function successAction()
	{
		$this->view->headTitle('Заказ оплачен');
		// регистрационная информация (пароль #1)
		// registration info (password #1)
		$mrh_pass1 = Zend_Registry::get('config')->robokassa->pass1;
		
		// чтение параметров
		// read parameters
		$out_summ = $_REQUEST["OutSum"];
		$inv_id = $_REQUEST["InvId"];
		$shp_item = $_REQUEST["Shp_item"];
		$crc = $_REQUEST["SignatureValue"];
		$crc = strtoupper($crc);
		$my_crc = strtoupper(md5($out_summ . ":" . $inv_id . ":" . $mrh_pass1));
		
		// проверка корректности подписи и номера счета
		// check signature
		$orders = Model_Service::factory('checkout/order')->getAll();
		if ($my_crc != $crc or $inv_id != $orders[0][id])
		{
		  $this->view->result = 0;
		}else{
			$this->view->result = 1;
			$service = Model_Service::factory('checkout/order');
			$service->updateStatus($inv_id,5);
		}
	}
	
	public function resultAction()
	{
		// регистрационная информация (пароль #2)
		// registration info (password #2)
		$mrh_pass2 = Zend_Registry::get('config')->robokassa->pass2;
		
		//установка текущего времени
		//current date
		$tm=getdate(time()+9*3600);
		$date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";
		// чтение параметров
		// read parameters
		$out_summ = $_REQUEST["OutSum"];
		$inv_id = $_REQUEST["InvId"];
		$shp_item = $_REQUEST["Shp_item"];
		$crc = $_REQUEST["SignatureValue"];
		$crc = strtoupper($crc);
	
		$my_crc = strtoupper(md5($out_summ . ":" . $inv_id . ":" . $mrh_pass1));
		// проверка корректности подписи
		// check signature
		if ($my_crc !=$crc)
		{
		  echo "bad sign\n";
		  exit();
		}
		// признак успешно проведенной операции
		// success
		echo "OK$inv_id\n";
	}
	
	public function failAction()
	{
		$this->view->headTitle('Заказ не оплачен');
		$inv_id = $_REQUEST["InvId"];
		$this->view->inv_id = $inv_id;
						
		$service = Model_Service::factory('checkout/order');
		$service->updateStatus($inv_id,6);
	}	
    
}