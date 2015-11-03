<?php

class Model_Object_Faq extends Model_Object_Abstract
{

	public function init()
	{
        $this->addElements(array(
            'id',
            'status',
            'sort',
            'seo_id',
            'quest',
            'brief',
            'full',
            'html_title',
            'meta_description',
            'meta_keywords',
            'adder_id',
            'changer_id',
            'site_ids',
        ));
	}

}