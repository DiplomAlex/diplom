<?php

class Model_Object_Article extends Model_Object_Abstract
{
    
    const TOPIC_RELATION_ONE_TO_MANY = 'one';
    const TOPIC_RELATION_MANY_TO_MANY = 'many';
        
    public function init()
    {
        $this->addElements(array(
            'id',
            'seo_id',
            'status',
            'sort',
            'title',
            'brief',
            'text',
            'author',
            'html_title', 'meta_keywords', 'meta_description',
            'date_added', 'date_changed',
            'adder_id', 'adder_login', 'adder_name',
            'changer_id', 'changer_login', 'changer_name',        
            'topics',
        
            'rc_id', 'rc_id_filename', 'rc_id_mime', 'rc_id_width', 'rc_id_height',
            'rc_id_preview', 'rc_id_prv_width', 'rc_id_prv_height',
            'rc_id_preview2', 'rc_id_prv2_width', 'rc_id_prv2_height',
                
            /* when relation to topics is n-1 then current topic data can be used */
            'topic_id', 'topic_name', 'topic_seo_id', 
        
            'site_ids',
        ));
    }
    
    public function getMode()
    {
        return self::TOPIC_RELATION_ONE_TO_MANY;
    }
    
}