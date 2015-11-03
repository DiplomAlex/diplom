<?php

class App_Acl extends Zend_Acl
{

    protected $_privileges = array(
        'create',
        'read',
        'update',
        'delete',
        'exec',

        'foreign_create',
        'foreign_read',
        'foreign_update',
        'foreign_delete',
        'foreign_exec',
    );


    public function addRoles($roles)
    {
        foreach ($roles as $role=>$parent) {
            if (empty($parent)) {
                $parent = NULL;
            }
            $this->addRole(new Zend_Acl_Role($role), $parent);
        }
    }

    public function addResources($resources)
    {
            foreach ($resources as $resource => $parent) {
                if (empty($parent)) {
                    $parent = NULL;
                }
                $this->add(new Zend_Acl_Resource($resource), $parent);
            }

    }

    public function addAllows($allows)
    {
        // add access rules  (allow,deny)
        if ($allows) {
            foreach ($allows as $role=>$rules) {
                foreach ($rules as $value) {
                    @list($resource, $privileges) = explode('|', $value);
                    if ( ! empty($privileges)) {
                        $privileges = explode(',', $privileges);
                        if ( ! count($privileges)) {
                            $privileges = $this->_privileges;
                        }
                    }
                    else {
                        $privileges = NULL;
                    }
                    $this->allow($role, $resource, $privileges);
                }
            }
        }
    }


    public function addDenies($denies)
    {
        if ($denies) {
            foreach ($denies as $role=>$rules) {
                foreach ($rules as $value) {
                    @list($resource, $privileges) = explode('|', $value);
                    $privileges = explode(',', $privileges);
                    if ( ! count($privileges)) {
                        $privileges = $this->_privileges;
                    }
                    $this->deny($role, $resource, $privileges);
                }
            }
        }
    }

}