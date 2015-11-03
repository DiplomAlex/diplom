<?php

class AdminAuthController extends Zend_Controller_Action
{

    public function init()
    {
        //App_Event::factory('AdminController__init', array($this, 'admin-auth'))->dispatch();
    }


    public function loginAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->view->submitted = TRUE;
            $this->view->success = TRUE;
            try {
                Model_Service::factory('user')->authorize($this->_getParam('login'), $this->_getParam('password'));
            }
            catch(Model_Service_Exception $e) {
                $this->view->success = FALSE;
            }
            if ($this->view->success === TRUE) {
                if ( ! $url = $this->_getParam('redirect_url')) {
                    $url = $this->view->stdUrl(NULL, 'index', 'admin-index');
                }
                $this->getHelper('Redirector')->gotoUrlAndExit($url);
            }
        }

        $this->view->form = new Form_Login;

    }

    public function logoutAction()
    {
        Model_Service::factory('user')->unauthorize();
    }

}