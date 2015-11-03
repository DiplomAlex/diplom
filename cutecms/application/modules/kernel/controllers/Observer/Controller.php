<?php

class Observer_Controller extends App_Event_Observer
{

    /**
     * init actions for controller:
     * set layout
     * send correct headers
     *
     * @param Zend_Controller_Action
     * @param string
     */
    public function initAction()
    {
        $controller = $this->getData(0);
        @$layout = $this->getData(1);
        @$throwExceptionInsteadOfRedirect = $this->getData(2);

        $this->_initLocale($controller);
        
        /* init view paths */
        $this->_initViewPaths($controller->view);
        
        /*init layout*/
        $this->_initLayout($controller->view, $layout);

        /* init plugin cache*/
        $this->_initPluginCache($controller);

        /* paginator */
        $this->_initPagination();
        
        /* set js helpers playsholders */
        $this->_initLayoutJsHelpers($controller->view);        

        /* set html title, meta tags and encoding*/
        $this->_initHtmlMeta($controller);
        
        if (PHP_SAPI != 'cli') {        
            $this->_initAcl($controller, $throwExceptionInsteadOfRedirect);
            
            /* reset return url if controller changed */
            $this->_initReturnUrl($controller);        
    
            /* init db profiler as FirePHP for ajax requests */        
            $this->_initDebug($controller);
        }
    }
    
    protected function _initViewPaths(Zend_View_Interface $view)
    {
    }
    
    protected function _initLayout(Zend_View_Interface $view, $layout)
    {
        if ( ! $layout) {
            if (PHP_SAPI == 'cli') {
                $layout = 'cli';
            }
            else {
                $layout = 'layout';
            }
        }
        $view->layout()->setLayout($layout);        
    }
    
    protected function _initPluginCache(Zend_Controller_Action $controller)
    {
        if (PHP_SAPI !== 'cli') {
            $classFileIncCache = FRONT_APPLICATION_PATH . '/var/cache/pluginLoaderCache_'.$controller->getRequest()->getModuleName().'.php';
            if (file_exists($classFileIncCache)) {
                include_once $classFileIncCache;
            }
            Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
        }
    }
    
    protected function _initHtmlMeta(Zend_Controller_Action $controller)
    {
        $controller->view->headTitle(Model_Service::factory('config')->read('var/html_meta.xml')->html_title);
        /* set encoding */
        $controller->view->headMeta('text/html; charset=utf-8', 'Content-Type', 'http-equiv');
        $controller->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8', TRUE);        
    }
        
    
    protected function _initPagination()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
        Zend_Paginator::addAdapterPrefixPath('App_Paginator_Adapter_', APPLICATION_PATH . '/library/App/Paginator/Adapter');        
    }
    
    protected function _initLayoutJsHelpers(Zend_View_Interface $view)
    {
        /* example: 
         * $view->layout()->box_Language = $view->box_Language();*/
    }    
    
    protected function _initLocale(Zend_Controller_Action $controller)
    {
        if ( ! Zend_Registry::isRegistered('Zend_Translate')) { 
            $langService = Model_Service::factory('language');
            $siteService = Model_Service::factory('site');
            $currentSite = $siteService->getCurrent();
            if ($currentSite->default_language_id) {
                $newLang = $langService->get($currentSite->default_language_id);
                $langService->setCurrent($newLang);
                $code2 = $newLang->code2;
            }
            else {
                $code2 = $langService->getCurrent()->code2;
            }
            
            $cache = Zend_Registry::get('Zend_Cache');
            Zend_Locale::setCache($cache);
            $locale = new Zend_Locale;
            $locale->setLocale($code2);
            Zend_Registry::set('Zend_Locale', $locale);
            Zend_Translate::setCache($cache);
            $translate = new Zend_Translate(
                'gettext',
                APPLICATION_PATH . '/i18n/'.$code2.'/kernel.mo',
                $code2
            );
            $module = $controller->getRequest()->getParam('module');
            $trs = array(
                FRONT_APPLICATION_PATH . '/i18n/'.$code2.'/kernel.mo',
                APPLICATION_PATH . '/i18n/'.$code2.'/'.$module.'.mo',
                FRONT_APPLICATION_PATH . '/i18n/'.$code2.'/'.$module.'.mo',
            );
            foreach ($trs as $file) {
                if (file_exists($file)) {
                    $translate->addTranslation($file);
                }
            }
            Zend_Registry::set('Zend_Translate', $translate);
            Zend_Form::setDefaultTranslator($translate);
        }
    }
    
    protected function _initAcl(Zend_Controller_Action $controller, $throwExceptionInsteadOfRedirect)
    {
        /* prepare role for acl request */
        $userService = Model_Service::factory('user');
        $user = $userService->getCurrent();

        if ( ! $user OR  ! ($role = $user->acl_role)) {
            /*if ($controller->getRequest()->getControllerName() != 'auth') {
                $controller->getHelper('Redirector')->gotoUrlAndExit($controller->view->stdUrl(NULL, 'login', 'auth', 'kernel'));
            }*/
            $role = 'guest';
        }

        /* prepare resource name for acl request */
        $module = $controller->getRequest()->getModuleName();
        $filter = new Zend_Filter_Word_DashToCamelCase();
        $acl = Zend_Registry::get('Zend_Acl');
        $resourceAction = get_class($controller).'__'.lcfirst($filter->filter($controller->getRequest()->getActionName())).'Action';
        $resourceController = get_class($controller);
        if ( ! $acl->has($resourceAction)) {
            $resource = $resourceController;
        }
        else {
            $resource = $resourceAction;
        }
        /* request acl for allowance */
        try {
            /*commented due to adding several roles to one user
            $allowed = $acl->isAllowed($role, $resource, 'read');*/
            $allowed = $userService->isAllowedByAcl($user, $resource, 'read', $acl);
        }
        catch (Zend_Acl_Exception $e) {
            throw new App_Event_Observer_Exception(__CLASS__.' says there is no resource named "'.$resource.'" '.(($resource != $resourceAction)?' or "'.$resourceAction.'" ':'').' in '.$module.'/configs/acl.ini (less probably - there is no role "'.$role.'" within kernel/configs/config.ini->[aclRoles]) ');
        }

        /* if not allowed - throw exception */
        if ( ! $allowed) {
            if ($throwExceptionInsteadOfRedirect === TRUE) {
                throw new Zend_Acl_Exception(__CLASS__.' says that current role ('.$role.') has no access to resource '.$resource);
            }
            else {
                $controller->getHelper('Redirector')->gotoUrlAndExit($controller->view->stdUrl(NULL, 'login', 'auth', 'kernel'));
            }
        }        
    }
    
    protected function _initReturnUrl(Zend_Controller_Action $controller)
    {
        $controller->getHelper('ReturnUrl')->validate();        
    }
    
    protected function _initDebug(Zend_Controller_Action $controller)
    {
        if ((APPLICATION_ENV == 'development') AND ($controller->getRequest()->isXmlHttpRequest())) {
            $profiler = new Zend_Db_Profiler_Firebug('DB Queries');
            $profiler->setEnabled(TRUE);
            Zend_Db_Table_Abstract::getDefaultAdapter()->setProfiler($profiler);
        }
    }


}