<?php

class Api_Model_Mapper_Db_History extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Api_Model_Object_History',
        'Model_Db_Table_Interface'   => 'Api_Model_Db_Table_History',
        'Model_Collection_Interface' => 'Api_Model_Collection_History'
    );

}