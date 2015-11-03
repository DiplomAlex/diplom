<?php

class Social_MailController  extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('Social_Controller__init', array($this));
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        }
    }

    public function indexAction()
    {
        if ( ! $this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('ReturnUrl')->remember();
        }
        $this->view->showNewButton = TRUE;
        $senderId = $this->_getParam('sender_id');
        $recipientId = $this->_getParam('recipient_id');
        if ( ! $recipientId AND ( ! $recipientId = Model_Service::factory('user')->getCurrent()->manager_id)) {
            //$recipientId = Model_Service::factory('user')->getCurrent()->id;
            $this->view->emailSupport = Zend_Registry::get('config')->email->support;
            $this->view->error = TRUE;
        }
        else {
            $talking = $this->_getParam('talking');
            $this->view->recipient = Model_Service::factory('user')->getComplex($recipientId);
            $this->view->mails = Model_Service::factory('social/mail')->getCorrespondence($senderId, $recipientId, NULL, $talking, TRUE);
        }
    }

    public function newAction()
    {
        $userService = Model_Service::factory('user');
        $authKey = $this->_getParam('authkey');
        if ( ! empty($authKey)) {
            if ( ! $user = $userService->getOneByAuthKey($authKey)) {
                throw new Zend_Controller_Exception('user with authkey="'.$authKey.'" not found');
            }
            else {
                $userService->authorize($user->login, NULL, TRUE);
            }
        }
        if ( ! $userService->isAuthorized()) {
            throw new Zend_Controller_Exception('Unauthorized access not allowed here!');
        }

        $this->view->menu()->setTopMenuPages(array(
            array(
                'label' => $this->view->translate('Переписка'),
                'uri' => $this->view->url(array(), 'social_mail_index'),
                'pages' => array(
                    array(
                        'label' => $this->view->translate('Написать письмо'),
                        'uri' => $this->view->url(),
                        'active' => TRUE,
                    )
                ),
            ),
        ));

        $service = Model_Service::factory('social/mail');
        $sender = Zend_Auth::getInstance()->getIdentity();
        $recipientId = $this->_getParam('recipient_id');
        $subject = $this->_getParam('subject');
        $talking = $this->_getParam('talking');
        $parentId = $this->_getParam('parent_id');
        if ( ! $recipientId) {
            $recipientId = Model_Service::factory('user')->getCurrent()->manager_id;
        }
        $this->view->recipient = Model_Service::factory('user')->getComplex($recipientId);
        // init form
        $form = new Social_Form_MailNew;
        if ( ! $this->getRequest()->isPost()) {
        // if it was just called (via get)
            $values = $service->newMessage($sender->id, $recipientId, $subject, $talking, $parentId)->toArray();
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
            $this->view->success = FALSE;
            return;
        }
        else if ($form->getAnswer() == 'cancel') {
        // if 'cancel' was pressed - get away
            $this->view->message = $this->view->translate('Message cancelled');
            $this->view->success = TRUE;
            return;
        }
        else {
        // if the form was posted
            $values = $this->getRequest()->getParams();
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
        }
        // validate it
        if ( ! $form->isValid($values)) {
            $this->view->message = $this->view->translate('Error sending message');
            $this->view->success = FALSE;
            return;
        }
        // save
        $service->send($values);
        $this->view->message = $this->view->translate('Message was sent');
        $this->view->success = TRUE;
        if ( ! $this->getRequest()->isXmlHttpRequest()) {
            if ( ! $url = $this->getHelper('ReturnUrl')->get()) {
                $url = $this->view->url(array('recipient_id'=>$recipientId), 'social_mail_index');
            }
            /*App_Debug::dump($url);*/
            $this->getHelper('Redirector')->gotoUrlAndExit($url);
        }
    }

}