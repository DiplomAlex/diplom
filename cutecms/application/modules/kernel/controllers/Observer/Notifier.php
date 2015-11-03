<?php

class Observer_Notifier extends App_Event_Observer
{


    /**
     * send emails to subscribers
     */
    public function onAfterSaveNews()
    {
        $data = $this->getEvent()->getData();
        $object = $data[0];
        if ( (int) $object->send_to_subscribers > 0) {
            $complexObject = Model_Service::factory('news')->getComplex($object->id);
            //$this->_sendNewsToSubscribers($complexObject);
            $object->is_new = FALSE;
        }
        return $object;
    }

    protected function _sendNewsToSubscribers($news)
    {
        $subscribedUsers = Model_Service::factory('news-topic')->getSubscribersList(5);
        foreach($subscribedUsers as $subscribedUser){
            $emailArray1[] = $subscribedUser['email'];
        }
        $subscribedEmails = Model_Service::factory('news-topic')->getEmailSubscribersList();
        foreach($subscribedEmails as $subscribedEmail){
            $emailArray2[] = $subscribedEmail['ens_email'];
        }
        $emailsArray = array_merge($emailArray1, $emailArray2);
        $emailsArray = array_unique($emailsArray);
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
        $config = Zend_Registry::get('config');
        $viewData = array(
            'siteName' => $config->www->siteName,
            'siteHref' => $config->www->siteHref,
            'news' => $news,
        );
        $subj = $view->partial('admin-news/mail-news-to-subscribers-subj.phtml', $viewData);
        $emailService = Model_Service::factory('email-queue');
        foreach ($emailsArray as $singleEmail) {
            $viewData['unsubscribeLink'] = $view->stdUrl(array(), 'unsubscribe', 'user', 'shop');
            $viewData['email'] = $singleEmail;
            $bodyHtml = $view->partial('admin-news/mail-news-to-subscribers-html.phtml', $viewData);
            $bodyText = $view->partial('admin-news/mail-news-to-subscribers-text.phtml', $viewData);
            $email = $emailService->create();
            $email->from = $config->email->support;
            $email->from_name = $config->email->supportName;
            $email->to = $singleEmail;
            $email->to_name = $singleEmail;
            $email->subject = $subj;
            $email->body_text = $bodyText;
            $email->body_html = $bodyHtml;
            $emailService->addToQueue($email);
        }
    }


}

