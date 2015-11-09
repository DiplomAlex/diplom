<?php

class Lab_UserController extends Zend_Controller_Action
{
    protected $_defaultInjections = array(
        'Form_Register' => 'Lab_Form_Register',
//        'Form_Quick'   => 'Lab_Form_OrderQuickUser',
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
        App_Event::factory('Lab_Controller__init', array($this))->dispatch();
    }

    public function registerAction()
    {
        $this->view->headTitle('Регистрация');
        $config = Zend_Registry::get('config');
        $form = $this->getInjector()->getObject('Form_Register');
        $this->view->form = $form;
        $values = $this->getRequest()->getParams();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($values)) {
                $ar['name'] = $values['firstname'] . ' ' . $values['fathersname'] . ' ' . $values['lastname'];
                $ar['firstname'] = $values['firstname'];
                $ar['fathersname'] = $values['fathersname'];
                $ar['lastname'] = $values['lastname'];
                $ar['email'] = $values['email_address'];
                $ar['address'] = $values['city'];
                $ar['tel'] = $values['telephone'];
                $ar['password'] = $values['password'];
                $ar['login'] = $values['email_address'];

                $user = Model_Service::factory('user')->registerNewUser($ar);

                $this->view->userName = $ar['name'];
                $this->view->userLogin = $ar['login'];
                $this->view->userPassword = $ar['password'];
                $this->view->siteName = $config->www->siteName;
                $this->view->siteUrl = $this->view->stdUrl(array('reset' => TRUE));

//                App_Mail::factory()
//                    ->addTo($ar['email'], $ar['name'])
//                    ->setFrom($config->email->support, $config->email->supportName)
//                    ->setSubject($this->view->render('user/register/email-subj.phtml'))
//                    ->setBodyText($this->view->render('user/register/email-text.phtml'))
//                    ->setBodyHtml($this->view->render('user/register/email-html.phtml'))
//                    ->send();

                $this->view->success = TRUE;
            } else {
                $this->view->success = FALSE;
            }
        }
//            if ($this->getRequest()->isPost()) {
//                /** @var $userService Model_Service_User */
//                $userService = Model_Service::factory('user');
//
//                $form = $this->getInjector()->getObject('Form_Quick');
//
//                $params = $this->getRequest()->getParams();
//                $form->populate($params);
//                $isValid = $form->isValid($params);
//
//                $values = array();
//                foreach($form->getElements() as $element){
//                    $values[$element->getName()] = $element->getValue();
//                }
//
//                if ($isValid){
//                    try {
//                        $ar['name'] = $params['name'];
//                        $ar['firstname'] = $params['name'];
//                        $ar['fathersname'] = $params['name'];
//                        $ar['lastname'] = $params['name'];
//                        $ar['email'] = $params['email'];
//                        $ar['login'] = $params['email'];
//                        $ar['password'] = $params['email'];
//                        $ar['address'] = $params['city'];
//                        $ar['tel'] = $params['telephone'];
//                        $ar['guid'] = App_Uuid::get();
//
//                        if (!in_array($params['email'], Model_Service::factory('user')->getAllEmails()))
//                            $user = $userService->registerNewUser($ar);
//                        else {
//                            $user = $userService->getComplexActiveByLogin($params['email']);
//                            $ar['id'] = $user->id;
//                            $userService->saveFromValues($ar);
//                        }
//                        Model_Service::factory('user')->authorize($params['email'], $params['email'], TRUE);
//                        $success = TRUE;
//                    } catch (Exception $e) {
//                        $success = FALSE;
//                    }
//                } else {
//                    $errors = array();
//                    foreach($form->getMessages() as $key => $messages)
//                        foreach($messages as $k => $message)
//                            $errors[$key][] = $this->view->translate($key.'.'.$k);
//
//                    $this->_helper->json(array('success' => TRUE, 'valid' => FALSE, 'errors' => $errors, 'values' => $values));
//                }
//
//                $this->_helper->json(
//                    array(
//                        'success'    => $success,
//                        'authorized' => $userService->isAuthorized(),
//                        'values'     => $values
//                    )
//                );
//            }
//        $this->_helper->json(array('success' => FALSE));
    }

    public function changeLanguageAction()
    {
        $this->_forward('change-language', 'user', 'kernel', $this->getRequest()->getParams());
    }

}