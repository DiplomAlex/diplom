<?php

class AdminEmailQueueController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
        if (PHP_SAPI == 'cli') {
            $this->view->layout()->disableLayout();
        }
    }

    /**
     * send portion of letters from queue to receivers
     */
    public function sendTopAction()
    {
        $this->view->quantity = Model_Service::factory('email-queue')->sendTop();
    }

}