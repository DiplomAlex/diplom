<?php

class Checkout_Model_Mapper_Array_Payment extends Model_Mapper_Array_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Checkout_Model_Object_Payment',
    	'Model_Collection_Interface' => 'Checkout_Model_Collection_Payment',
    );

    /**
     * @var array
     */
    protected $_arrayFields = array(
        'client_requisites',
        'seller_requisites',
        'params',
        'allowed_shipments'
    );

    public function mapSimpleObject(Model_Object_Interface $obj, array $values)
    {
        $arrayFields = $this->_arrayFields;
        foreach ($obj->getElements() as $key=>$value) {
            if (array_key_exists($key, $values)) {
                $obj->{$key} = $values[$key];
            }
            else if (in_array($key, $arrayFields)) {
                $arr = array();
                $found = FALSE;
                foreach ($values as $innerKey=>$innerValue) {
                    $strlenPlus2 = strlen($key)+2;
                    if (substr($innerKey, 0, $strlenPlus2) == $key.'__') {
                        $found = TRUE;
                        $arrayKey = substr($innerKey, $strlenPlus2);
                        $arr[$arrayKey] = $innerValue;
                    }
                }
                if ($found) {
                    $obj->{$key} = $arr;
                }
            }
        }
        return $obj;
    }

}