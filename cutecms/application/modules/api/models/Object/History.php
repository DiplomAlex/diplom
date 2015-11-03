<?php

class Api_Model_Object_History extends Model_Object_Abstract
{

    public function init()
    {

        $this->addElements(array(
            'id',
            'data',
            'request_method',
            'request',
            'response',
        ));
    }

}