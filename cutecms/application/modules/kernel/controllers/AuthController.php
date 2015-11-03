<?php

class AuthController extends Zend_Controller_Action
{

    protected $_defaultInjections = array(
        'Form_Login',
        'Form_ForgotPassword' => 'Form_AuthForgotPassword',
    );

    protected $_injector = NULL;
    
    /**
     * @return App_DIContainer
     */
    public function getInjector()
    {
        if (($this->_injector === NULL) AND ( ! $this->_injector = $this->_getParam('injector'))) {
            $this->_injector = new App_DIContainer($this);
            foreach ($this->_defaultInjections as $interface=>$class) {
                $this->_injector->inject($interface, $class);
            }
        }
        return $this->_injector;
    }    
    
    public function init()
    {
        App_Event::factory('Controller__init', array($this, $this->_getLayoutName()))->dispatch();
        if ($this->_getDisableLayout()) {
            $this->view->layout()->disableLayout();
        }
    }

    /**
     * main login
     */
    public function loginAction()
    {
        if ($this->getRequest()->isPost() OR $this->getRequest()->isXmlHttpRequest()) {
            $this->view->submitted = TRUE;
            $this->view->success = TRUE;
            try {
                Model_Service::factory('user')->authorize($this->_getParam('login'), $this->_getParam('password'));
            }
            catch(Model_Service_Exception $e) {
                $this->view->success = FALSE;
            }
            if ($this->_getParam('remember')) {
                Zend_Session::rememberMe(3600 * 24 * 365); /* 1 year */
            }
            if (($this->view->success === TRUE) AND ($this->_getRedirectAfterLogin()===TRUE)) {
                if ( ! $url = $this->_getParam('redirect_url')) {
                    $url = $this->_getAfterLoginRedirectUrl();
                }
                $this->getHelper('Redirector')->setPrependBase(FALSE)->gotoUrlAndExit($url);
            }
        }        
        $this->view->form = $this->getInjector()->getObject('Form_Login');
    }

    /**
     * main logout
     */
    public function logoutAction()
    {
        Model_Service::factory('user')->unauthorize();
        if ($this->_getRedirectAfterLogout()===TRUE) {
            $url = $this->_getAfterLogoutRedirectUrl();
            $this->getHelper('Redirector')->setPrependBase(FALSE)->gotoUrlAndExit($url);
        }
    }

    public function forgotPasswordAction()
    {
        $this->view->layout()->setLayout('layout');
        $service = Model_Service::factory('user');
        $form = $this->getInjector()->getObject('Form_ForgotPassword');
        $config = Zend_Registry::get('config');
       
        if ($this->getRequest()->isPost()) {
             
            $this->view->submitted = TRUE;
            if ($form->isValid($_POST)) {                                    
			
                $user = $service->recoverPassword($form->email->getValue());
				
                $this->view->siteHref = $this->view->stdUrl(array('reset'=>TRUE));
                $this->view->siteName = $config->www->siteName;
                $this->view->user = $user;
                App_Mail::factory()
                        ->addTo($user->email, $user->name)
                        ->setFrom($config->email->support, $config->email->supportName)
                        ->setSubject ($this->view->render('auth/mail-new-password-subj.phtml'))
                        ->setBodyHtml($this->view->render('auth/mail-new-password-html.phtml'))
                        ->setBodyText($this->view->render('auth/mail-new-password-text.phtml'))
                        ->send()
                        ;
                
                $this->view->success = TRUE;
            }
        }
        $this->view->form = $form;
        
    }

    /**
     * usually is called by clicking link "reload" near captcha image
     */
    public function captchaReloadAction()
    {
        $captcha = App_Captcha::factory();
        $obj = new StdClass;
        $captcha->generate();
        $obj->id = $captcha->getId();
        $obj->view = $captcha->render($this->view);
        echo Zend_Json::encode($obj);
        exit;
    }

    public function changeAuthorizedUserAction()
    {
        $newId = $this->_getParam('id');
        $service = Model_Service::factory('user');
        $user = $service->get($newId);
        $service->reauthorize($user);
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->serverUrl(APPLICATION_BASE));
    }
    
    

    protected function _getLayoutName()
    {
        if ( ! $name = $this->_getParam('layoutName')) {
            $name = 'auth';
        }
        return $name;
    }
        
    protected function _getAfterLoginRedirectUrl()
    {
        if ( ! $url = $this->_getParam('afterLoginRedirectUrl')) {
            $url = $this->view->serverUrl(APPLICATION_BASE);
        }
        return $url;
    }
        
    protected function _getAfterLogoutRedirectUrl()
    {
        if ( ! $url = $this->_getParam('afterLogoutRedirectUrl')) {
            $url = $this->view->serverUrl(APPLICATION_BASE);
        }
        return $url;
    }
    
    protected function _getRedirectAfterLogin()
    {
        $result = $this->_getParam('redirectAfterLogin', TRUE);
        return $result;
    }
    
    protected function _getRedirectAfterLogout()
    {
        $result = $this->_getParam('redirectAfterLogout', TRUE);
        return $result;
    }
        
    
    protected function _getDisableLayout()
    {
        $result = $this->_getParam('disableLayout', FALSE);
        return $result;
    }
    
}