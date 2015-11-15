<?php

class Model_Object_ArduinoLine extends Model_Object_Abstract
{
    const MINUTES_FOR_USER = 2;

    public function init()
    {
        $this->addElements(array(
            'id',
            'sketch_id',
            'date_added',
            'date_start',
        ));
    }
}