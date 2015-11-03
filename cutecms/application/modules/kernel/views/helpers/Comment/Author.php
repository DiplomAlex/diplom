<?php

class View_Helper_Comment_Author extends Zend_View_Helper_Abstract
{
    
    public function comment_Author(Model_Object_Interface $comment) 
    {
        if ($comment->adder_id) {
            $html = '<a href="'.$this->view->stdUrl(array('id'=>$comment->adder_id), 'edit', 'admin-user', 'kernel').'">'.$comment->adder_name.'</a>';
        }
        else if ($comment->adder_email) {
            $html = '<a href="mailto:'.$comment->adder_email.'">'.$comment->adder_name.'</a>';
        }
        else {
            $html = $comment->adder_name . ' ('.$this->view->translate('Anonym').')';
        }
        return $html;
    }
    
}