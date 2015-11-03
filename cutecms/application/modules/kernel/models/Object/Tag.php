<?php

class Model_Object_Tag extends Model_Object_Abstract
{ 
    
    public function init()
    {
        $this->addElements(array(
            'id', 
            'tag',
            'norma',

            'date_added', 'date_changed',
            'adder_id', 'adder_login', 'adder_name', 
            'changer_id', 'changer_login', 'changer_name', 

            'parent_id', 'tree_id', 'tree_level', 'tree_left', 'tree_right', 'children_count',
        
            'content_type', 'content_id', 'content_seo_id', 'content_title', 
        ));
    }
    
}