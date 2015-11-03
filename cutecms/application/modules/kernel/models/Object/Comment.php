<?php

class Model_Object_Comment extends Model_Object_Abstract
{
    
    public function init()
    {
        $this->addElements(array(
            'id',
        
            'status',
            'subject',
            'text',
        
            'date_added', 'date_changed',
            'adder_id', 'adder_login', 'adder_name', 'adder_email',
            'changer_id', 'changer_login', 'changer_name', 'changer_email',

            'parent_id', 'tree_id', 'tree_level', 'tree_left', 'tree_right', 'children_count', 'all_children_count',
        
            'rc_id', 'rc_id_filename', 'rc_id_mime', 'rc_id_width', 'rc_id_height',
            'rc_id_preview', 'rc_id_prv_width', 'rc_id_prv_height',
            'rc_id_preview2', 'rc_id_prv2_width', 'rc_id_prv2_height',
            'rc_id_preview3', 'rc_id_prv3_width', 'rc_id_prv3_height',
        
            'content_type', 'content_id', 'content_seo_id', 'content_title',
        ));
    }
    
}