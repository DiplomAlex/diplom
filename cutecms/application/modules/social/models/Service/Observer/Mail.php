<?php

class Social_Model_Service_Observer_Mail extends App_Event_Observer
{

    public function onGetNewMailsCount()
    {
        $count = Model_Service::factory('social/mail')->getNewMailsCount();
        $this->getEvent()->setFired()->setData($count);
    }

}