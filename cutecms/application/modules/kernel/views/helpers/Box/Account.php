<?php

class View_Helper_Box_Account extends Zend_View_Helper_Abstract
{

    public function box_Account()
    {
        $service = Model_Service::factory('user');
        if ($service->isAuthorized()) {
            $user = $service->getCurrent();
            $bindedUsers = array($user->id => $user->name.' ('.$user->login.')');
            if ($bindeds = $service->getBindedUsers($user)) {
                foreach ($bindeds as $binded) {
                    $bindedUsers[$binded->id] = $binded->name.' ('.$binded->login.')';
                }
            }
        }
        else {
            $user = NULL;
            $bindedUsers = array();
        }
        $html = $this->view->partial('box/account.phtml', array('user' => $user, 'bindedUsers' => $bindedUsers));
        return $html;
    }

}