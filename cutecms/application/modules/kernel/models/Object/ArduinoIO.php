<?php

class Model_Object_ArduinoIO extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'id',
            'sketch_id',
            'in',
            'out',
        ));
    }
}