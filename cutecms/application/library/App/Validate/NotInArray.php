<?php

class App_Validate_NotInArray extends Zend_Validate_Abstract
{

    const IS_IN_ARRAY = 'is in array';

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
            $this->_error(self::IS_IN_ARRAY);
            $result = FALSE;
        }
        return $result;
    }

}
