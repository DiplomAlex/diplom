<?php

class Model_Object_Role extends Model_Object_Abstract
{

	public function init()
	{
        $this->addElements(array(
            'id',
            'sort',
            'status',
            'name',
            'brief',
            'acl_role',
            'param1', 
            'rc_id',
            'rc_id_filename',
            'rc_id_preview',
        ));

	}

}