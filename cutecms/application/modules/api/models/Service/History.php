<?php

class Api_Model_Service_History extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Api_Model_Object_History',
        'Model_Mapper_Interface'     => 'Api_Model_Mapper_Db_History',
        'Model_Collection_Interface' => 'Api_Model_Collection_History'
    );

}