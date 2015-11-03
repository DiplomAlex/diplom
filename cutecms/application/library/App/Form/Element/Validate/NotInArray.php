<?php

class App_Form_Element_Validate_NotInArray extends Zend_Validate_Abstract
{

    const NOT_IN_ARRAY = 'Not in array';

    protected $_array = array();

    public function __construct(array $array)
    {
        $this->_array = $array;
    }

    public function isValid($value)
    {
        if ( ! in_array($value, $this->_array)) {
            $result = TRUE;
        }
        else {
            $this->_error(self::NOT_IN_ARRAY);
            $result = FALSE;
        }
        return $result;
    }

}
