<?php

class AdminAuthController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('Controller__init', array($this, 'admin-auth'))->dispatch();
    }

    /**
     * main login
     */
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
                $this->view->error = TRUE;
            }
            if ($this->view->success === TRUE) {
                $url = $this->view->url(array(), 'admin_index');
                $this->getHelper('Redirector')->gotoUrlAndExit($url, array('prependBase'=>FALSE));
            }
        }
        $this->view->form = new Form_Login;
    }

    /**
     * main logout
     */
    public function logoutAction()
    {
        Model_Service::factory('user')->unauthorize();
    }

    public function changeAuthorizedUserAction()
    {
        $newId = $this->_getParam('id');
        $service = Model_Service::factory('user');
        $user = $service->get($newId);
        $service->reauthorize($user);
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->url(array(), 'admin_index'));
    }

}