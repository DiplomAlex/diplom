<?php

class View_Helper_StdUrl extends Zend_View_Helper_Abstract
{

    /**
     * return standard url
     * just sometimes (when seo urls not needed) more usefull than usual url()
     *
     * @param array parameters to router
     * @param string action
     * @param string controller
     * @param string module
     * @return string
     */
    public function stdUrl(array $params = NULL, $action = NULL, $controller = NULL, $module = NULL)
    {
        $checkRouteName = '';
        $checkRoute = TRUE;
        if ($params == NULL) {
            $params = array();
        }
        if ($module !== NULL) {
            $params['module'] = $module;
            $checkRouteName .= $module.'-';
        }
        if ($controller !== NULL) {
            $params['controller'] = $controller;
            $checkRouteName .= $controller.'-';
        }
        if ($action !== NULL) {
            $params['action'] = $action;
            $checkRouteName .= $action;
        }
        else {
            $checkRoute = FALSE;
        }
        if (isset($params['encode'])) {
            $encode = $params['encode'];
            unset($params['encode']);
        }
        else {
            $encode = TRUE;
        }
        if (isset($params['reset'])) {
            $reset = $params['reset'];
            unset($params['reset']);
        }
        else {
            $reset = FALSE;
        }
        if (isset($params['route'])) {
            $name = $params['route'];
            unset($params['route']);
        }
        else {
            $name = 'default';
        }
        
        if ($checkRoute AND ($name == 'default') AND (Zend_Controller_Front::getInstance()->getRouter()->hasRoute($checkRouteName))) {
            $name = $checkRouteName;
        }
        
        $url = 'http://'.$this->getCurrentHost().$this->view->url($params, $name, $reset, $encode);
        if ($name == 'default') {
            $url = trim($url, '/');
            $url = $url.'/';
        }
        return $url;
    }

    public function getCurrentHost()
    {
        return Model_Service::factory('site')->getCurrentHost();
    }

}