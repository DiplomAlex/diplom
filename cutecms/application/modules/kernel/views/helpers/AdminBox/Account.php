<?php

class View_Helper_AdminBox_Account extends Zend_View_Helper_Abstract
{

    public function adminBox_Account()
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
        $isAllowedAccountEdit = $service->isAllowedByAcl(NULL, 'AdminUserController', 'update');
        $html = $this->view->partial('admin-box/account.phtml', array('user' => $user, 
        															  'bindedUsers' => $bindedUsers,
                                                                      'isAllowedAccountEdit' => $isAllowedAccountEdit));
        return $html;
    }

}