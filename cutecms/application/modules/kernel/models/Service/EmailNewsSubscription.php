<?php

class Model_Service_EmailNewsSubscription extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
    	'Model_Mapper_Interface' => 'Model_Mapper_Db_EmailNewsSubscription',
        'Model_Object_Interface' => 'Model_Object_EmailNewsSubscription',
    );
    
}