<?php

class Issues_View_Helper_Issue_Status extends Zend_View_Helper_Abstract
{

    public function issue_Status($issue)
    {
        if ($issue instanceof Model_Object_Interface ) {
            $status = $issue->status;
        }
        else {
            $status = $issue;
        }
        $html = Model_Service::factory('issues/issue')->getStatusById($status, TRUE);
        return $html;
    }

}