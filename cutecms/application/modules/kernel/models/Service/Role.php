<?php

class Model_Service_Role extends Model_Service_Abstract
{

    protected static $_cacheAclRole = array();

    protected $_defaultInjections = array(
    	'Model_Mapper_Interface' => 'Model_Mapper_Db_Role',
        'Model_Object_Interface' => 'Model_Object_Role',
        'Model_Service_Language',
    );

    protected $_coworkers = array('admin', 'director', 'editor', 'keeper', 'manager');


    public function init()
    {
        $lang = $this->getInjector()->getObject('Model_Service_Language');
        $this->getMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());
    }


    /**
     * get objects fields values for edit form
     * @return array
     */
    public function getEditFormValues($id)
    {
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        $descs = $this->getMapper()->getPlugin('Description')->fetchDescriptions($id);
        $rcs = $this->getMapper()->getPlugin('Resource')->fetchResources($obj);
        $values = $values + $descs + $rcs;
        return $values;
    }

    /**
     * get all acl roles from config
     * @return array
     */
    public function getAclRolesList()
    {
        $list = array();
        foreach (Zend_Registry::get('config')->aclRoles as $role=>$parent) {
            $list []= $role;
        }
        return $list;
    }

    public function findByAclRole($aclRole)
    {
        if ( ! isset(self::$_cacheAclRole[$aclRole])) {
            self::$_cacheAclRole[$aclRole] =  $this->getMapper()->fetchOneByAclRole($aclRole);
        }
        return self::$_cacheAclRole[$aclRole];
    }

    public function getAll()
    {
        $cache = Zend_Registry::get('Zend_Cache');
        $cacheKey = __CLASS__.'__getAll';
        if ( ! $all = $cache->load($cacheKey)) {
            $all = parent::getAll();
            $cache->save($all, $cacheKey, array('role'));
        }
        return $all;
    }

    public function getNonCache($id)
    {
        return $role = parent::get($id);
    }
    
    public function changeSortingNonCache($objId, $position)
    {
        if ( ! $this->getMapper()->hasPlugin('Sorting')) {
            $this->_throwException('my mapper ('.get_class($this->getMapper()).') has no plugin "Sorting" so it cannot changeSorting');
        }
        $object = $this->getNonCache($objId);
        $this->getMapper()->getPlugin('Sorting')->changeSorting($object, $position);
        return $this;
    }
    
    //@TODO: исправить работу с кэш
    public function get($id) 
    {
        $cache = Zend_Registry::get('Zend_Cache');
        $cacheKey = __CLASS__.'__get__'.$id;
        if ( ! $role = $cache->load($cacheKey)) {
            $role = parent::get($id);
            $cache->save($role, $cacheKey, array('role'));
        }
        return $role;
    }

    public function getCoworkersAclRoles()
    {
        return $this->_coworkers;
    }

    public function isAllowedByAcl($aclRole, $resource = NULL, $privilege = NULL, $acl = NULL)
    {
        if ($acl === NULL) {
            $acl = Zend_Registry::get('Zend_Acl');
        }
        if (! $aclRole) {
            $aclRole = 'guest';
        }
        return $acl->isAllowed($aclRole, $resource, $privilege);
    }

    /**
     * @param mixed Model_Object_Interface | string
     * @return string
     */
    public function getViewAlias($role)
    {
        if ($role instanceof Model_Object_Interface) {
            $role = $role->acl_role;
        }
        $config = Zend_Registry::get('config')->roleViewAlias;
        $result = $config->{$role};
        return $result;
    }    
}