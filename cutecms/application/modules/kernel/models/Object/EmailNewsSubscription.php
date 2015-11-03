<?php

class Model_Object_EmailNewsSubscription extends Model_Object_Abstract
{

	public function init()
	{
        $this->addElements(array(
            'id',
            'email',
        ));
	}

}