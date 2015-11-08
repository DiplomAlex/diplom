<?php

class Lab_AuthController extends Zend_Controller_Action
{

    protected $_forwardParams = NULL;

    protected $_defaultInjections = array(
        'Form_Login' => 'Lab_Form_Login',
        'Form_ForgotPassword' => 'Form_AuthForgotPassword',
    );

    protected $_injector = NULL;

    public function getInjector()
    {
        if (($this->_injector === NULL) AND (!$this->_injector = $this->_getParam('injector'))) {
            $this->_injector = new App_DIContainer($this);
            foreach ($this->_defaultInjections as $interface => $class) {
                $this->_injector->inject($interface, $class);
            }
        }
        return $this->_injector;
    }


    public function init()
    {
        $this->view->flag_right_colum = 2;
        $this->view->flag_telef = TRUE;
        App_Event::factory('Lab_Controller__init', array($this))->dispatch();
        $this->_helper->Injector($this->_defaultInjections);
    }


    public function loginAction()
    {
        $this->view->headTitle('Вход на сайт');
        $form = $this->getInjector()->getObject('Form_Login');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_getAllParams())) {
                $this->view->ER = FALSE;

                if ($this->_getParam('login') != '' and $this->_getParam('password') != '') {
                    $this->view->ER = FALSE;
                    try {
                        Model_Service::factory('user')->authorize($this->_getParam('login'), $this->_getParam('password'));
                    } catch (Model_Service_Exception $e) {
                        $this->view->ER = TRUE;
                    }
                    if ($this->view->ER != TRUE) {
                        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array()), array());
                    }
                }
            }
        }
    }

    public function logoutAction()
    {
        $this->_forward('logout', 'auth', 'kernel', $this->_getForwardParams());
    }

    public function forgotPasswordAction()
    {
        $service = Model_Service::factory('user');
        $form = $this->getInjector()->getObject('Form_ForgotPassword');
        $config = Zend_Registry::get('config');

        if ($this->getRequest()->isPost()) {
            $this->view->submitted = TRUE;
            if ($form->isValid($_POST)) {

                $user = $service->recoverPassword($form->email->getValue());

                $this->view->siteHref = $this->view->stdUrl(array('reset' => TRUE));
                $this->view->siteName = $config->www->siteName;
                $this->view->user = $user;
                App_Mail::factory()
                    ->addTo($user->email, $user->name)
                    ->setFrom($config->email->support, $config->email->supportName)
                    ->setSubject($this->view->render('auth/mail-new-password-subj.phtml'))
                    ->setBodyHtml($this->view->render('auth/mail-new-password-html.phtml'))
                    ->setBodyText($this->view->render('auth/mail-new-password-text.phtml'))
                    ->send();

                $this->view->success = TRUE;
            }
        }
        $this->view->form = $form;
    }


    protected function _getForwardParams()
    {
        if ($this->_forwardParams === NULL) {
            $this->_forwardParams = array(
                'layoutName' => 'layout',
                'disableLayout' => FALSE,
                'redirectAfterLogin' => TRUE,
                'redirectAfterLogout' => TRUE,
                'afterLoginRedirectUrl' => $this->getHelper('Url')->url(array(), 'lab-index'),
                'afterLogoutRedirectUrl' => $this->getHelper('Url')->url(array(), 'lab-index'),
                'injector' => $this->getHelper('Injector')->getInjector(),
            );
        }
        return $this->_forwardParams;
    }


}

