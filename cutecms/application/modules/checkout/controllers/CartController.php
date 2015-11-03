<?php

class Checkout_CartController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('Checkout_Controller__init', array($this))->dispatch(
        );
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        }
        $this->view->headTitle('Корзина');
    }

    public function indexAction()
    {
        $this->view->items = Model_Service::factory('checkout/cart')->getAll();
        $this->view->isUserLoggedIn = Model_Service::factory('user')
            ->isAuthorized();
    }

    public function deleteAction()
    {
        $hash = $this->_getParam('hash');
        Model_Service::factory('checkout/cart')->remove($hash);
        $redirectUrl = $this->_getParam(
            'redirectUrl', $this->view->stdUrl(array(), 'shop-cart-index')
        );
        $this->getHelper('Redirector')->gotoUrlAndExit($redirectUrl);
    }

    public function recalculateAction()
    {
        // print_r($this->_getParam('newQty'));exit;
        $service = Model_Service::factory('checkout/cart');
        $hash = $this->_getParam('hash');
        $qty = $this->_getParam('newQty');
        $del = $this->_getParam('del');
        if (!empty($del)) {
            $service->removeArray($del);
            $qtyData = array();
            foreach ($hash as $key => $hashVal) {
                if (!in_array($hashVal, $del)) {
                    $qtyData[$hashVal] = $qty[$key];
                }
            }
        } else {
            $qtyData = array_combine($hash, $qty);
        }
        $service->recalculate($qtyData);
        $this->indexAction();
        $this->getHelper('ViewRenderer')->setScriptAction('index');
    }

    public function ajaxGetCartBoxAction()
    {
        $this->getHelper('ViewRenderer')->setNoRender();

        echo Zend_Json::encode(
            array(
                'success' => true,
                'cart' => $this->view->box_ShoppingCart(),
                'pre_cart' => $this->view->box_ShoppingCart(
                    'box/pre-shopping-cart.phtml'
                )
            )
        );
    }

    public function cleanAction()
    {
        Model_Service::factory('checkout/cart')->clean();
        $redirectUrl = $this->_getParam(
            'redirectUrl', $this->view->stdUrl(array(), 'shop-cart-index')
        );
        $this->getHelper('Redirector')->gotoUrlAndExit($redirectUrl);
    }

    /**
     * add item to cart
     * and redirect to cart page if not ajax request
     */
    public function addToCartAction()
    {
        $this->getHelper('ViewRenderer')->setNoRender();
        $redirectUrl = $this->_getParam(
            'redirectUrl',
            $this->view->stdUrl(null, 'index', 'cart', 'checkout')
        );
        /** @var $itemService Catalog_Model_Service_Item */
        $itemService = Model_Service::factory('catalog/item');
        /** @var $bundleService Catalog_Model_Service_ItemBundle */
        $bundleService = Model_Service::factory('catalog/item-bundle');
        $values = $this->getRequest()->getParams();
        $item = $itemService->createFromArray($values);
        $item->current_bundles = $bundleService->mergeAllWithCurrentsFromArray(
            $item, $values
        );
        if ($item->isCalculatable()) {
            $itemService->addToShoppingCart($item);
            if (!$this->getRequest()->isXmlHttpRequest()) {
                $this->getHelper('Redirector')->gotoUrlAndExit($redirectUrl);
            }

            echo 'ok';
        } else {
            if (!$this->getRequest()->isXmlHttpRequest()) {
                $this->getHelper('Redirector')->gotoUrlAndExit($redirectUrl);
            }
            echo 'error';
        }
    }

}