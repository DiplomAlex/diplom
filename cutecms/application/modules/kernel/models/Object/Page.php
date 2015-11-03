<?php

class Model_Object_Page extends Model_Object_Abstract
{

	public function init()
	{
        $this->addElements(array(
            'id',
            'code',
            'status',
            'sort',
            'seo_id',
            'code',
            'driver',
            'title',
            'brief',
            'full',
            'html_title',
            'meta_description',
            'meta_keywords',
            'adder_id',
            'changer_id',
            'flag1', 'flag2',
            'rc_id', 'rc_id_filename', 'rc_id_preview',
            'site_ids',
            'video',
            'banner',
        ));
	}

}