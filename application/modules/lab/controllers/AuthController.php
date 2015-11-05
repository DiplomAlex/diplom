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
		$params = $this->_getForwardParams();
		
		if ($this->getRequest()->isPost()) {
		   if ($form->isValid($params)){
		   		$isAjax = $this->getRequest()->isXmlHttpRequest();
				if ($isAjax) {
					$this->view->layout()->disableLayout();
					$params['redirectAfterLogin'] = FALSE;
					$params['disableLayout'] = TRUE;
					$this->view->isJsonResponse = TRUE;
				}
			}
		}
		if($this->_getParam('login') != '' and $this->_getParam('password') != ''){
				$this->view->ER = FALSE;
			try {
				Model_Service::factory('user')->authorize($this->_getParam('login'), $this->_getParam('password'));
			}
			catch(Model_Service_Exception $e) {
				$this->view->ER = TRUE;
			}
			if($this->view->ER != TRUE){
				$this->_forward('index', 'index', 'lab');
			}
		}
	}

    public function logoutAction()
    {
        $this->_forward('logout', 'auth', 'kernel', $this->_getForwardParams());
    }
    
    public function forgotPasswordAction()
    {
        $this->_forward('forgot-password', 'auth', 'kernel', $this->_getForwardParams());
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

