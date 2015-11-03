<?php

class Checkout_View_Helper_Box_ShoppingCart extends Zend_View_Helper_Abstract
{

    public function box_ShoppingCart()
    {
        $service = Model_Service::factory('checkout/cart');
        $this->view->totalQty = $service->calculateTotalQty();
        $this->view->totalSumm = $service->calculateTotal();
        $this->view->positionQty = $service->getAll()->count();
        $this->view->cartLink = $this->view->stdUrl(array('reset'=>TRUE), 'index', 'cart', 'checkout');
        $html = $this->view->render('box/shopping-cart.phtml');
        return $html;
    }

}
