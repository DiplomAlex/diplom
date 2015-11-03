<?php

class View_Helper_Box_Currency extends Zend_View_Helper_Abstract
{

    public function box_Currency()
    {
        $service = Model_Service::factory('currency');
        $currencies = $service->getAllDefaultFirst();
        $rates = array();
        $this->view->currencies = array();
        foreach ($currencies as $curr) {
            $rates []= $curr->signPre.' '.$curr->rate.' '.$curr->signPost;
            $this->view->currencies[$curr->code] = $curr->name;
        }
        $this->view->currentCurrency = $service->getCurrent()->code;
        $this->view->rates = $rates;
        //return $this->view->render('box/currency.phtml');
    }

}
