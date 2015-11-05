<?php

class View_Helper_AdminMenu extends Zend_View_Helper_Abstract
{

    protected static $_acl = NULL;
    protected static $_menuPages = NULL;
    protected static $_topMenuPages = NULL;
    protected static $_navigation = NULL;
    protected static $_addNavigations = NULL;

    /**
     * render menu by name, or let to use menu api
     */
    public function adminMenu($name = NULL)
    {
        if ($name !== NULL) {
            return $this->render($name);
        }
        else {
            return $this;
        }
    }

    public function setTopMenuPages(array $pages)
    {
        self::$_topMenuPages = $pages;
        return $this;
    }

    public function render($name)
    {
        $name = strtolower($name);
        return $this->{'_'.$name}();
    }

    protected function _top()
    {
        App_Profiler::start('View_Helper_Menu::_top');
        if ($user = Zend_Auth::getInstance()->getIdentity()) {
            $cache = Zend_Registry::get('Zend_Cache');
            $cacheKey = __CLASS__.'__'.__FUNCTION__.'__'.$user->id.'__'.md5($_SERVER['REQUEST_URI']);
            if ( ! $html = $cache->load($cacheKey)) {
                $pages = array(array(
                    'label' => $this->view->translate('Главная'),
					'icon' => 'icon-home',
					'route' => 'default',
					'controller' => 'admin-index',
					'action' => 'index',
                    'pages' => self::$_topMenuPages?self::$_topMenuPages:$this->_getMenuPages('all'),
                ));
                $html = $this->view
                            ->navigation(new Zend_Navigation($pages))
                            ->setAcl($this->_getAcl())
                            ->breadcrumbs()
                            ->setMinDepth(0)
                            ->setPartial('admin-menu/top.phtml')
                            ;
                $cache->save($html, $cacheKey);
            }
        }
        else {
            $html = '';
        }
        App_Profiler::stop('View_Helper_Menu::_top');
        return $html;
    }

    protected function _bottom()
    {
        return $this->view->partial('admin-menu/bottom.phtml');
    }

    protected function _left()
    {
        App_Profiler::start('View_Helper_Menu::_left');
        if ($user = Zend_Auth::getInstance()->getIdentity()) {
            // $cache = Zend_Registry::get('Zend_Cache');
            // $cacheKey = __CLASS__.'__'.__FUNCTION__.'__'.$user->id;
            // if ( ! $html = $cache->load($cacheKey)) {
                $html = $this->view->partial('admin-menu/left.phtml', array('menu'=>$this->_getMainNavigation(), 'addMenu'=>$this->_getAddNavigations()));
                // $cache->save($html, $cacheKey);
            // }
        }
        else {
            $html = '';
        }
        App_Profiler::stop('View_Helper_Menu::_left');
        return $html;
    }

    protected function _dashboard()
    {
        App_Profiler::start('View_Helper_Menu::_dashboard');
        if ($user = Zend_Auth::getInstance()->getIdentity()) {
            $cache = Zend_Registry::get('Zend_Cache');
            $cacheKey = __CLASS__.'__'.__FUNCTION__.'__'.$user->id;
            if ( ! $html = $cache->load($cacheKey)) {
                $html = $this->view->partial('admin-menu/dashboard.phtml', array('menu'=>$this->_getMainNavigation(), 'addMenu'=>$this->_getAddNavigations()));
                $cache->save($html, $cacheKey);
            }
        }
        else {
            $html = '';
        }
        App_Profiler::stop('View_Helper_Menu::_dashboard');

        return $html;
    }


    /**
     * lazy init navigation
     */
    protected function _getMainNavigation()
    {
        if (self::$_navigation === NULL) {
            self::$_navigation = new Zend_Navigation($this->_getMenuPages(Model_Service::factory('user')->getCurrent()->acl_role));
        }
        return self::$_navigation;
    }

    protected function _getAddNavigations()
    {
        if (self::$_addNavigations === NULL) {
            $service = Model_Service::factory('user');
            $user = $service->getCurrent();
            $roles = $service->getBindedRoles($user);
            self::$_addNavigations = array();
            $currAlias = $this->view->roleViewAlias($user->acl_role);
            foreach ($roles as $role) {
                if ($this->view->roleViewAlias($role->acl_role) != $currAlias) {
                    self::$_addNavigations[$role->acl_role] = new Zend_Navigation($this->_getMenuPages($role->acl_role));
                }
            }
        }
        return self::$_addNavigations;
    }


    /**
     * lazy init acl
     */
    protected function _getAcl()
    {
        if (self::$_acl === NULL) {
            self::$_acl = $this->_initMenuAcl();
        }
        return self::$_acl;
    }

    /**
     * prepare acl
     */
    protected function _initMenuAcl()
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

    /**
     * init all pages and reformat structure for concrete role
     */
    protected function _getMenuPages($role = NULL)
    {
        $service = Model_Service::factory('user');
        $user = $service->getCurrent();
        if (isset($user->manager_id)) {
            $managerId = $user->manager_id;
        }
        else {
            $managerId = NULL;
        }
        /*if ( ! self::$_menuPages) {*/

            $cache = Zend_Registry::get('Zend_Cache');
            $cacheKey = __CLASS__.'__'.__FUNCTION__.'__'.@$user->id.'__'.(is_array($role)?implode('_', $role):$role);
            $cacheTags = array('user_'.@$user->id);
            if ( ! self::$_menuPages = $cache->load($cacheKey)) {

                $level1 = array(

                    'dashboard' => array(
                        'label' => $this->view->translate('Главная'),
                        'icon' => 'icon-home',
                        'route' => 'default',
                        'controller' => 'admin-index',
                        'action' => 'index',
                    ),
                    'user' => array(
                        'label' => $this->view->translate('Пользователи'),
                        'icon' => 'icon-user',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-user',
                        'action' => 'index',
                        'hideTop' => TRUE,
                        'pages' => array(
                            array(
                                'label' => $this->view->translate('Пользователи'),
                                'icon' => 'icon-user',
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-user',
                                'action' => 'index',
                            ),
                            array(
                                'label' => $this->view->translate('Добавить пользователя'),
                                'icon' => 'ico_buy_sell',
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-user',
                                'action' => 'edit',
                            ),
                            array(
                                'label' => $this->view->translate('Удаление пользователя'),
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-user',
                                'action' => 'delete',
                                'rel' => array('hide' => FALSE)
                            ),  
                            array(
                                'label' => $this->view->translate('Группы'),
                                'icon' => 'ico_buy_sell',
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-role',
                                'action' => 'index',
                            ),
                             array(
                                'label' => $this->view->translate('Добавить группу'),
                                'icon' => 'ico_buy_sell',
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-role',
                                'action' => 'edit',
                            ),
                            array(
                                'label' => $this->view->translate('Удаление группы'),
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-role',
                                'action' => 'delete',
                                'rel' => array('hide' => FALSE)
                            ),
                        ),
                    ),

                    'change_profile' => array(
                        'label' => $this->view->translate('Изменить профиль'),
                        'icon' => 'ico_my_manager',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-user',
                        'action' => 'edit',
                        'params' => array(
                            'id' => $user->id,
                        ),
                    ),

                    'SPACER' => array(
                        'label' => '-',
                        'module' => 'kernel',
                        'controller' => 'index',
                        'action' => 'developing',
                    ),
                );

                if ($role == 'all') {
                    $aliases = $this->_getRolesViewAliases($service->getAllAclRoles($user));
                    $structure = array();
                    foreach ($aliases as $alias) {
                        if ( ! empty($alias)) {
                            $aliasStruct = $this->{'_getMenuStructure_'.$alias}($level1);
                            $structure = array_merge($structure, $aliasStruct);
                        }
                    }
                }
                else {
                    if ($role === NULL) {
                        $role = $user->acl_role;
                    }
                    $alias = $this->_getRolesViewAliases($role);
                    if (empty($alias)) {
                        throw new Zend_View_Exception('no role alias for "'.$role.'"');
                    }
                    $structure = $this->{'_getMenuStructure_'.$alias}($level1);
                }
                self::$_menuPages = $this->_processStructureAcl($structure);
                $cache->save(self::$_menuPages, $cacheKey, $cacheTags);
            }

        /*}*/
        return self::$_menuPages;

    }

    protected function _getRolesViewAliases($roles)
    {
        $config = Zend_Registry::get('config')->roleViewAlias;
        if (is_array($roles)) {
            $result = array();
            foreach ($roles as $role) {
                $result[$config->{$role}] = $config->{$role};
            }
        }
        else {
            $result = $config->{$roles};
        }
        return $result;
    }

    protected function _getMenuStructure_director($level1)
    {
        return array(
            $level1['dashboard'],
            $level1['user'],
        );
    }

    protected function _getMenuStructure_client($level1)
    {
        $struct = array(
        );
        return $struct;
    }

    protected function _getMenuStructure_editor($level1)
    {
        return array(
        );
    }


    /**
     * process prepared structure - remove elements not allowed by acl
     * @param array
     * @return @array
     */
    protected function _processStructureAcl($structure)
    {
        $pages = array();
        $acl = $this->_getAcl();
        $service = Model_Service::factory('user');
        $user = $service->getCurrent();
        foreach ($structure as $page) {
            if (empty($page['resource']) OR ( ! $acl->has($page['resource'])) OR ($service->isAllowedByAcl($user, $page['resource'], NULL, $acl))) {
                if (isset($page['pages']) AND is_array($page['pages'])) {
                    foreach($page['pages'] as $key=>$sub) {
                        if ( ! empty($sub['resource']) AND ! $service->isAllowedByAcl($user, $sub['resource'], NULL, $acl)) {
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

}

