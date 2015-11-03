<?php

class Model_Object_News extends Model_Object_Abstract
{

	public function init()
	{
        $this -> addElements(array(
            'id',
            'status',
            'sort',
            'seo_id',
            'rc_id',
            'rc_id_filename',
            'rc_id_preview',
            'rc_id_preview2',
            'date_publish',
            'title',
            'title2',
            'announce',
            'full',
            'html_title',
            'meta_description',
            'meta_keywords',
            'date_added',
            'adder_id',
            'date_changed',
            'changer_id',
            'ntopic_id',
            'ntopic_name',
            'ntopic_seo_id',
            'ntopic_brief',
            'ntopic_news_count',
            'ntopic_subscribed',
            'is_new',
            'send_to_subscribers',
            'site_ids',
            'main_page',
        ));
	}

}