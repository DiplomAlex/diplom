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

        /*
        $acl->add(new Zend_Acl_Resource('shipment_admin_search'));
        $acl->allow('director', 'shipment_admin_search');

        $acl->add(new Zend_Acl_Resource('job_admin'));
        $acl->allow('director', 'job_admin');
        $acl->allow('editor', 'job_admin');
        $acl->allow('keeper', 'job_admin');

        $acl->add(new Zend_Acl_Resource('shipment_admin_stats'));
        $acl->allow('director', 'shipment_admin_stats');

        $acl->add(new Zend_Acl_Resource('shipment_abuse_stats'));
        $acl->allow('director', 'shipment_abuse_stats');

        $acl->add(new Zend_Acl_Resource('nearest_product_shippings_add'));
        $acl->allow('editor', 'nearest_product_shippings_add');
        $acl->deny('director', 'nearest_product_shippings_add');

        $acl->add(new Zend_Acl_Resource('transport_companies_stats'));
        $acl->allow('director', 'transport_companies_stats');

        $acl->add(new Zend_Acl_Resource('contacts_stats'));
        $acl->allow('director', 'contacts_stats');

        $acl->add(new Zend_Acl_Resource('instruction_add'));
        $acl->allow('editor', 'instruction_add');
        */

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
                
                    'catalog' => array(
                        'label' => $this->view->translate('Категории'),
                        'icon' => 'icon-barcode',
                        'route' => 'default',
                        'module' => 'catalog',
                        'controller' => 'admin-category',
                        'action' => 'index',
                        'hideTop' => TRUE,
                        'pages' => array(
                            array(
                                'icon' => 'icon-barcode',
                                'label' => $this->view->translate('Категории'),
                                'route' => 'default',
                                'module' => 'catalog',
                                'controller' => 'admin-category',
                                'action' => 'index',                               
                            ),
                             array(
                                'label' => $this->view->translate('Добавть категорию'),
                                'route' => 'default',
                                'module' => 'catalog',
                                'controller' => 'admin-category',
                                'action' => 'edit',
                            ),
                             array(
                                'label' => $this->view->translate('Удаление категории'),
                                'route' => 'default',
                                'module' => 'catalog',
                                'controller' => 'admin-category',
                                'action' => 'delete',
                                'rel' => array('hide' => FALSE)
                            ),
                            array(
                                'label' => $this->view->translate('Товары'),
                                'route' => 'default',
                                'module' => 'catalog',
                                'controller' => 'admin-item_index',
                                'action' => 'index',
                            ),
                            array(
                                'label' => $this->view->translate('Добавить товар'),
                                'route' => 'default',
                                'module' => 'catalog',
                                'controller' => 'admin-item_index',
                                'action' => 'edit',
                            ),
                            array(
                                'label' => $this->view->translate('Удаление товара'),
                                'route' => 'default',
                                'module' => 'catalog',
                                'controller' => 'admin-item_index',
                                'action' => 'delete',
                                'rel' => array('hide' => FALSE)
                            ),                            
                        ),
                    ),

                'attribute' => array(
                    'label' => $this->view->translate('Управление атрибутами'),
                    'icon' => 'ico_news',
                    'route' => 'default',
                    'module' => 'catalog',
                    'controller' => 'admin-attribute',
                    'action' => 'index',
                    'pages' => array(
                        array(
                            'label' => $this->view->translate('Добавить атрибут'),
                            'route' => 'default',
                            'module' => 'catalog',
                            'controller' => 'admin-attribute',
                            'action' => 'edit',
                        ),
                        array(
                            'label' => $this->view->translate('Наборы'),
                            'route' => 'default',
                            'module' => 'catalog',
                            'controller' => 'admin-attribute-group',
                            'action' => 'index',
                        ),
                    ),
                ),

                /*
                    'manufacturer' => array(
                        'label' => $this->view->translate('Производители'),
                        'icon' => 'ico_news',
                        'route' => 'default',
                        'module' => 'catalog',
                        'controller' => 'admin-manufacturer',
                        'action' => 'index',
                        'pages' => array(
                            array(
                                'label' => $this->view->translate('Новый производитель'),
                                'route' => 'default',
                                'module' => 'catalog',
                                'controller' => 'admin-manufacturer',
                                'action' => 'edit',
                            ),
                        ),
                    ),
 */
                    
                    

                    'order' => array(
                        'label' => $this->view->translate('Заказы'),
                        'icon' => 'icon-shopping-cart',
                        'route' => 'default',
                        'module' => 'checkout',
                        'controller' => 'admin-order',
                        'action' => 'index',
                        'pages' => array(
                            array(
                                'label' => $this->view->translate('Удаление заказа'),
                                'route' => 'default',
                                'module' => 'checkout',
                                'controller' => 'admin-order',
                                'action' => 'delete',
                                'rel' => array('hide' => FALSE)
                            ),     
                        )
                    ),


                    // 'currency' => array(
                        // 'label' => $this->view->translate('Курсы валют'),
                        // 'icon' => 'ico_news',
                        // 'route' => 'default',
                        // 'module' => 'kernel',
                        // 'controller' => 'admin-currency',
                        // 'action' => 'edit',
                    // ),


                    


                     'news' => array(
                         'label' => $this->view->translate('Новости и рассылки'),
                         'icon' => 'icon-globe',
                         'route' => 'default',
                         'module' => 'kernel',
                         'controller' => 'admin-news',
                         'action' => 'index',
                         'hideTop' => TRUE,
                         'pages' => array(
                            array(
                                 'icon' => 'icon-globe',
                                 'label' => $this->view->translate('Новости и рассылки'),
                                 'route' => 'default',
                                 'module' => 'kernel',
                                 'controller' => 'admin-news',
                                 'action' => 'index',
                            ),
                            array(
                                 'label' => $this->view->translate('Добавить новость'),
                                 'route' => 'default',
                                 'module' => 'kernel',
                                 'controller' => 'admin-news',
                                 'action' => 'edit',
                             ),
                            array(
                                'label' => $this->view->translate('Удаление новости'),
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-news',
                                'action' => 'delete',
                                'rel' => array('hide' => FALSE)
                            ),   
                            array(
                                 'label' => $this->view->translate('Рубрики'),
                                 'route' => 'default',
                                 'module' => 'kernel',
                                 'controller' => 'admin-news-topic',
                                 'action' => 'index',
                             ),
                              array(
                                 'label' => $this->view->translate('Добавить рубрику'),
                                 'route' => 'default',
                                 'module' => 'kernel',
                                 'controller' => 'admin-news-topic',
                                 'action' => 'edit',
                             ),
                            array(
                                'label' => $this->view->translate('Удаление рубрики'),
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-news-topic',
                                'action' => 'delete',
                                'rel' => array('hide' => FALSE)
                            ),   
                              array(
                                 'label' => $this->view->translate('Список подписчиков'),
                                 'route' => 'default',
                                 'module' => 'kernel',
                                 'controller' => 'admin-news',
                                 'action' => 'subscribers-list',
                             ),
                         ),
                     ),



                    // 'faq' => array(
                        // 'label' => $this->view->translate('FAQs'),
                        // 'icon' => 'ico_news',
                        // 'route' => 'default',
                        // 'module' => 'kernel',
                        // 'controller' => 'admin-faq',
                        // 'action' => 'index',
                        // 'pages' => array(
                            // array(
                                // 'label' => $this->view->translate('Добавить'),
                                // 'route' => 'default',
                                // 'module' => 'kernel',
                                // 'controller' => 'admin-faq',
                                // 'action' => 'edit',
                            // ),
                        // ),
                    // ),




                    /*
                    'stats' => array(
                        'label' => $this->view->translate('Статистика'),
                        'icon' => 'icons_statistic',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-index',
                        'action' => 'index',
                        'hideTop' => TRUE,
                        'pages' => array(
                            array(
                                'label' => $this->view->translate('Вход в сервис (логи)'),
                                'route' => 'default',
                                'module' => 'tickets',
                                'controller' => 'stats',
                                'action' => 'user-login',
                            ),
                        ),
                    ),
                    */

                    'banner' => array(
                        'label' => $this->view->translate('Баннеры'),
                        'icon' => 'icon-picture',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-banner',
                        'action' => 'index',
                        'hideTop' => TRUE,
                        'pages' => array(
                            array(
                                'label' => $this->view->translate('Баннеры'),
                                'icon' => 'icon-picture',
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-banner',
                                'action' => 'index',
                            ),
                            array(
                                'label' => $this->view->translate('Добавить баннер'),
                                'icon' => 'ico_buy_sell',
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-banner',
                                'action' => 'edit',
                            ),
                             array(
                                'label' => $this->view->translate('Удаление баннера'),
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-banner',
                                'action' => 'delete',
                                'rel' => array('hide' => FALSE)
                            ),   
                        ),
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
                            // array(
                                // 'label' => $this->view->translate('Последние комментарии'),
                                // 'icon' => 'ico_buy_sell',
                                // 'route' => 'default',
                                // 'module' => 'kernel',
                                // 'controller' => 'admin-comment',
                                // 'action' => 'index-top-new',
                            // ),
                        ),
                    ),

                    'page' => array(
                        'label' => $this->view->translate('Страницы'),
                        'icon' => 'icon-file',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-page',
                        'action' => 'index',
                        'hideTop' => TRUE,
                        'pages' => array(
                             array(
                                'icon' => 'icon-file',
                                'label' => $this->view->translate('Страницы'),
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-page',
                                'action' => 'index',
                            ),
                            array(
                                'label' => $this->view->translate('Добавить страницу'),
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-page',
                                'action' => 'edit',
                            ),
                             array(
                                'label' => $this->view->translate('Удалиние страницы'),
                                'route' => 'default',
                                'module' => 'kernel',
                                'controller' => 'admin-page',
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

                    /*
                    'settings' => array(
                        'label' => $this->view->translate('Настройки'),
                        'icon' => 'ico_settings',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'index',
                        'action' => 'developing',
                    ),
                    */

                    'white_ip' => array(
                        'label' => $this->view->translate('Белые IP'),
                        'icon' => 'ico_your_clients',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-white-ip',
                        'action' => 'index',
                    ),
                    
                    // 'admin_article' => array(
                        // 'code' => 'admin_article',
                        // 'label' => $this->view->translate('Статьи'),
                        // 'icon' => 'darts',
                        // 'route' => 'default',
                        // 'module' => 'kernel',
                        // 'controller' => 'admin-article',
                        // 'action' => 'index',
                        // 'pages' => array(
                            // array(
                                // 'code' => 'admin_article_new',
                                // 'label' => $this->view->translate('Новая'),
                                // 'icon' => 'darts',
                                // 'route' => 'default',
                                // 'module' => 'kernel',
                                // 'controller' => 'admin-article',
                                // 'action' => 'edit',
                            // ),
                            // array(
                                // 'code' => 'admin_article_topic',
                                // 'label' => $this->view->translate('Темы'),
                                // 'icon' => 'darts',
                                // 'route' => 'default',
                                // 'module' => 'kernel',
                                // 'controller' => 'admin-article-topic',
                                // 'action' => 'index',
                            // ),                    
                        // ),
                    // ),
/*
                    'admin_site' => array(
                        'code' => 'admin_site',
                        'label' => $this->view->translate('Web-сайты'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-site',
                        'action' => 'index',
                    ),
 */
/*
                    'admin_language' => array(
                        'code' => 'admin_language',
                        'label' => $this->view->translate('Языки'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-language',
                        'action' => 'index',
                    ),
 */
                    
                    // 'admin_brule' => array(
                        // 'code' => 'admin_brule',
                        // 'label' => $this->view->translate('Доставки/оплаты'),
                        // 'icon' => 'darts',
                        // 'route' => 'default',
                        // 'module' => 'checkout',
                        // 'controller' => 'admin-brue',
                        // 'action' => 'index-shipment',
                    	// 'hideTop' => TRUE,
                        // 'pages' => array(
                            // array(
                                // 'code' => 'admin_brule_shipment',
                                // 'label' => $this->view->translate('Доставки'),
                                // 'icon' => 'darts',
                                // 'route' => 'default',
                                // 'module' => 'checkout',
                                // 'controller' => 'admin-brule',
                                // 'action' => 'index-shipment',
                            // ),
                            // array(
                                // 'code' => 'admin_brule_payment',
                                // 'label' => $this->view->translate('Оплаты'),
                                // 'icon' => 'darts',
                                // 'route' => 'default',
                                // 'module' => 'checkout',
                                // 'controller' => 'admin-brule',
                                // 'action' => 'index-payment',
                            // ),                    
                        // ),
                    // ),
                                        
                    
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
         
            $level1['catalog'],
            $level1['attribute'],
            //$level1['manufacturer'],
           
            $level1['order'],
            // $level1['admin_brule'],
            $level1['banner'],
            
            // $level1['admin_article'],
            $level1['news'],
            $level1['page'],

            // $level1['faq'],         
            $level1['user'],
            /*$level1['white_ip'],*/
            
            //$level1['admin_site'],
            //$level1['admin_language'],
            
            // $level1['currency'],

            

            /*$level1['stats'],*/
        );
    }

    protected function _getMenuStructure_client($level1)
    {
        $struct = array(
        );
        return $struct;
    }

    protected function _getMenuStructure_dealer($level1)
    {
        $struct = array(
        );
        return $struct;
    }

    protected function _getMenuStructure_manager($level1)
    {
        return array(
            $level1['catalog'],
            //$level1['attribute'],
            $level1['order'],

            $level1['user'],
            $level1['change_profile'],

            /*$level1['stats'],*/
        );
    }

    protected function _getMenuStructure_stockman($level1)
    {
        return array(
            $level1['catalog'],
            //$level1['attribute'],
            //$level1['manufacturer'],
        );
    }

    protected function _getMenuStructure_editor($level1)
    {
        return array(
            $level1['catalog'],
            // $level1['admin_article'],
            $level1['news'],
            $level1['page'],
            // $level1['faq'],
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

