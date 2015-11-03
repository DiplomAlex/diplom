<?php

class Model_Service_User extends Model_Service_Abstract
{

    const GEN_PASSWORD_LEN_MIN = 6;
    const GEN_PASSWORD_LEN_MAX = 10;

    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_User',
        'Model_Mapper_Interface' => 'Model_Mapper_Db_User',
    );

    /**
     * Proxy for IDE
     *
     * @param null $name
     *
     * @return Model_Mapper_Db_User
     */
    public function getMapper($name = null)
    {
        return parent::getMapper($name);
    }

    /**
     * trying to authorize with account data
     * @param string
     * @param string
     * @param bool - if TRUE - no password checking
     */
    public function authorize($login, $password, $force = FALSE)
    {
        try {
            if (empty($login)) {
                $this->_throwException('login is empty');
            }
            try {
                $user = $this->getMapper()->fetchOneByLogin($login);
            }
            catch(Model_Mapper_Exception $e) {
                $this->_throwException('wrong login '.$login);
            }
            if (($force === FALSE) AND ($user->password != $this->cryptPassword($password))) {
                $this->_throwException('wrong login '.$login);
            }
            if ( ! $this->isAllowedByIp($user)) {
                $this->_throwException('wrong ip');
            }
            if ( ! $this->isAllowedByLogin($user)) {
                $this->_throwException('wrong login');
            }
            Zend_Auth::getInstance()->getStorage()->write($user);
            App_Event::factory('Model_Object_User__onAfterLogin', array($user))->dispatch();
        }
        catch (Model_Exception $e) {
            $this->unauthorize();
            $this->_throwException($e->getMessage());
        }

        $_SESSION['isAuthorized'] = TRUE; // crutch for CKFinder - to disallow anonymous upload

        return TRUE;
    }

    public function reauthorize(Model_Object_Interface $newUser)
    {
        $oldUser = $this->getCurrent();
        if ($this->usersAreBinded($oldUser, $newUser)) {
            $this->authorize($newUser->login, NULL, TRUE);
        }
    }

    /**
     * checks if user is authorized now
     */
    public function isAuthorized()
    {
        return (bool) Zend_Auth::getInstance()->hasIdentity();
    }

    /**
     * gets current user
     *
     * TODO should be deleted?
     *
     */
    public function getCurrent()
    {
        return Zend_Auth::getInstance()->getIdentity();
    }

    /**
     * reload user to session (used when some data of user changed)
     */
    public function renewCurrent()
    {
        $user = $this->getComplex($this->getCurrent()->id);
        Zend_Auth::getInstance()->getStorage()->write($user);
        return $this;
    }

    /**
     * clear session
     */
    public function unauthorize()
    {
        App_Event::factory('Model_Object_User__onBeforeLogout', array($this->getCurrent()))->dispatch();
        return Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * hash password
     * @param string
     * @return string
     */
    public function cryptPassword($password)
    {
        return md5($password);
    }


    /**
     * get objects fields values for edit form
     * @return array
     */
    public function getEditFormValues($id)
    {
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        $rcs = $this->getMapper()->getPlugin('Resource')->fetchResources($obj);
        $rolesColl = $this->getBindedRoles($obj);
        $values['roles'] = array($obj->role_id);
        foreach ($rolesColl as $role) {
            $values['roles'][] = $role->id;
        }
        $values = $values + $rcs;
        return $values;
    }


    /**
     * get full info about user by login
     * @param string
     * @return Model_Object_Interface
     */
    public function getComplexActiveByLogin($login)
    {
        $user = $this->getMapper()->fetchOneByLogin($login);
        if ($user->status < 1) {
            throw new Model_Service_Exception('user with login "'.$login.'" is disabled');
        }
        return $user;
    }


    /**
     * just recieve all emails to array
     * @return array
     */
    public function getAllEmails()
    {
        return $this->getMapper()->fetchDistinctField('email');
    }



    /**
     * just recieve all logins to array
     * @return array
     */
    public function getAllLogins()
    {
        return $this->getMapper()->fetchDistinctField('login');
    }


    /**
     * save it
     * @param array
     */
    public function saveFromValues(array $values)
    {
        if (empty($values['password'])) {
            unset($values['password']);
        }
        else {
            $values['password'] = $this->cryptPassword($values['password']);
        }
        $this->getMapper()->saveComplex($values);
        if (array_key_exists('id', $values) AND (int) $values['id']) {
            Zend_Registry::get('Zend_Cache')->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('user_'.$values['id']));
        }
        return $this;
    }


    public function saveRowsPerPage(array $values)
    {
        if ($this->isAuthorized()) {
            $this->getCurrent()->rows_per_page = serialize($values);
            $this->save($this->getCurrent());
            $this->renewCurrent();
        }
    }



    /**
     * recover password for user by email
     * @param string
     */
    public function recoverPassword($email)
    {	
        try {
            $user = $this->getMapper()->fetchOneByEmail($email);
        }
        
        catch (Model_Exception $e) {
            $this->_throwException('user with email "'.$email.'" not found');
        }
        
        $password = $this->generatePassword($user);
        $user->password = $this->cryptPassword($password);
        $this->save($user);
        $user->password = $password;
        return $user;

    }

    public function generatePassword(Model_Object_Interface $user)
    {
        $hash = md5($user->login.$user->email);
        $max = strlen($hash)-1;
        $len = rand(self::GEN_PASSWORD_LEN_MIN, self::GEN_PASSWORD_LEN_MAX);
        $pass = '';
        for ($i = 0; $i<$len; $i++) {
            $idx = rand(0, $max);
            $pass .= $hash[$idx];
        }
        return $pass;
    }


    public function getAuthKey(Model_Object_Interface $user)
    {
        $key = md5($user->login.$user->password);
        return $key;
    }

    public function getOneByAuthKey($key)
    {
        return $this->getMapper()->fetchOneByAuthKey($key);
    }

    public function isAllowedByAcl(Model_Object_Interface $user = NULL, $resource = NULL, $privilege = NULL, $acl = NULL)
    {
        if ($acl === NULL) {
            $acl = Zend_Registry::get('Zend_Acl');
        }
        if ( ($user === NULL) OR ! $user->acl_role) {
            if ($acl->isAllowed('guest', $resource, $privilege)) {
                $result = TRUE;
            }
            else {
                $result = FALSE;
            }
        }
        else if ($acl->isAllowed($user->acl_role, $resource, $privilege)) {
            $result = TRUE;
        }
        else {
            $roles = $this->getBindedRoles($user);
            $result = FALSE;
            foreach ($roles as $role) {
                if ($acl->isAllowed($role->acl_role, $resource, $privilege)) {
                    $result = TRUE;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * @param Model_Object_Interface
     * @return Model_Collection_Interface
     */
    public function getBindedRoles(Model_Object_Interface $user)
    {
        $cacheKey = __CLASS__.'__'.__FUNCTION__.'__'.$user->id;
        $cache = Zend_Registry::get('Zend_Cache');
        if ( ! $roles = $cache->load($cacheKey)) {
            $roles = $this->getMapper()->fetchBindedRoles($user);
            $cache->save($roles, $cacheKey, array('role', 'user_'.$user->id));
        }
        return $roles;
    }


    /**
     * returns an array of all user's roles - default and binded
     */
    public function getAllAclRoles(Model_Object_Interface $user)
    {
        if ( ! $user) {
            $roles = array('guest');
        }
        else {
            $roles = array($user->acl_role);
            $binded = $this->getBindedRoles($user);
            foreach ($binded as $role) {
                $roles []= $role->acl_role;
            }
        }
        return $roles;
    }


    public function usersAreBinded(Model_Object_Interface $old, Model_Object_Interface $new)
    {
        return  (bool) ($old->binding == $new->binding);
    }

    public function getBindedUsers(Model_Object_Interface $user)
    {
        return $this->getMapper()->fetchBindedUsers($user);
    }


    protected function _generateBindingId()
    {
        return md5(App_Uuid::get());
    }

    public function bindUsersByIdArray(array $ids)
    {
        return $this->getMapper()->bindUsersByIdArray($ids, $this->_generateBindingId());
    }

    public function unbindUsersByIdArray(array $ids)
    {
        return $this->getMapper()->bindUsersByIdArray($ids, NULL);
    }


    public function paginatorGetBinded(Model_Object_Interface $user, $rowsPerPage, $page)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        $paginator = $this->getMapper()->paginatorFetchBindedUsers($user, $rowsPerPage, $page);
        return $paginator;
    }


    public function isAllowedByIp(Model_Object_Interface $user, $ip = NULL)
    {
        if ( ! (int) Zend_Registry::get('config')->security->checkWhiteIp) {
            $result = TRUE;
        }
        else if (( ! $this->isCoworker($user)) OR (substr($user->login, 0, 5) == 'debug')) {
            $result = TRUE;
        }
        else {
            $result = Model_Service::factory('white-ip')->isInList($ip);
        }
        return $result;
    }

    public function isAllowedByLogin(Model_Object_Interface $user)
    {
        $config = Model_Service::factory('config')->read('var/denied_access.xml', 'deny');
        $result = TRUE;
        foreach ($config->userLogin as $login) {
            if ($user->login == $login) {
                $result = FALSE;
                break;
            }
        }
        return $result;
    }


    public function isCoworker(Model_Object_Interface $user)
    {
        $coworkers = Model_Service::factory('role')->getCoworkersAclRoles();
        $result = in_array($user->acl_role, $coworkers);
        if ( ! $result) {
            $binded = $this->getBindedRoles($user);
            foreach ($binded as $role) {
                if (in_array($role->acl_role, $coworkers)) {
                    $result = TRUE;
                    break;
                }
            }
        }
        return $result;
    }


    /**
     * Сохранение нового пользователя
     *
     * @param array $values
     * @return $this
     */
    public function registerNewUser(array $values)
    {
        $values['role_id'] = Model_Service::factory('role')->findByAclRole('client')->id;
        $values['status'] = '1';
        $this->saveFromValues($values);

        return $this;
    }

    /**
     * @return Model_Collection_Interface
     */
    public function getAllCoworkers()
    {
        return $this->getMapper()->fetchByAclRole(Model_Service::factory('role')->getCoworkersAclRoles(), FALSE);
    }


    public function getListOfCoworkers()
    {
        $coll = $this->getAllCoworkers();
        $list = array();
        foreach ($coll as $user) {
            $list[$user->id] = $user->name;
        }
        return $list;
    }

    /**
     * Получить массив пользователей
     *
     * @return array
     */
    public function getAllUsersExport()
    {
        $startTime = time();
        $data = $this->getMapper()->fetchAllExport($startTime);

        foreach ($data as $key => $user) {
            if (!$user['guid']) {
                $guid = App_Uuid::get();
                $this->getMapper()->poolUpdate('user', array('user_guid' => $guid), array('user_id = ?' => $user['id']));
                $data[$key]['guid'] = $guid;
            }

            $data[$key]['bonus_account'] = number_format($user['bonus_account'], 2, ',', ' ');

            unset($data[$key]['id']);
        }

        $this->getMapper()->poolUpdate('user');

        $this->getMapper()->clearExport($startTime);

        return $data;
    }

    /**
     * Добавить/обновить запись пользователей
     *
     * @param SimpleXMLElement $users
     * @return int
     */
    public function setUsers(SimpleXMLElement $users)
    {
        $guids = $this->getMapper()->fetchDistinctField('guid');

        foreach ($users->user as $user) {
            $user = $this->_prepareToSetUserValues($user);

            if (in_array($user['user_guid'], $guids)) {
                $user['user_changer_id'] = null;
                $this->getMapper()->poolUpdate('user', $user, array('user_guid = ?' => $user['user_guid']));
            } else {
                $user['user_role_id'] = Model_Service::factory('role')->findByAclRole('client')->id;
                $user['user_status'] = '1';
                $user['user_guid'] = $user['user_guid'];

                $this->getMapper()->poolInsert('user', $user);
            }
        }

        $this->getMapper()->poolUpdate('user');
        $this->getMapper()->poolInsert('user');

        return $this->getMapper()->getPoolUpdateCounter() + $this->getMapper()->getPoolInsertCounter();
    }

    /**
     * Get user by guid
     *
     * @param $guid
     *
     * @return mixed
     */
    public function getUserByGuid($guid)
    {
        return $this->getMapper()->fetchUserByGuid($guid);
    }

    /**
     * Подготовить значения для сохранения
     *
     * @param SimpleXMLElement $user
     * @return array
     */
    private function _prepareToSetUserValues(SimpleXMLElement $user)
    {
        $prefix = $this->getMapper()->getTable()->getColumnPrefix() . '_';

        $user = (array)$user;
        $user['export'] = '0';
        $user['date_changed'] = date('Y-m-d H:i:s');

        $user['bonus_account'] = App_Utf8::strip_non_ascii($user['bonus_account']);
        $user['bonus_account'] = str_replace(' ', '', $user['bonus_account']);
        $user['bonus_account'] = str_replace(',', '.', $user['bonus_account']);
        $user['bonus_account'] = $user['bonus_account'] != 0 ? $user['bonus_account']: null;

        foreach ($user as $key => $value) {
            $user[$prefix . $key] = $value ? trim($value) : $value;
            unset($user[$key]);
        }

        return $user;
    }

}