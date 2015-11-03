<?php

class Model_Object_Observer_Acl extends App_Event_Observer
{

    /**
     * checks if current logged user can insert/udpate this object
     * @param Model_Object_Interface
     * @return $this
     */
    public function onBeforeSave()
    {
        $object = $this->getData(0);

        if (PHP_SAPI=='cli') {
            return $object;
        }

        $resource = get_class($object);
        $userService = Model_Service::factory('user');
        $user = $userService->getCurrent();
        if ( ! $user OR  ! ($role = $user->acl_role)) {
            $role = 'guest';
        }

        $privPrefix = '';
        if (( ! $object->hasElement('adder_id')) OR ($user->id != $object->adder_id)) {
            $privPrefix = 'foreign_';
        }
        $privilege = $privPrefix;
        if ($object->id) {
             $privilege .= 'update';
        }
        else {
            $privilege .= 'create';
        }
        /*if ( ! Zend_Registry::get('Zend_Acl')->isAllowed($role, $resource, $privilege)) {
            throw new Model_Object_Exception(get_class($this).' says that object cannot be saved by current user: $acl->isAllowed('.$role.', '.$resource.', '.$privilege.')');
        }*/
        if ( ! $userService->isAllowedByAcl($user, $resource, $privilege)) {
            throw new Model_Object_Exception(get_class($this).' says that object cannot be saved by current user: $acl->isAllowed('.$role.', '.$resource.', '.$privilege.')');
        }
        return $object;
    }


    /**
     * checks if current logged user can delete this object
     * @param Model_Object_Interface
     * @return $this
     */
    public function onDelete()
    {
        $object = $this->getData(0);

        if (PHP_SAPI=='cli') {
            return $object;
        }

        $resource = get_class($object);
        $user = Zend_Auth::getInstance()->getIdentity();
        if ( ! $user OR  ! ($role = $user->acl_role)) {
            $role = 'guest';
        }
        $privPrefix = '';
        if (( ! $object->hasElement('adder_id')) OR ($user->id != $object->adder_id)) {
            $privPrefix = 'foreign_';
        }
        $privilege = $privPrefix . 'delete';
        if ( ! Zend_Registry::get('Zend_Acl')->isAllowed($role, $resource, $privilege)) {
            throw new Model_Object_Exception(get_class($this).' says that object cannot be deleted by current user: $acl->isAllowed('.$role.', '.$resource.', '.$privilege.')');
        }
        return $object;
    }



    /**
     * checks if current logged user can edit this user
     * @param Model_Object_Interface
     * @return $this
     */
    public function onBeforeSaveUser()
    {
        $object = $this->getData(0);

        if (PHP_SAPI=='cli') {
            return $object;
        }

        $resource = get_class($object);
        $user = Zend_Auth::getInstance()->getIdentity();
        if ( ! $user OR  ! ($role = $user->acl_role)) {
            $role = 'guest';
        }
        $privPrefix = '';
        if (@$user->id != $object->id) { // difference from onBeforeSave is here: $object->id instead of $object->adder_id
            $privPrefix = 'foreign_';
        }
        $privilege = $privPrefix;
        if ($object->id) {
             $privilege .= 'update';
        }
        else {
            $privilege .= 'create';
        }
        if ( ! Zend_Registry::get('Zend_Acl')->isAllowed($role, $resource, $privilege)) {
            throw new Model_Object_Exception(get_class($this).' says that object '.get_class($object).' cannot be saved by currently logged user: $acl->isAllowed('.$role.', '.$resource.', '.$privilege.')');
        }
        return $object;
    }


}
