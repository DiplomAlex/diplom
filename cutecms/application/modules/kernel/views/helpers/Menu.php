<?php

class View_Helper_Menu extends Zend_View_Helper_Abstract
{

    protected static $_menuPages = NULL;
    protected static $_topMenuPages = NULL;
    protected static $_navigation = NULL;
    protected static $_addNavigations = NULL;

    /**
     * render menu by name, or let to use menu api
     */
    public function menu($name = NULL)
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

    protected function _breadcrumbs()
    {
        return '';
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
                    'module' => 'kernel',
                    'controller' => 'index',
                    'action' => 'index',
                    'pages' => self::$_topMenuPages?self::$_topMenuPages:$this->_getMenuPages('all'),
                ));
                $html = $this->view
                            ->navigation(new Zend_Navigation($pages))
                            ->setAcl(Model_Service::factory('menu')->getAcl())
                            ->breadcrumbs()
                            ->setMinDepth(0)
                            ->setPartial('menu/top.phtml')
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
        return $this->view->partial('menu/bottom.phtml');
    }

    protected function _left()
    {
        App_Profiler::start('View_Helper_Menu::_left');
        if ($user = Zend_Auth::getInstance()->getIdentity()) {
            $cache = Zend_Registry::get('Zend_Cache');
            $cacheKey = __CLASS__.'__'.__FUNCTION__.'__'.$user->id;
            if ( ! $html = $cache->load($cacheKey)) {
                $html = $this->view->partial('menu/left.phtml', array('menu'=>$this->_getMainNavigation(), 'addMenu'=>$this->_getAddNavigations()));
                $cache->save($html, $cacheKey);
            }
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
                $html = $this->view->partial('menu/dashboard.phtml', array('menu'=>$this->_getMainNavigation(), 'addMenu'=>$this->_getAddNavigations()));
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
     * init all pages and reformat structure for concrete role
     */
    protected function _getMenuPages($role = NULL)
    {
        $service = Model_Service::factory('user');
        $menuService = Model_Service::factory('menu');
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

                //$level1 = $menuService->getAllStructure($user);

                if ($role == 'all') {
                    $aliases = $this->_getRolesViewAliases($service->getAllAclRoles($user));
                    $structure = array();
                    foreach ($aliases as $alias) {
                        if ( ! empty($alias)) {
                            $aliasStruct = $menuService->getStructureByRoleViewAlias($alias, Model_Service_Menu::MODE_USE_CONDITIONS, $user);
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
                    $structure = $menuService->getStructureByRoleViewAlias($alias, Model_Service_Menu::MODE_USE_CONDITIONS, $user);
                }
                self::$_menuPages = $menuService->processStructureAcl($structure, $user);
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

}

