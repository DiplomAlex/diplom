<?php

class Model_Mapper_Db_EmailNewsSubscription extends Model_Mapper_Db_Abstract
{
	protected $_defaultInjections = array(
		'Model_Db_Table_Interface' => 'Model_Db_Table_EmailNewsSubscription',
		'Model_Object_Interface' => 'Model_Object_EmailNewsSubscription',
        'Model_Collection_Interface' => 'Model_Collection_EmailNewsSubscription',
	);
}