<?php

class App_Form_Element_Validate_EmailAddress extends Zend_Validate_EmailAddress
{

    public function isValid($value)
    {
        $result = parent::isValid($value);
        $this->_errors = array();
        $this->_messages = array();
        if ( ! $result) {
            $this->_error(parent::INVALID);
        }
        return $result;
    }

}