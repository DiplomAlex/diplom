<?php

class Issues_Observer_Notifier extends App_Event_Observer
{
    
    private static $_isNewIssue = NULL;
    
    public function onBeforeSaveIssue()
    {
        $object = $this->getData(0);
        if ( ! $object->id) {
            self::$_isNewIssue = TRUE;
        }
    }
    
    public function onAfterSaveIssue()
    {
        if (self::$_isNewIssue === TRUE) {
            $object = $this->getData(0);
            $this->_sendNotification($object);
            self::$_isNewIssue = NULL;
        }
    }
    
    private function _fetchFirstEmailFromString($str)
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
    
    private function _sendNotification(Model_Object_Interface $issue)
    {
        $svcUser = Model_Service::factory('user');
        $svcIssue = Model_Service::factory('issues/issue');
        $config = Zend_Registry::get('config');
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
        $emailService = Model_Service::factory('email-queue');
        $recipients = array();
        /* add emails of all users of issue */
        $users = $svcIssue->getIssueUsersList($issue->id, TRUE);
        foreach ($users as $user) {
            $email = $this->_fetchFirstEmailFromString($user['email']);
            if ($email) {
                $recipients[$email] = $user['name'];
            }
        }
        if ( ! empty($recipients)) {
            $subject = $view->translate('Добавлена новая задача N%1$s', $issue->id);
            $bodyHtml = $view->partial('email/new-issue.html.phtml', array('issue'=>$issue, 'config'=>$config));
            $bodyText = $view->partial('email/new-issue.txt.phtml', array('issue'=>$issue, 'config'=>$config));
            foreach ($recipients as $rcpEmail=>$rcpName) {
                $email = $emailService->create();
                $email->from = $config->email->support;
                $email->from_name = $config->email->supportName;
                $email->to = $rcpEmail;
                $email->to_name = $rcpName;
                $email->subject = $subject;
                $email->body_html = $bodyHtml;
                $email->body_text = $bodyText;
                $emailService->addToQueue($email);
            }
        }
        else {
            App_Debug::log('no recipients', __CLASS__.'::'.__FUNCTION__);
            App_Debug::log($issue->toArray(), __CLASS__.'::'.__FUNCTION__.' - $issue');
        }
        
    }
    
}
