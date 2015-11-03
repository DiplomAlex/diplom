<?php

class BootstrapInstaller extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Constructor
     *
     * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
     * @return void
     */
    public function __construct($application)
    {
        App_Profiler::enable();
        App_Profiler::start('Bootstrap::init');
        if (PHP_SAPI == 'cli') {
            $this->unregisterPluginResource('Session');
        }
        parent::__construct($application);
    }


    protected function _initAutoload()
    {
        $autoloader = new App_Loader_Autoloader_Resource(array(
            	'namespace' => '',
            	'basePath'  => APPLICATION_PATH,
        ));
        $autoloader->addResourceTypes(array(
            'model'   => array(
                'namespace' => 'Model',
                'path'      => array('library/App/Model', 'modules/kernel/models'),
            ),
            'forms'   => array(
                'namespace' => 'Form',
                'path'      => 'modules/kernel/forms',
            ),
            'controllerplugins'   => array(
                'namespace' => 'Controller_Plugin',
                'path'      => 'modules/kernel/controllers/Plugin',
            ),
            'controllerhelpers'   => array(
                'namespace' => 'Controller_Helper',
                'path'      => 'modules/kernel/controllers/Helper',
            ),
		));
        $autoloader->removeResourceType('mappers');
        $autoloader->addResourceTypes(array(
            'viewhelper'   => array(
                'namespace' => 'View_Helper',
                'path'      => 'modules/kernel/views/helpers',
            ),
        ));
        return $autoloader;
    }

    protected function _initSettings()
    {
        if (APPLICATION_ENV == 'production') {
            error_reporting(0);
        }
        else {
            error_reporting(E_ALL);
            ini_set('display_startup_errors', 1);
            ini_set('display_errors', 1);
        }
        ini_set('memory_limit', '64M');
        ini_set('upload_max_filesize', '10M');

        if (PHP_SAPI == 'cli') {
            /** disable cookies */
            ini_set('session.use_cookies', false);
        }

    }

    protected function _initLocale(array $options = array())
    {
        App_Profiler::start('Bootstrap::initLocale');
        $this->bootstrap('Config');
        $code2 = $this->getOption('locale');
        $locale = new Zend_Locale;
        $locale->setLocale($code2);
        Zend_Registry::set('Zend_Locale', $locale);
        $translate = new Zend_Translate(
            'gettext',
            APPLICATION_PATH . '/i18n/'.$code2.'/kernel.mo',
            $code2
        );
        Zend_Form::setDefaultTranslator($translate);
        Zend_Registry::set('Zend_Translate', $translate);
        App_Profiler::stop('Bootstrap::initLocale');
    }

    protected function _initView(array $options = array())
    {
        App_Profiler::start('Bootstrap::initView');

		$view = new Zend_View($options);

        $view->addHelperPath(APPLICATION_PATH . '/modules/kernel/views/helpers','View_Helper_');
        $view->addHelperPath(APPLICATION_PATH . '/library/App/View/Helper','App_View_Helper_');
        $view->addScriptPath(APPLICATION_PATH . '/modules/kernel/views/scripts');

		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
        Zend_Paginator::addAdapterPrefixPath('App_Paginator_Adapter_', APPLICATION_PATH . '/library/App/Paginator/Adapter');

        Zend_Registry::set('skin', $this->getOption('skin'));

        App_Profiler::stop('Bootstrap::initView');
        return $view;
    }

    protected function _initConfig()
    {
        /*$config = new Zend_Config_Ini(APPLICATION_PATH . '/modules/kernel/configs/config.ini');*/
        $config = Model_Service::factory('config')->read('config');
        Zend_Registry::set('config', $config);
        return $config;
    }


    protected function _initAcl()
    {
        $this->bootstrap('Config');
        $acl = new App_Acl;
        $acl->addRoles(Zend_Registry::get('config')->aclRoles);
        $aclIni = new Zend_Config_Ini(APPLICATION_PATH . '/modules/kernel/configs/acl.ini');
        $acl->addResources($aclIni->resources);
        $acl->addAllows($aclIni->allow);
        $acl->addDenies($aclIni->deny);
        Zend_Registry::set('Zend_Acl', $acl);
        return $acl;
    }


    protected function _initEvents()
    {
        App_Profiler::start('Bootstrap::initEvents');
        $this->bootstrap('Config');
        $this->bootstrap('Frontcontroller');
        $xml = new Zend_Config_Xml(APPLICATION_PATH . '/modules/kernel/configs/events.xml');
        $events = array();
        foreach ($xml->events as $eventName => $event) {
            $events[$eventName] = $event->observers;
        }
        Zend_Registry::set('events', $events);
        App_Profiler::stop('Bootstrap::initEvents');
        return $events;
    }


    protected function _initObservers()
    {
        $this->bootstrap('Events');
        $this->getResource('Autoload')->addResourceTypes(array(
            'observers' => array(
                'namespace' => 'Observer',
                'path'      => 'modules/kernel/controllers/Observer',
            ),
        ));

    }


    protected function _initRoute()
    {
        App_Profiler::start('Bootstrap::initRoute');
        $this->bootstrap('Frontcontroller');
        $fc = $this->getResource('FrontController');

        $router = new App_Controller_Router_Rewrite;
        $router->addRoute(
            'index_php',
            new Zend_Controller_Router_Route_Static(
                'index.php',
                array(
                    'controller' => $fc->getDefaultControllerName(),
                    'action' => $fc->getDefaultAction(),
                    'module' => $fc->getDefaultModule(),
                )
            )
        );
        $fc->setRouter($router);
        App_Profiler::stop('Bootstrap::initRoute');
        return $router;
    }

/*
    protected function _initZFDebug()
    {
        if (APPLICATION_ENV !== 'production') {
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('ZFDebug');
            $this->bootstrap('frontController');
            $options = array(
                'plugins' => array(
                    'Variables',
                    'File' => array('base_path' => APPLICATION_PATH.'/../'),
                    'Memory',
                    'Time',
                    'Registry',
                    'Exception',
                    'Html',
                )
            );
            $zfdebug = new ZFDebug_Controller_Plugin_Debug($options);
            $zfdebug->registerPlugin(new App_Controller_Plugin_Debug_Plugin_Dump());
            $this->getResource('frontController')->registerPlugin($zfdebug);
            App_Debug::enable();
        }
    }
*/

    public function run()
    {
        App_Profiler::stop('Bootstrap::init');

        parent::run();

    }


}

