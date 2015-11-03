<?php

abstract class View_Helper_Observer_Abstract extends App_Event_Observer implements Zend_View_Helper_Interface
{

    /**
     * View object
     *
     * @var Zend_View_Interface
     */
    protected $view = NULL;

    /**
     * Set the View object
     *
     * @param  Zend_View_Interface $view
     * @return Zend_View_Helper_Abstract
     */
    public function setView(Zend_View_Interface $view)
    {
        $module = $this->_getModule();
        if ($module != 'kernel') {
            /**
             * "virtualize" view environment such as it would be when $module is current module:
             * helpers paths should be in order of: 1) this module, 2) kernel, 3) libs
             * scripts paths should have order: 1) this module, 2) kernel
             */
            $this->view = clone $view;
            $moduleViewsPath = realpath(APPLICATION_PATH.'/modules/'.lcfirst($module).'/views');
            $helperPaths = $this->view->getHelperPaths();
            $this->view->setHelperPath($moduleViewsPath.'/helpers', $module.'_View_Helper_');
            foreach ($helperPaths as $prefix=>$path) {
                $this->view->addHelperPath($path, $prefix);
            }
            $scriptPaths = $this->view->getScriptPaths();
            $this->view->setScriptPath($moduleViewsPath.'/scripts');
            foreach ($helperPaths as $path) {
                $this->view->addScriptPath($path);
            }

        }
        else {
            $this->view = $view;
        }
        return $this;
    }

    /**
     * Strategy pattern: currently unutilized
     *
     * @return void
     */
    public function direct()
    {
    }


    protected function _getModule()
    {
        $arr = explode('_', get_class($this));
        if ($arr[0] == 'View') {
            $result = 'kernel';
        }
        else {
            $result = $arr[0];
        }
        return $result;
    }

    public function __construct()
    {
        $this->setView(Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view);
    }

/*
    protected function _runModuleHelper($name, array $params = array())
    {
        $module = $this->_getModule();
        $class = 'View_Helper_'.ucfirst($name);
        if ($module != 'kernel') {
            $class = $module.'_'.$class;
        }
        $obj = new $class;
        $obj->setView($this->view);
        return call_user_func_array(array($obj, $name), $params);
    }
*/

}
