<?php

class Model_Object_User extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'id',
            'sort',
            'status',
            'name',
            'login',
            'password',
            'dob',
            'email',
            'rc_id', 'rc_id_filename', 'rc_id_preview', 'rc_id_preview2', 'rc_id_preview3',
            'role_id', 'role_name', 'role_acl_role', 'role_param1',
            'rows_per_page',
            'binding', 'binded_count',
            'personal_discount',
            'last_login', 'login_count',
            'tel', 'address',
            'where_know',
            'firstname', 'fathersname', 'lastname',
            'bonus_account',
            'comment',
            'guid',
            'date_added',
            'date_changed',
            'changer_id',
            'export',
        ));
    }

    public function getAcl_role()
    {
        return $this->role_acl_role;
    }

    /**
     * Return or set user guid
     *
     * @return mixed
     * @throws Model_Service_Exception
     */
    public function getGuid()
    {
        /** @var $service Model_Service_User */
        $service = Model_Service::factory('user');
        if (null == $this->_elements['guid']) {
            $this->_elements['guid'] = App_Uuid::get();
            $service->getMapper()->setUserGuid($this->_elements['guid'], $this->_elements['id']);
        }

        return $this->_elements['guid'];
    }

}