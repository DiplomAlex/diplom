<?php

class View_Helper_Currency extends Zend_View_Helper_Abstract
{
	public function currency()
	{
		return Model_Service::factory('currency')->getCurrent();		
	}
}