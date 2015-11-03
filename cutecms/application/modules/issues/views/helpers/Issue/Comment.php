<?php

class Issues_View_Helper_Issue_Comment extends Zend_View_Helper_Abstract
{

    /**
     * 
     * @param mixed Model_Object_Interface | array
     */
    public function issue_Comment($issue)
    {
        $statuses = Zend_Registry::get('issues_config')->statuses;
        App_Debug::dump($issue);
        App_Debug::dump($statuses->toArray());
        if (($issue['status'] == $statuses->resolved) OR ($issue['status'] == $statuses->closed)) {
            $class = 'issue_comment_closed';
        }
        else if ($issue['status'] == $statuses->processing) {
            $class = 'issue_comment_processing';
        }
        else {
            $class = 'issue_comment';
        }
        $html = '<div class="'.$class.'">'.$issue['comment'].'</div>';
        return $html;
    }

}