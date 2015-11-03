<?php

class Catalog_Model_Service_ItemViewed extends Model_Service_Abstract
{
	protected $_defaultInjections = array(
    	'Model_Mapper_Interface' => 'Catalog_Model_Mapper_Db_ItemViewed',
        'Model_Object_Interface' => 'Catalog_Model_Object_ItemViewed',
    );
	
	public function add($visitor)
	{
		return $this->getMapper()->add($visitor);
	}

	public function getByIpAndItemId($visitor)
	{
		return $this->getMapper()->fetchByIpAndItemId($visitor);
	}
	
	public function getByIpAndLimit($Ip, $limit)
	{
		return $this->getMapper()->fetchByIpAndLimit($Ip, $limit);
	}
}
	