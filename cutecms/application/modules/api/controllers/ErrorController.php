<?php

class Api_ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('reset' => 'true')));
    }

}