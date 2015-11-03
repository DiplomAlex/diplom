<?php

require_once 'App/Model/Service/Exception.php';
require_once 'App/DIContainer.php';

class Model_Service
{

    protected static $_singletons = array();

    /**
     * @var App_DIContainer
     */
    protected static $_injector = NULL;

    /**
     * @var array(string)
     */
    protected $_defaultInjections = array(
    );

    public static function getInjector()
    {
        if (self::$_injector === NULL) {
            self::$_injector = new App_DIContainer;
        }
        return self::$_injector;
    }




    public static function factory($serviceName)
    {
        if (empty($serviceName)) {
            throw new Model_Service_Exception('empty $serviceName in Model_Service::factory');
        }
        
        $nameArr = explode('/', $serviceName);

        $filterDTCC = new Zend_Filter_Word_DashToCamelCase;
        foreach ($nameArr as $key=>$val) {
            $nameArr[$key] = $filterDTCC->filter($val);
        }

        if (count($nameArr) > 1) {
            $serviceName = array_pop($nameArr);
            $prefix = str_replace(' ', '_', ucwords(implode(' ', $nameArr))) . '_';
        }
        else {
            $serviceName = array_pop($nameArr);
            $prefix = '';
        }

        $className = $prefix.'Model_Service_' . ucfirst($serviceName);

        if ( ! self::getInjector()->hasInjection($className)) {
            self::getInjector()->inject($className, $className);
        }


        if ( ! isset(self::$_singletons[$className])) {
            try {
                self::$_singletons[$className] = self::getInjector()->getObject($className);
            }
            catch (App_DIContainer_Exception $e) {
                throw new Model_Service_Exception(
                                'Model_Sevice::factory() cannot create model service ' . $className
                              . ' because of error: '. $e->getMessage()
                          );
            }
        }

        $service = self::$_singletons[$className];

        return $service;
    }

}