<?php

class Model_Mapper_Db_Vertical extends Model_Mapper_Db_Abstract
{

	protected $_defaultInjections = array(
		'Model_Db_Table_Interface' => 'Model_Db_Table_Verticals',
		'Model_Object_Interface' => 'Model_Object_Vertical',
        'Model_Collection_Interface' => 'Model_Collection_Vertical',
	);

}
