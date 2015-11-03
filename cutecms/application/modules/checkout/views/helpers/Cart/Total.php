<?php

class Checkout_View_Helper_Cart_Total extends Zend_View_Helper_Abstract
{

    public function cart_Total(Model_Collection_Interface $items, $returnHtml = TRUE, $module = 'checkout')
    {
        $viewData = Model_Service::factory('checkout/cart')->getHelper('Brule')
                                                            ->setItems($items)->getRows();
        if ($returnHtml) {
            $result = $this->view->partial('cart/total.phtml', $module, array('data'=>$viewData));
        }
        else {
            $result = $viewData;
        }
        return $result;
    }

}