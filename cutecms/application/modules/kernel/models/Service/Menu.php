<?php

class Model_Service_Menu implements Model_Service_Interface
{

    const MODE_NO_CONDITIONS  = 0;
    const MODE_USE_CONDITIONS = 1;

    protected static $_acl = NULL;
    protected static $_translator = NULL;

    /**
     * lazy init translator
     * @return Zend_Translate_Adapter
     */
    public function getTranslator()
    {
        if (self::$_translator === NULL) {
            self::$_translator = Zend_Registry::get('Zend_Translate')->getAdapter();
        }
        return self::$_translator;
    }

    public function getAllStructure(Model_Object_Interface $user = NULL, $withSpacer = TRUE)
    {
        $data = array(
            'admin_user_list' => array(
                'code' => 'user_list',
                'icon' => 'icons_statistic',
                'label' => $this->getTranslator()->_('Список пользователей'),
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-user',
                'action' => 'index',
             ),

            'errors' => array(
                'code' => 'errors',
                'label' => $this->getTranslator()->_('Ошибки по фирме'),
                'icon' => 'ico_joints',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'bug',
                'action' => 'index',
                'pages' => array(
                    array(
                        'code' => 'error_admin_topics',
                        'label' => $this->getTranslator()->_('Темы ошибок'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'admin-bug',
                        'action' => 'topic',
                        'checkAcl' => TRUE,
                    ),
                ),
            ),

            'change_profile' => array(
                'code' => 'change_profile',
                'label' => $this->getTranslator()->_('Изменить профиль'),
                'icon' => 'ico_my_manager',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-user',
                'action' => 'edit',
                'params' => array(
                    'id' => @$user->id,
                ),
            ),

            'usermenu' => array(
                'code' => 'usermenu',
                'label' => $this->getTranslator()->_('Меню пользователя'),
                'icon' => 'ico_clients_active',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-user',
                'action' => 'edit',
                'params' => array(
                    'id' => @$user->id,
                ),
            ),

            'settings' => array(
                'code' => 'settings',
                'label' => $this->getTranslator()->_('Настройки'),
                'icon' => 'ico_settings',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'index',
                'action' => 'developing',
            ),

            'my_manager' => array(
                'code' => 'my_manager',
                'label' => $this->getTranslator()->_('Мой преподаватель'),
                'icon' => 'ico_my_manager',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'user',
                'action' => 'my-manager',
            ),

            'your_clients' => array(
                'code' => 'your_clients',
                'label' => $this->getTranslator()->_('Ваши студенты'),
                'icon' => 'ico_your_clients',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'user',
                'action' => 'my-clients',
            ),


            'admin_client_resource' => array(
                'code' => 'admin_client_resource',
                'label' => $this->getTranslator()->_('Для студентов'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-client-resource',
                'action' => 'index',
            ),

            'client_resource' => array(
                'code' => 'client_resource',
                'label' => $this->getTranslator()->_('Для студентов'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'client-resource',
                'action' => 'index',
            ),

            'admin_site' => array(
                'code' => 'admin_site',
                'label' => $this->getTranslator()->_('Web-сайты'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'admin-site',
                'action' => 'index',
            ),

            'SPACER' => array(
                'code' => 'spacer',
                'label' => '-',
                'module' => 'kernel',
                'controller' => 'index',
                'action' => 'developing',
            ),

        );

        return $data;
    }

    public function getFlatStructure(Model_Object_Interface $user = NULL, $withSpacer = TRUE)
    {
        $struct = $this->getAllStructure($user, $withSpacer);
        $list = array();
        foreach ($struct as $row) {
            $list[$row['code']] = $row;
            if (isset($row['pages'])) {
                foreach ($row['pages'] as $page) {
                    $page['label'] = $row['label'].' --> ' . $page['label'];
                    $list[$page['code']] = $page;
                }
            }
        }
        return $list;
    }


    public function getStructureByRoleViewAlias($role, $mode = self::MODE_USE_CONDITIONS, Model_Object_Interface $user = NULL)
    {
        if (empty($role)) {
            $result = array();
        }
        else {
            $data = $this->getAllStructure($user);
            $result = $this->{'_getStructure_'.$role}($data, $mode);
        }
        return $result;
    }


    protected function _getStructure_director($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        return array(
            $level1['admin_user_list'],

            $level1['errors'],

            $level1['change_profile'],

            $level1['admin_client_resource'],

            $level1['admin_site'],
        );
    }

    protected function _getStructure_client($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        $struct = array(
            $level1['usermenu'],
            $level1['settings'],

            $level1['discounts'],
        );

        $struct [] = $level1['my_manager'];
        $struct [] = $level1['client_resource'];
        return $struct;
    }



    protected function _getStructure_manager($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        return array(
            $level1['your_clients'],
            $level1['errors'],

            $level1['usermenu'],

            $level1['client_resource'],
        );
    }

    protected function _getStructure_editor($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        return array(
            $level1['errors'],

            $level1['usermenu'],

            $level1['admin_client_resource'],
        );
    }

    protected function _getStructure_siteEditor($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        return array(
        );
    }

    

    /**
     * process prepared structure - remove elements not allowed by acl
     * @param array
     * @param mixed Model_Object_Interface | string - user or role to check by acl
     * @return @array
     */
    public function processStructureAcl($structure, $checking)
    {
        $pages = array();
        $acl = $this->getAcl();
        $service = Model_Service::factory('user');
        if ($checking instanceof Model_Object_Interface) {
            $aclService = $service;
        }
        else {
            $aclService = Model_Service::factory('role');
        }
        foreach ($structure as $page) {
            if (isset($page['resource'])) {
                $pageResource = $page['resource'];
            }
            else {
                $pageResource = $page['code'];
            }
            if (( ! $acl->has($pageResource)) OR ($page['checkAcl']!==TRUE) OR ($aclService->isAllowedByAcl($checking, $pageResource, NULL, $acl))) {
                if (isset($page['pages']) AND is_array($page['pages'])) {
                    foreach($page['pages'] as $key=>$sub) {
                        if (isset($sub['resource'])) {
                            $subResource = $sub['resource'];
                        }
                        else {
                            $subResource = $sub['code'];
                        }
                        if (isset($page['checkAcl']) AND ($page['checkAcl']===TRUE) AND ! $aclService->isAllowedByAcl($checking, $subResource, NULL, $acl)) {
                            unset($page['pages'][$key]);
                        }
                    }
                    $page['pages'] = array_values($page['pages']);
                }
                $pages []= $page;
            }
        }
        return $pages;
    }



    /**
     * lazy init acl
     */
    public function getAcl()
    {
        if (self::$_acl === NULL) {
            self::$_acl = $this->_initAcl();
        }
        return self::$_acl;
    }

    /**
     * prepare acl
     */
    protected function _initAcl()
    {
        $acl = new Zend_Acl();
        $roles = array();
        foreach (Zend_Registry::get('config')->aclRoles as $role => $parent) {
            if (empty($parent)) {
                $parentRole = NULL;
            }
            else {
                $parentRole = $roles[$parent];
            }
            $roles[$role] = new Zend_Acl_Role($role);

            $acl->addRole($roles[$role], $parentRole);
        }

        return $acl;
    }

}
