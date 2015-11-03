<?php

class Shop_View_Helper_Order_Quick extends Checkout_View_Helper_Order_Total
{
    
    public function box_Quick()
    {
	$service = Model_Service::factory('checkout/cart');
        $this->view->totalQty = $service->calculateTotalQty();
        $this->view->totalSumm = $service->calculateTotal();
	return $this->view->render('box/quick.phtml');
    }

}