<?php

class Model_Object_Tip extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'id',
            'status',
            'destination',
            'role',
            'title', 'text',
            'adder_id', 'changer_id',
            'date_added', 'date_changed',
        ));
    }

}