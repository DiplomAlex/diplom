<?php

class Model_Object_Observer_Stats extends App_Event_Observer
{

    public function onAfterLoginUser()
    {
        $user = $this->getData(0);
        $user->login_count = (int) $user->login_count + 1;
        $user->last_login = date('Y-m-d H:i:s');
        Model_Service::factory('user')->save($user);
    }


}