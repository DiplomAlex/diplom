<?php

class Model_Mapper_Db_ArduinoIO extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Model_Db_Table_ArduinoIO',
        'Model_Collection_Interface' => 'Model_Collection_ArduinoIO',
        'Model_Object_Interface' => 'Model_Object_ArduinoIO',
    );

    public function fetchByLab($id)
    {
        return $this->fetchComplex(array('aio_sketch_id = ? ' => $id));
    }
}