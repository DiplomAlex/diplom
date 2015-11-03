<?php

class Catalog_Model_Object_Category extends Model_Object_Abstract
{

	public function init()
	{

        $this->addElements(array(
            'id', 'seo_id',
            'status',
            'sort',
            'seo_id',
            'design', 
        	'name',
            'brief',
            'full',
            'html_title',
            'meta_description',
            'meta_keywords',
            'date_added', 'date_changed',
            'adder_id', 'changer_id',
            'rc_id', 'rc_id_filename', 'rc_id_preview', 'rc_id_preview2', 'rc_id_preview3',
            'parent_id', 'tree_id', 'tree_level', 'tree_left', 'tree_right', 'children_count',
            'items_count',
            'param1', 'param2', 'param3',
            'site_ids',
            /* for importer */
            'delete',
            'guid',
            'parent_guid',
            'filter_id'
        ));

	}

}