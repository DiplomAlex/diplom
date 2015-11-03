<?php

class Model_Object_Currency extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'id',
            'code',
            'name',
            'signPre', 'signPost',
            'rate', 'rateNonCache',
            'is_default',
        ));
    }

}