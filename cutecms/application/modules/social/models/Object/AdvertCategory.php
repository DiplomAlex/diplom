<?php

class Social_Model_Object_AdvertCategory extends Model_Object_Abstract
{

    public function init()
    {
        $this -> addElements(array(
            'id',
            'status',
            'sort',
            'name',
            'brief',
            'full',
            'adder_id',
            'adder_login',
            'adder_name',
            'changer_id',
            'changer_login',
            'changer_name',
            'date_added',
            'date_changed',
            'fields',
            'advert_count',
        ));
    }

}