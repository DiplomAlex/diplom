<?php

class Model_Object_WhiteIp extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array('id', 'ip', 'provider'));
    }

}