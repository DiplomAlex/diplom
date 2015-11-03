<?php

class Model_Object_ArticleTopic extends Model_Object_Abstract
{
    
    public function init()
    {
        $this->addElements(array(
            'id',
            'seo_id',
            'name',
            'brief',
            'full',
            'html_title', 'meta_keywords', 'meta_description',
            'date_added', 'date_changed',
            'adder_id', 'adder_login', 'adder_name',
            'changer_id', 'changer_login', 'changer_name',
            'parent_id', 'tree_id', 'tree_level', 'tree_left', 'tree_right', 'children_count',
            'article_count',
            'site_ids',
        ));
    }
    
}