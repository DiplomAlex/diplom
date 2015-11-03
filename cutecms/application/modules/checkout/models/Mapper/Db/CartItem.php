<?php

class Checkout_Model_Mapper_Db_CartItem extends Model_Mapper_Db_Abstract
{
    protected $_defaultInjections
        = array(
            'Model_Db_Table_Interface'            => 'Checkout_Model_Db_Table_CartItem',
            'Model_Object_Interface'              => 'Checkout_Model_Object_CartItem',
            'Model_Collection_Interface'          => 'Checkout_Model_Collection_CartItem',
            'Model_Mapper_Interface'              => 'Checkout_Model_Mapper_Db_CartItem',
        );
}