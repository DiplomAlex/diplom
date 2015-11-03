<?php

class Model_Object_Banner extends Model_Object_Abstract
{

	public function init()
	{

		$this -> addElements(array(
                                'id',
                                'sort',
                                'status',
                                'place',
                                'name',
                                'html',
                                'text',
                                'image_id',
                                'link',
                                'image_id_filename',
                                'image_id_width',
                                'image_id_height',
                                'date_added',
                                'date_changed',
		                        'site_ids',
                            ));

	}


}