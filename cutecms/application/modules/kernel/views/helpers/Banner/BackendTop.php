<?php

class View_Helper_Banner_BackendTop extends Zend_View_Helper_Abstract
{

    public function banner_BackendTop()
    {
        if ($user = Model_Service::factory('user')->getCurrent()) {
            if ($user->acl_role == 'client') {
                $innerHtml = $this->view->banner('backend_top_client');
            }
            else if ($user->acl_role == 'diller') {
                $innerHtml = $this->view->banner('backend_top_diller');
            }
            else {
                $innerHtml = $this->view->banner('backend_top_coworker');
            }
        }
        else {
            $innerHtml = $this->view->banner('backend_top_guest');
        }
        return $innerHtml;
    }

}