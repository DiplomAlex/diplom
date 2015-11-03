<?php

class Api_Model_Object_Remain extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'id',
            'sku',
            'code',
            'material',
            'probe',
            'size',
            'characteristics',
            'weight',
            'price',
            'in_stock',
        ));
    }

}