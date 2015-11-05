<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Constructor
     *
     * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
     * @return void
     */
    public function __construct($application)
    {
        if (APPLICATION_ENV == 'development') {
            App_Profiler::enable();
        }
        App_Profiler::start('Bootstrap::init');
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
                'namespace' => 'Controller_Action_Helper',
                'path'      => 'modules/kernel/controllers/Helper',
            ),
            'viewhelper'    => array(
                'namespace' => 'View_Helper',
                'path'      => 'modules/kernel/views/helpers',
            ),
            'validate'      => array(
                'namespace' => 'Form_Validate',
                'path'      => 'modules/kernel/forms/Validate',
            ),
        ));
        $autoloader->removeResourceType('mappers');

        return $autoloader;
    }
    
    /*
    protected function _initControllers()
    {
        $controllersPath = '/modules/kernel/controllers/';
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . $controllersPath . 'Helper',
            'Controller_Action_Helper'
        );
    }
    */

    protected function _initSettings()
    {
        if (APPLICATION_ENV == 'production') {
            error_reporting(0);
        }
        else {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
        }

    }

    protected function _initDbLocaleAndTimeZone(array $options = array())
    {
        App_Profiler::start('Bootstrap::initLocale');
        $this->bootstrap('Db');        
        $db = $this->getResource('db');

        $db->query("SET NAMES 'utf8'");
        $db->query("SET CHARACTER SET 'utf8'");
        $db->query("SET time_zone = '+02:00'");
        
        date_default_timezone_set('Europe/Kiev');

        App_Profiler::stop('Bootstrap::initLocale');
    }

    protected function _initView(array $options = array())
    {
        App_Profiler::start('Bootstrap::initView');
        $this->bootstrap('Db');

		$view = new Zend_View($options);
		
        $view->addHelperPath(APPLICATION_PATH . '/modules/kernel/views/helpers','View_Helper_');
        $view->addHelperPath(APPLICATION_PATH . '/library/App/View/Helper','App_View_Helper_');
        $view->addScriptPath(APPLICATION_PATH . '/modules/kernel/views/scripts');        
		

		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

        App_Profiler::stop('Bootstrap::initView');
        return $view;
    }



    protected function _initRoute()
    {
        App_Profiler::start('Bootstrap::initRoute');
        $this->bootstrap('Frontcontroller');
        $router = new App_Controller_Router_Rewrite;
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/routes.xml', NULL, array('allowModifications' => TRUE));
        $frontConfigFilename = FRONT_APPLICATION_PATH . '/configs/routes.xml';
        if ((FRONT_APPLICATION_PATH != APPLICATION_PATH) AND (file_exists($frontConfigFilename))) {
            $frontConfig = new Zend_Config_Xml($frontConfigFilename);
            $config->merge($frontConfig);
            $router->addConfig($config); 
        }       
        $this->getResource('Frontcontroller')->setRouter($router);
        App_Profiler::stop('Bootstrap::initRoute');
        return $router;
    }



    protected function _initConfig()
    {
        $config = Model_Service::factory('config')->read('config', NULL, FALSE);
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

        if (!$acl->hasRole('admin'))
            App_PreBoot::stripRequestXssArray();
        
        return $acl;
    }


    protected function _initEvents()
    {
        App_Profiler::start('Bootstrap::initEvents');
        $this->bootstrap('Config');
        $this->bootstrap('Frontcontroller');
        $this->bootstrap('Route');
        $xml = new Zend_Config_Xml(APPLICATION_PATH . '/modules/kernel/configs/events.xml');
        $events = array();
        foreach ($xml->events as $eventName => $event) {
            $events[$eventName] = $event->observers;
        }
        Zend_Registry::set('events', $events);
        foreach ($xml->modules as $moduleName => $module) {
            $bootClass = ucfirst($moduleName) . '_Bootstrap';
            $bootFile = APPLICATION_PATH . '/modules/'.$moduleName.'/Bootstrap.php';
            if (file_exists($bootFile)) {
                include_once($bootFile);
                $boot = new $bootClass($this->getApplication());
            }
        }
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

    protected function _initSAPI()
    {
        $this->bootstrap('Config');
        $this->bootstrap('FrontController');
        $this->bootstrap('Db');
    }

    protected function _initCache()
    {
        $config = $this->bootstrap('Config');
        $options = $this->getOption('cache');
        $frontendOptions = array(
           'lifetime' => $options['lifetime'],
           'automatic_serialization' => TRUE,
        );
        $backendOptions = array(
            'cache_dir' => realpath($options['dir']),
            'cache_file_umask' => 0666,
        );

        $cache = Zend_Cache::factory('Core',
                                     'File',
                                     $frontendOptions,
                                     $backendOptions);
        if ((APPLICATION_ENV == 'development') OR ($_GET['clean_cache'] == 'clean_cache')) {
            $cache->clean();
        }
        Zend_Registry::set('Zend_Cache', $cache);

        if (APPLICATION_ENV !== 'development') {
            Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        }

        return $cache;
    }

    protected function _initZFDebug()
    {
        if ((APPLICATION_ENV == 'development') OR ($_GET['debug'] == 'debug')) {
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('ZFDebug');
            $this->bootstrap('db');
            $this->bootstrap('cache');
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
                    'Cache' => array('backend' => $this->getResource('cache')->getBackend()),
                )
            );
            $zfdebug = new ZFDebug_Controller_Plugin_Debug($options);
            $zfdebug->registerPlugin(new ZFDebug_Controller_Plugin_Debug_Plugin_Database());
            $zfdebug->registerPlugin(new App_Controller_Plugin_Debug_Plugin_Dump());
            $this->getResource('frontController')->registerPlugin($zfdebug);
            App_Debug::enable();
        }
    }

    public function run()
    {
        App_Profiler::stop('Bootstrap::init');
        App_Event::factory('Bootstrap__beforeRun', array($this))->dispatch();

        parent::run();

        App_Event::factory('Bootstrap__afterRun', array($this))->dispatch();
    }


}

