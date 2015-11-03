<?php

class Shop_CartController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->flag_right_colum = 2;
        App_Event::factory('Shop_Controller__init', array($this, 'layout'))
            ->dispatch();
    }

    public function indexAction()
    {
        $this->_forward('index', 'cart', 'checkout');
    }

    public function deleteAction()
    {
        $this->_forward(
            'delete', 'cart', 'checkout', array_merge(
            $this->getRequest()->getParams(), array(
            'redirectUrl' => $this->view->url(array(), 'shop-cart-index'))
        ));
    }

    public function cleanAction()
    {
        $this->_forward(
            'clean', 'cart', 'checkout', array_merge(
            $this->getRequest()->getParams(), array(
            'redirectUrl' => $this->view->url(array(), 'shop-cart-index'))
        ));
    }

    public function recalculateAction()
    {
        $this->_forward('recalculate', 'cart', 'checkout');
    }

    public function ajaxGetCartBoxAction()
    {
        $this->_forward('ajax-get-cart-box', 'cart', 'checkout');
    }

    /**
     * adds item to cart
     * and redirects to cart page if not ajax request
     */
    public function addToCartAction()
    {
        $this->_forward(
            'add-to-cart', 'cart', 'checkout', array_merge(
            $this->getRequest()->getParams(), array(
            'redirectUrl' => $this->view->url(array(), 'shop-cart-index'))
        ));
    }
}