<?php

class View_Helper_Box_Login extends Zend_View_Helper_Abstract
{

	protected $_deniedRedirect = array(
		'auth/login',
		'auth/logout',
	);

    public function box_Login()
    {
        $controller = Zend_Controller_Front::getInstance();
        $rc = $controller->getRequest()->getControllerName().'/'.$controller->getRequest()->getActionName();
        if ( ! in_array($rc, $this->_deniedRedirect)) {
            $redirectHref = $this->view->url(array(), 'frontend_index');
        }
        else {
            $redirectHref = NULL;
        }
        $userService = Model_Service::factory('user');
        $alreadyAuthorized = $userService->isAuthorized();
        $user = $userService->getCurrent();
        return $this->view->partial('box/login.phtml', array(
            'redirectHref'=>$redirectHref,
            'alreadyAuthorized'=>$alreadyAuthorized,
            'user'=>$user,
        ));
    }

}