<?php

class UserController extends Zend_Controller_Action
{
    
    protected $_defaultInjections = array(
        'Form_Register' => 'Form_UserRegister',
    );
    
    public function init()
    {
        $this->_helper->Injector($this->_defaultInjections);
        App_Event::factory('Controller__init', array($this, $this->_getParam('layoutName'), TRUE))->dispatch();
    }


    public function registerAction()
    {		
        if ($this->_getParam('noLayout')) {
            $this->view->layout()->disableLayout();
        }		
        $service = Model_Service::factory('user');
        $form = $this->_getParam('form', $this->getInjector()->getObject('Form_Register'));
        $values = $this->getRequest()->getParams();
        $form->populate($values);
        $this->view->form = $form;
        $this->view->values = $values;	
        if (($this->getRequest()->isPost() AND ($form->getAnswer()!='cancel')) OR $this->getRequest()->isXmlHttpRequest()) {
            if ($form->isValid($values)) {
    	        $values['export'] = 1;
    	        $service->registerNewUser($values);
            	$service->authorize($values['login'], NULL, TRUE);
                $this->view->success = TRUE;
        		$this->view->user = $service->getCurrent();
        		if (( ! $this->getRequest()->isXmlHttpRequest()) AND ($redirectUrl = $this->_getParam('redirect'))) {
        		    $this->getHelper('Redirector')->gotoUrlAndExit($redirectUrl);
        		}
            }
    	    else {
        		$this->view->success = FALSE;
            }		
        }
        else if ($form->getAnswer()=='cancel') {
    	    $this->getHelper('Redirector')->gotoUrlAndExit($this->_getParam('redirectCancel', $this->_getParam('redirect', $this->view->stdUrl(NULL, 'index', 'index', 'kernel'))));
        }
    }

    public function profileAction()
    {
        $this->view->user = Model_Service::factory('user')->getComplexActiveByLogin($this->_getParam('login'));
    }
    
    public function changeLanguageAction()
    {
        $newLangId = $this->_getParam('lang');
        $redirectUrl = $this->_getParam('redirect');
        $service = Model_Service::factory('language');
        $lang = $service->get($newLangId);
        $service->setCurrent($lang);
        $this->getHelper('Redirector')->gotoUrlAndExit($redirectUrl);
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
            $this->getHelper('ViewRenderer')->setNoRender();
        }
    }
      
    public function getInjector()
    {
        $injector = $this->_getParam('injector', $this->getHelper('Injector')->getInjector());
        return $injector;
    }
    
}

