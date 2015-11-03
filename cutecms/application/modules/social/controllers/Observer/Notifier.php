<?php

class Social_Observer_Notifier extends App_Event_Observer
{

    public function onAfterSend()
    {
        $data = $this->getEvent()->getData();
        $obj = $data[0];
        $this->_sendNotificationMail($obj);
        return $obj;
    }

    protected function _fetchFirstEmailFromString($str)
    {
        $regexp = '/^((\"[^\"\f\n\r\t\b]+\")|([A-Za-z0-9_][A-Za-z0-9_\!\#\$\%\&\'\*\+\-\~\/\=\?\^\`\|\{\}]*(\.[A-Za-z0-9_\!\#\$\%\&\'\*\+\-\~\/\=\?\^\`\|\{\}]*)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9])(([A-Za-z0-9\-])*([A-Za-z0-9]))?(\.(?=[A-Za-z0-9\\-]))?)+[A-Za-z]+))$/D';
        $count = preg_match($regexp, $str, $matches);
        if ( ! $count) {
            $result = FALSE;
        }
        else {
            $result = $matches[0];
        }
        return $result;
    }

    protected function _sendNotificationMail(Model_Object_Interface $msg)
    {
        $userService = Model_Service::factory('user');
        $config = Zend_Registry::get('config');
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
        $emailService = Model_Service::factory('email-queue');
        $recipient = $userService->getComplex($msg->recipient_id);
        $sender = $userService->getComplex($msg->sender_id);

        $email = $emailService->create();
        $email->from = $config->email->support;
        $email->from_name = $config->email->supportName;
        $email->to = $this->_fetchFirstEmailFromString($recipient->email);
        $email->to_name = $recipient->name;
        $email->subject = $view->translate('Уведомление о сообщении');
        $params = array('email'=>$email,
                        'msg'=>$msg,
                        'recipient'=>$recipient,
                        'authKey'=>$userService->getAuthKey($recipient),
                        'sender'=>$sender,
                        'host'=>'http://'.Model_Service::factory('site')->getCurrentHost(),);
        if ($recipient->acl_role == 'manager') {
            $email->body_html = $view->partial('mail/notification-email-to-manager-html.phtml', $params);
            $email->body_text = $view->partial('mail/notification-email-to-manager-txt.phtml', $params);
            $addon = $view->translate('от клиента менеджеру');
        }
        else if ($recipient->acl_role == 'client') {
            $email->body_html = $view->partial('mail/notification-email-to-client-html.phtml', $params);
            $email->body_text = $view->partial('mail/notification-email-to-client-txt.phtml', $params);
            $addon = $view->translate('от менеджера клиенту');
        }
        if ( ! empty($email->body_html)) {
            if ( ! empty($email->to)) {
                $emailService->addToQueue($email);
            }
            /**
             * send copy to reserve
             */
            $email->id = NULL;
            $email->subject = $email->subject . ' ' . $addon;
            $email->to = $config->email->reserve;
            $email->to_name = $config->email->reserveName;
            $emailService->addToQueue($email);
        }
    }



}