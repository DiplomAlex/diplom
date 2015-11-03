<?php

class View_Helper_RoleViewAlias extends Zend_View_Helper_Abstract
{

    public function roleViewAlias($role = NULL)
    {
        if ($role === NULL) {
            $result = 'guest';
            if ($user = Zend_Auth::getInstance()->getIdentity()) {
                if ($acl_role = $user->acl_role) {
                    $result = Zend_Registry::get('config')->roleViewAlias->{$acl_role};
                }
            }
        }
        else {
            $result = Zend_Registry::get('config')->roleViewAlias->{$role};
        }
        return $result;
    }

}

