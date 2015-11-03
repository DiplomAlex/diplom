<?php

class App_Controller_Router_Rewrite extends Zend_Controller_Router_Rewrite
{

    /**
     * difference between this and standard Zend_Controller_Router_Rewrite
     * is that "reset" parameter for "assemble" method resets global parameters too !
     */

    /**
     * Generates a URL path that can be used in URL creation, redirection, etc.
     *
     * @param  array $userParams Options passed by a user used to override parameters
     * @param  mixed $name The name of a Route to use
     * @param  bool $reset Whether to reset to the route defaults ignoring URL params
     * @param  bool $encode Tells to encode URL parts on output
     * @throws Zend_Controller_Router_Exception
     * @return string Resulting absolute URL path
     */
    public function assemble($userParams, $name = null, $reset = false, $encode = true)
    {
        if ($name == null) {
            try {
                $name = $this->getCurrentRouteName();
            } catch (Zend_Controller_Router_Exception $e) {
                $name = 'default';
            }
        }

        /**
         * old code in Zend_Controller_Router_Rewrite:
         * $params = array_merge($this->_globalParams, $userParams);
         */
        if ($reset === FALSE) {
            $params = array_merge($this->_globalParams, $userParams);
        }
        else {
            $params = $userParams;
        }



        $route = $this->getRoute($name);
        $url   = $route->assemble($params, $reset, $encode);

        if (!preg_match('|^[a-z]+://|', $url)) {
            $url = rtrim($this->getFrontController()->getBaseUrl(), '/') . '/' . $url;
        }

        return $url;
    }


}