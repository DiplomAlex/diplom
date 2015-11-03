<?php

class Checkout_AdminStatsController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }

}