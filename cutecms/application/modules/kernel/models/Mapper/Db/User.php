<?php

class Model_Mapper_Db_User extends Model_Mapper_Db_Abstract implements Model_Mapper_User
{

	protected $_defaultInjections = array(
		'Model_Db_Table_Interface' => 'Model_Db_Table_Users',
        'Model_Db_Table_Resources',
        'Model_Db_Table_Roles',
        'Model_Db_Table_UsersRoles',

        'Model_Collection_Interface' => 'Model_Collection_User',
        'Model_Object_Interface' => 'Model_Object_User',

        'Model_Mapper_Db_Role',
        'Model_Mapper_Db_Plugin_Filter_User',
        'Model_Mapper_Db_Plugin_Resource',
        'Model_Mapper_Db_Plugin_Sorting',
	);


    public function init()
    {
        $this
            ->addPlugin('Resource',$this->getInjector()->getObject('Model_Mapper_Db_Plugin_Resource', array('rc_id'), 1))
            ->addPlugin('Sorting', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Sorting'))
            ->addPlugin('Filters', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Filter_User',
                array(
                    'filter_status',
                    'filter_login',
                    'filter_name',
                    'filter_email',
                    'filter_role_id',
                    'filter_date_added',
                ),
                array(
                    'filter_date_added' => array('filter_date_added_from', 'filter_date_added_to'),
                )
            ))
        ;
    }



    /**
     * addons for complex select
     * @param Zend_Db_Select
     * @return Zend_Db_Select
     */
    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        $lang = Model_Service::factory('language')->getCurrent();

        $select -> joinLeft(
                        array('role'),
                        'user_role_id = role_id ',
                        array(
                            'user_role_acl_role' => 'role_acl_role',
                            'user_role_param1' => 'role_param1', 
                        )
                   )
                -> joinLeft(
                        array('role_description'),
                        'user_role_id = role_desc_role_id '
                            . ' AND role_desc_language_id = '.$lang->id,
                        array(
                            'user_role_name' => 'role_desc_name',
                        )
                   )
                ;

        return $select;
    }

    /**
     * addon actions when building complex object
     * @param Model_Object_Interface $object
     * @param array $values
     * @return Model_Object_Interface
     */
    protected function _onBuildComplexObject(Model_Object_Interface $object, array $values = NULL, $addedPrefix = TRUE)
    {
        $object->role_acl_role = $values['user_role_acl_role'];
        return $object;
    }



    /**
     * @param Model_Object_Interface
     * @param array
     * @return Model_Object_Interface
     */
    protected function _postSaveComplex(Model_Object_Interface $user, array $values)
    {
        if (isset($values['roles'])) {
            $this->bindRoles($user, $values['roles']);
        }
        return $user;
    }




    /**
     * get user by login
     * @return Model_Object_User
     */
    public function fetchOneByLogin($login)
    {
        if ( ! $login) {
            $this->_throwException('login should be set');
        }

        /**
         * @todo calling from mapper to service should be replaced
         */
        $lang = Model_Service::factory('language')->getCurrent();

        $select = $this->getTable()->select()->setIntegrityCheck(FALSE)
                                 ->from($this->getTable()->info('name'), '*')

                                -> joinLeft(
                                        array('role'),
                                        'user_role_id = role_id ',
                                        array(
                                            'user_role_acl_role' => 'role_acl_role',
                                        )
                                   )
                                -> joinLeft(
                                        array('role_description'),
                                        'user_role_id = role_desc_role_id '
                                            . ' AND role_desc_language_id = '.$lang->id,
                                        array(
                                            'user_role_name' => 'role_desc_name',
                                        )
                                   )
                                -> joinLeft(
                                        array('resource'),
                                        'user_rc_id = rc_id',
                                        array(
                                            'resource_rc_id_id' => 'rc_id',
                                            'resource_rc_id_filename' => 'rc_filename',
                                            'resource_rc_id_preview' => 'rc_preview',
                                        )
                                   )


                                 ->where('user_login = ?', $login)
                                 ;
        $rows = $select->query()->fetchAll();

        if ( ! $rows) {
            $this->_throwException('user with login="'.$login.'" not found!');
        }
        $row = current($rows);
        $object = $this->makeComplexObject($row);
        return $object;
    }


    /**
     * get one user by email
     * @param string
     * @return Model_Object_Interface
     */
    public function fetchOneByEmail($email)
    {
        if (empty($email)) {
            $result = FALSE;
        }
        else if (($result = $this->fetchComplex(array('user_email = ?'=>$email))) AND ($result->count() > 0)) {
            $result = $result->current();
        }
        else {
            $result = FALSE;
        }
        return $result;
    }


    /**
     * get one user by auth_key
     * @param string
     * @return Model_Object_Interface
     */
    public function fetchOneByAuthKey($key)
    {
        if (empty($key)) {
            $result = FALSE;
        }
        else if (($result = $this->fetchComplex(array('md5(concat(user.user_login, user.user_password)) = ?'=>$key))) AND ($result->count() > 0)) {
            $result = $result->current();
        }
        else {
            $result = FALSE;
        }
        return $result;
    }


    /**
     * @param Model_Object_User
     * @param array - role ids to bind
     */
    public function bindRoles(Model_Object_Interface $user, array $roles)
    {
        $this->getTable('users-roles')->delete(array('ur_user_id = ?'=>$user->id));
        $defaultAdded = FALSE;
        foreach ($roles as $role) {
            if ($role == $user->role_id) {
                $defaultAdded = TRUE;
            }
            $this->getTable('users-roles')->insert(array(
                'ur_user_id' => $user->id,
                'ur_role_id' => $role,
            ));
        }
        if ($defaultAdded === FALSE) {
            $this->getTable('users-roles')->insert(array(
                'ur_user_id' => $user->id,
                'ur_role_id' => $user->role_id,
            ));
        }
    }

    /**
     * @param Model_Object_User
     * @return Model_Collection_Role
     */
    public function fetchBindedRoles(Model_Object_Interface $user)
    {
        $langId = Model_Service::factory('language')->getCurrent()->id;
        $select = $this->getTable('roles')
                                ->select()->setIntegrityCheck(FALSE)
                                ->from('role', $this->getTable('roles')->info('cols'))
                                ->joinLeft('user_role', 'ur_role_id = role_id', array())
                                ->joinLeft(array('rd'=>'role_description'),
                                           'role_desc_role_id = role_id AND role_desc_language_id = '.$langId,
                                           array('role_name'=>'role_desc_name'))
                                ->where('ur_user_id = ?', $user->id)
                                ;
        return $this->getMapper('role')->makeSimpleCollection($select->query()->fetchAll());
    }


    /**
     * @param array - ids of users
     * @param string - binding id
     */
    public function bindUsersByIdArray(array $ids, $binding)
    {
        if (count($ids) > 1) {
            $this->getTable()->update(array('user_binding'=>$binding), array('user_id IN (?)'=>$ids));
            $this->getTable()->getAdapter()->query('
                UPDATE `user`
                LEFT JOIN (
                    SELECT `user_binding` as `binding` , COUNT( user_id ) AS `cnt_binded`
                    FROM `user`
                    GROUP BY `user_binding`
                ) AS `binded`
                ON binding = user_binding
                SET user_binded_count = cnt_binded
            ');

        }
    }


    /**
     * @param Model_Object_Interface
     * @return Model_Collection_Interface
     */
    public function fetchBindedUsers(Model_Object_Interface $user, $fetch = TRUE)
    {
        if (empty($user->binding)) {
            $result = FALSE;
        }
        else {
            $where = array('user.user_binding = ?' => $user->binding,);
            $select = $this->fetchComplex($where, FALSE);
            if ($fetch === TRUE) {
                $result = $this->makeComplexCollection($select->query()->fetchAll());
            }
            else {
                $result = $select;
            }
        }
        return $result;
    }

    public function paginatorFetchBindedUsers($user, $rowsPerPage, $page)
    {
        $query = $this->fetchBindedUsers($user, FALSE);
        return $this->paginator($query,  $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
    }



    /**
     * get all users by acl_role
     * @param mixed array|string
     * @return mixed Zend_Db_Select | Model_Collection_Interface
     */
    public function fetchByAclRole($role, $activeOnly = FALSE)
    {
        $select = $this->fetchComplex(NULL, FALSE)
                       ->distinct()
                       ->joinLeft(array('ur'=>'user_role'), 'ur_user_id = user.user_id', array())
                       ->joinLeft(array('addon_role'=>'role'), 'addon_role.role_id = ur_role_id', array())
                       ;
        if (is_array($role)) {
            $select->where('role.role_acl_role IN (?) OR addon_role.role_acl_role IN (?)', $role);
        }
        else {
            $select->where('role.role_acl_role = ? OR addon_role.role_acl_role = ?', $role);
        }
        if ($activeOnly === TRUE) {
            $select->where('user.user_status > 0');
        }
        $select->reset('order')->order('user.user_name ASC');
        return $this->makeComplexCollection($select->query()->fetchAll());
    }

    /**
     * Отметить выгруженых пользователей
     *
     * @param $startTime
     * @return $this
     */
    public function clearExport($startTime)
    {
        $this->getTable()->update(
            array('user_export' => '0'),
            array('user_export' => '1',
                  'user.user_date_changed < ? OR isnull(user.user_date_changed)' => date('Y-m-d H:i:s', $startTime))
        );

        return $this;
    }

    /**
     * Fetch user by guid
     *
     * @param $guid
     *
     * @return Model_Object_Interface
     */
    public function fetchUserByGuid($guid)
    {
        $res = $this->getTable()->select()
            ->where('user_guid = ?', (string)$guid)
            ->query()
            ->fetch();

        if ($res) return $this->makeComplexObject($res);
        else return null;
    }

    /**
     * Set user guid
     *
     * @param $guid
     * @param $uid
     *
     * @return int
     */
    public function setUserGuid($guid, $uid)
    {
        return $this->getTable()->update(array('user_guid' => $guid), array('user_id = ?' => $uid));
    }
}
