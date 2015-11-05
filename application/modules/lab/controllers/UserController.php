<?php

class Lab_UserController extends Zend_Controller_Action
{
    protected $_defaultInjections = array(
        'Form_Register' => 'Lab_Form_Register',
        'Form_Quick'   => 'Lab_Form_OrderQuickUser',
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
        App_Event::factory('Lab_Controller__init', array($this))->dispatch();
    }

    public function registerAction()
    {
            if ($this->getRequest()->isPost()) {
                /** @var $userService Model_Service_User */
                $userService = Model_Service::factory('user');

                $form = $this->getInjector()->getObject('Form_Quick');

                $params = $this->getRequest()->getParams();
                $form->populate($params);
                $isValid = $form->isValid($params);

                $values = array();
                foreach($form->getElements() as $element){
                    $values[$element->getName()] = $element->getValue();
                }

                if ($isValid){
                    try {
                        $ar['name'] = $params['name'];
                        $ar['firstname'] = $params['name'];
                        $ar['fathersname'] = $params['name'];
                        $ar['lastname'] = $params['name'];
                        $ar['email'] = $params['email'];
                        $ar['login'] = $params['email'];
                        $ar['password'] = $params['email'];
                        $ar['address'] = $params['city'];
                        $ar['tel'] = $params['telephone'];
                        $ar['guid'] = App_Uuid::get();
                        $ar['export'] = 1;

                        if (!in_array($params['email'], Model_Service::factory('user')->getAllEmails()))
                            $user = $userService->registerNewUser($ar);
                        else {
                            $user = $userService->getComplexActiveByLogin($params['email']);
                            $ar['id'] = $user->id;
                            $userService->saveFromValues($ar);
                        }
                        Model_Service::factory('user')->authorize($params['email'], $params['email'], TRUE);
                        $success = TRUE;
                    } catch (Exception $e) {
                        $success = FALSE;
                    }
                } else {
                    $errors = array();
                    foreach($form->getMessages() as $key => $messages)
                        foreach($messages as $k => $message)
                            $errors[$key][] = $this->view->translate($key.'.'.$k);

                    $this->_helper->json(array('success' => TRUE, 'valid' => FALSE, 'errors' => $errors, 'values' => $values));
                }

                $this->_helper->json(
                    array(
                        'success'    => $success,
                        'authorized' => $userService->isAuthorized(),
                        'values'     => $values
                    )
                );
            }
        $this->_helper->json(array('success' => FALSE));
    }

    public function changeLanguageAction()
    {
        $this->_forward('change-language', 'user', 'kernel', $this->getRequest()->getParams());
    }

}