<?php

class Model_Object_Language extends Model_Object_Abstract implements Model_Object_Language_Interface
{

	public function init()
	{
		$this -> addElements(array('id', 'status', 'sort', 'title', 'code2', 'code3', 'is_default'));
	}

}