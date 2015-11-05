<?php

class View_Helper_Box_Stats extends Zend_View_Helper_Abstract
{

    public function box_Stats()
    {
        /**
         * stats box is visible only for logged in users
         */
        if ($user = Model_Service::factory('user')->getCurrent()) {
            $arr['loggedIn'] = TRUE;
            $arr['showClientStats'] = ($user->acl_role == 'client');
            $arr['showManagerStats'] = ($user->acl_role == 'manager');
        }
        else {
            $arr['loggedIn'] = FALSE;
        }
        $result = $this->view->partial('box/stats.phtml', $arr);
        return $result;
    }

}
