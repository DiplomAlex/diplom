<?php

class Issues_Model_Object_Comment extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'id',
            'issue_id',
            'status', 'status_history',
            'date_added', 'date_changed',
            'adder_id', 'adder_name', 'adder_login', 'adder_preview',
            'changer_id', 'changer_name', 'changer_login',
            'text',
            'parent_id', 'tree_id', 'tree_level', 'tree_left', 'tree_right', 'children_count',
        ));
    }

}