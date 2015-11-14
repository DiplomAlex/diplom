<?php

class Model_Service_ArduinoIO extends Model_Service_Abstract
{
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_ArduinoIO',
        'Model_Mapper_Interface' => 'Model_Mapper_Db_ArduinoIO',
    );
}
