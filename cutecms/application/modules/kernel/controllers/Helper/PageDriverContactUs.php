<?php

class Controller_Action_Helper_PageDriverContactUs extends Controller_Action_Helper_PageDriver_Abstract
{ 
    
    protected $_defaultInjections = array(
        'Form_ContactUs' => 'Form_PageDriver_ContactUs',
    );
    
    protected $_options = array(
        'viewScript' => 'page/driver/contact-us/action.phtml',
        'ajaxScript' => 'page/driver/contact-us/action.phtml',
        'mailSubjectScript' => 'page/driver/contact-us/mail-to-support.subject.phtml',
        'mailHtmlScript' => 'page/driver/contact-us/mail-to-support.html.phtml',
        'mailTextScript' => 'page/driver/contact-us/mail-to-support.txt.phtml',
        'supportEmail' => NULL,
        'supportName' => NULL,
        'robotEmail' => NULL,
    );
    
    /**
     * (non-PHPdoc)
     * @see application/modules/kernel/controllers/Helper/PageDriver/Controller_Helper_PageDriver_Abstract::isAjaxAccepted()
     */
    public function isAjaxAccepted()
    {
        return TRUE;
    }

    /**
     * (non-PHPdoc)
     * @see application/modules/kernel/controllers/Helper/PageDriver/Controller_Helper_PageDriver_Abstract::ajaxAction()
     */
    public function ajaxAction()
    {
        $controller = $this->getActionController();
        $form = $this->getInjector()->getObject('Form_ContactUs');
        $values = $controller->getRequest()->getParams();
        $form->populate($values);
        if ($form->isValid($values)) {
            $this->_sendEmail($values);
            $controller->view->ContactUs_Success = TRUE;
        }
        else {
            $form->populate($values);
            $controller->view->ContactUs_Error = TRUE;
        }
        $controller->view->ContactUs_Form = $form;
        $response = $controller->view->render($this->getOption('ajaxScript'));
        return $response;
    }
     
    /**
     * (non-PHPdoc)
     * @see application/modules/kernel/controllers/Helper/PageDriver/Controller_Helper_PageDriver_Abstract::action()
     */
    public function action()
    {
        $controller = $this->getActionController();
        $form = $this->getInjector()->getObject('Form_ContactUs');
        if (! $controller->getRequest()->isPost()) {
            $values = $this->_getDefaultFormValues();
            $form->populate($values);
        }
        else {
            $values = $controller->getRequest()->getParams();  
            $form->populate($values);         
            if ($form->isValid($values)) {
                $this->_sendEmail($values);
                $controller->view->ContactUs_Success = TRUE;
				
            }
            else {
                $form->populate($values);
                $controller->view->ContactUs_Error = TRUE;
            }
        }
		//print_r($form);exit;
        $controller->view->ContactUs_Form = $form;
        $response = $controller->view->render($this->getOption('viewScript'));
        return $response;
    }
    
    /**
     * getter for supportEmail option
     * @return string
     */
    public function getSupportEmail()
    {
        $email = $this->_options['supportEmail'];
        if ($email === NULL) {
            $email = Zend_Registry::get('config')->email->support;
            $this->_options['supportEmail'] = $email;
        }
        return $email;
    }
    
    /**
     * getter for supportName option
     * @return string
     */
    public function getSupportName()
    {
        $name = $this->_options['supportName'];
        if ($name === NULL) {
            $name = Zend_Registry::get('config')->email->supportName;
            $this->_options['supportName'] = $name;
        }
        return $name;
    }
    
    /**
     * getter for robotEmail option
     * @return string
     */
    public function getRobotEmail()
    {
        $email = $this->_options['robotEmail'];
        if ($email === NULL) {
            $email = Zend_Registry::get('config')->email->reserve;
            $this->_options['robotEmail'] = $email;
        }
        return $email;
    }
    
    protected function _getDefaultFormValues()
    {
        return array();
    }
    
    /**
     * send email from customer to admin
     */
    protected function _sendEmail(array $values)
    {
        $view = $this->getActionController()->view;
        $view->clientName = $values['name'];
		$view->text = $values['text'];
		$view->mail = $values['email'];
        $view->siteName = Model_Service::factory('site')->getCurrentHost();
        $view->SupportName = $this->getSupportName();
        App_Mail::factory()
                ->addTo($this->getSupportEmail(), $this->getSupportName())
                ->setFrom(Zend_Registry::get('config')->emailSMTP->username, Zend_Registry::get('config')->www->siteName)
                ->setSubject ($view->render($this->getOption('mailSubjectScript')))
                ->setBodyHtml($view->render($this->getOption('mailHtmlScript')))
                ->setBodyText($view->render($this->getOption('mailTextScript')))
                ->send()
                ;
    }
    
}

