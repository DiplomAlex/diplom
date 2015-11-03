<?php

class Checkout_View_Helper_Observer_Box extends View_Helper_Observer_Abstract
{

    public function shoppingCart()
    {
        $html = $this->view->box_ShoppingCart();
        $this->getEvent()->addResponse($html);
    }

}