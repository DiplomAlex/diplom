<?php

class Model_Object_NewsTopic extends Model_Object_Abstract
{

	public function init()
	{
        $this->addElements(array(
            'id',
            'status',
            'sort',
            'name',
            'brief',
            'full',
            'date_added',
            'adder_id',
            'date_changed',
            'changer_id',
            'news_count',
            'site_ids',
        ));
	}

}