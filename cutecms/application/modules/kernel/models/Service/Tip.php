<?php

class Model_Service_Tip extends Model_Service_Abstract
{

    protected static $_current = NULL;

    protected $_defaultInjections = array(
        'Model_Mapper_Interface' => 'Model_Mapper_Db_Tip',
        'Model_Object_Interface' => 'Model_Object_Tip',
        'Model_Service_Language',
    );


    public function init()
    {
        $lang = $this->getInjector()->getObject('Model_Service_Language');
        $this->getMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());
    }


    public function getEditFormValues($id)
    {
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        $descs = $this->getMapper()->getPlugin('Description')->fetchDescriptions($id);
        $values = $values + $descs;
        return $values;
    }


    public function getAvailableDestinations($role = NULL)
    {
        $menuService = Model_Service::factory('menu');
        if ($role === NULL) {
            $struct = $menuService->getAllStructure();
        }
        else {
            $struct = $menuService->getStructureByRoleViewAlias(Model_Service::factory('role')->getViewAlias($role));
        }
        $list = array();
        foreach ($struct as $row) {
            $list[$row['code']] = $row['label'];
            if (isset($row['pages'])) {
                foreach ($row['pages'] as $sub) {
                    $list[$sub['code']] = $row['label'] .' --> ' . $sub['label'];
                }
            }
        }
        return $list;
    }

    public function getAvailableRoles()
    {
        $roleService = Model_Service::factory('role');
        $roles = $roleService->getAclRolesList();
        $list = array();
        $aliases = array();
        foreach ($roles as $role) {
            if (($alias = $roleService->getViewAlias($role)) AND ( ! isset($aliases[$alias]))) {
                $list[$role] = $alias;//$this->getTranslator($role);
                $aliases[$alias] = $role;
            }
        }
        return $list;
    }

    public function getCurrent()
    {
        if (self::$_current === NULL) {
            if ( ! $user = Model_Service::factory('user')->getCurrent()) {
                $role = 'guest';
            }
            else {
                $role = $user->acl_role;
            }
            if ( ! $dest = $this->_getCurrentDestination($role)) {
                self::$_current = FALSE;
            }
            else {
                self::$_current = $this->getMapper()->fetchByDestinationAndRole($dest, $role);
            }
        }
        return self::$_current;
    }

    /**
     * @param string
     * @return string | FALSE
     */
    protected function _getCurrentDestination($role)
    {
        $alias = Model_Service::factory('role')->getViewAlias($role);
        $struct = Model_Service::factory('menu')->getStructureByRoleViewAlias($alias);
        $nav = new Zend_Navigation($struct);
        $dest = FALSE;
        foreach ($nav as $page) {
            if ($page->isActive()) {
                $dest = $page->get('code');
                break;
            }
            $kids = $page->get('pages');
            if (count($kids)) {
                foreach ($kids as $sub) {
                    if ($sub->isActive()) {
                        $dest = $sub->get('code');
                        break;
                    }
                }
            }
            if ($dest !== FALSE) {
                break;
            }
        }
        return $dest;
    }


    public function paginatorGetAllActive($rowsPerPage = NULL, $page = NULL)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        $paginator = $this->getMapper()->paginatorFetchAllActive($rowsPerPage, $page);
        return $paginator;
    }


    public function paginatorGetAllArchive($rowsPerPage = NULL, $page = NULL)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        $paginator = $this->getMapper()->paginatorFetchAllArchive($rowsPerPage, $page);
        return $paginator;
    }

    public function saveFromValues(array $values)
    {
        if (empty($values['id'])) {
            unset($values['id']);
        }
        if (empty($values['status'])) {
            $values['status'] = 1;
        }
        $this->getMapper()->saveComplex($values);
        return $this;
    }

    public function deactivate(Model_Object_Interface $tip)
    {
        $tip->status = 0;
        $this->save($tip);
    }


}