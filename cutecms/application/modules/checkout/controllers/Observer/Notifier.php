<?php

class Checkout_Observer_Notifier extends App_Event_Observer
{
    
    protected static $_isNewOrder = FALSE;
    
    public function onBeforeSaveOrder()
    {
        $order = $this->getEvent()->getData();
        $order = $order[0];
        if (empty($order->id)) {
            self::$_isNewOrder = TRUE;
        }
        else {
            self::$_isNewOrder = FALSE;
        }
    }
    
    public function onAfterSaveOrder()
    {
        $order = $this->getEvent()->getData();
        $order = $order[0];
                
        $userService = Model_Service::factory('user');
        
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
        $config = Zend_Registry::get('config');
        $view->order = $order;
        $view->siteName = $config->www->siteName;
        $view->siteHref = $config->www->siteHref;
        $emailFrom = $config->email->support;
        if (empty($emailFrom)) {
            throw new Zend_Controller_Action_Exception('$emailFrom is empty');
        }
        $nameFrom = $config->email->supportName;
        $emailClient = $order->client_email;
        if (empty($emailClient)) {
            throw new Zend_Controller_Action_Exception('$emailClient is empty');
        }
        $nameClient = $order->client_name;
        $emailAdmin = $config->email->reserve;
        if (empty($emailAdmin)) {
            throw new Zend_Controller_Action_Exception('$emailAdmin is empty');
        }            
        $nameAdmin = $config->email->reserveName;
               
        
        if (self::$_isNewOrder) {

            /* email to client */
            if(count($order->items)>0){
                $subjToClient = $view->translate('Вами оформлен заказ в магазине '.$config->www->siteName);
                $bodyToClient = $view->render('order/mail/to-client-order-new-html.phtml');
            } else {
                $subjToClient = $view->translate('Регистрация на сайте '.$config->www->siteName);
                $bodyToClient = $view->render('order/mail/to-client-order-user-html.phtml');
            }
            App_Mail::factory()
                ->setBodyHtml($bodyToClient)
                ->setFrom($emailFrom, $nameFrom)
                ->addTo($emailClient, $nameClient)
                ->setSubject($subjToClient)
                ->send();            
            
            /* email to admin */
            if(count($order->items)>0){
                $subjToAdmin = $view->translate('Оформлен заказ в магазине '.$config->www->siteName);
                $bodyToAdmin = $view->render('order/mail/to-admin-order-new-html.phtml');
            } else {
                $subjToAdmin = $view->translate('Регистрация нового пользователя на сайте '.$config->www->siteName);
                $bodyToAdmin = $view->render('order/mail/to-admin-order-user-html.phtml');
                //$result = $order->client_comment;
                //$bodyToAdmin = $result;
            }
            App_Mail::factory()
                ->setBodyHtml($bodyToAdmin)
                ->setFrom($emailFrom, $nameFrom)
                ->addTo($emailAdmin, $nameAdmin)
                ->setSubject($subjToAdmin)
                ->send();            
        }
        else if ($order->send_mail_to_client) {
            /* email to client */
            $subjToClient = $view->translate('Обновлен Ваш заказ #'.$order->id.' в магазине '.$config->www->siteName);
            $bodyToClient = $view->render('order/mail/to-client-order-update-html.phtml');
            App_Mail::factory()
                ->setBodyHtml($bodyToClient)
                ->setFrom($emailFrom, $nameFrom)
                ->addTo($emailClient, $nameClient)
                ->setSubject($subjToClient)
                ->send();            
            
        }
        
        $order->send_mail_to_client = FALSE;
        
    }
    
    
    
}