<?php

class Checkout_Model_Mapper_XML_CartItem extends Model_Mapper_XML_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Checkout_Model_Object_CartItem',
        'Model_Collection_Interface' => 'Checkout_Model_Collection_CartItem',
    );

    public function makeSimpleObjectFromArray(array $values)
    {
        $obj = $this->getInjector()->getObject('Model_Object_Interface');
        foreach ($values as $key=>$val) {
            if ($obj->hasElement($key)) {
                $obj[$key] = $val;
            }
        }
        return $obj;
    }

}