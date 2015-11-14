<?php

class Model_Object_Arduino extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'id',
            'adder_id', 'date_added',
            'checker_id', 'date_checked',
            'sketch',
            'console',
        ));
    }
}