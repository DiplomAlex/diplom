<?php

class Social_Model_Object_Advert extends Model_Object_Abstract
{

    public function init()
    {
        $this -> addElements(array(
            'id',
            'status',
            'category_id',
            'category_name',
            'parent_id',
            'group_key',
            'text',
            'adder_id',
            'adder_login',
            'adder_name',
            'changer_id',
            'changer_login',
            'changer_name',
            'date_added',
            'date_changed',
            'category_fields',
            'fields',
            'automate',
            'qty',
            'price',
        ));
    }

}
