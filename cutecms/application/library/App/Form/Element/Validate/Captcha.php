<?php

class App_Form_Element_Validate_Captcha extends Zend_Validate_Abstract {

    const EMPTY_CAPTCHA = 'isEmpty';
    const WRONG_CAPTCHA = 'isWrong';

    /**
     *
     * @param $value string
     * @param $context array All other elements from the form
     * @return boolean Returns true if the element is valid
     */
    public function isValid($value, $context = NULL) {

        if (empty($value['input'])) {
            $this->_error(self::EMPTY_CAPTCHA);
            $result = FALSE;
        }
        else {

            $result = App_Captcha::factory()->isValid($value, $context);
            if ($result === FALSE) {
                $this->_error(self::WRONG_CAPTCHA);
            }
        }

        return $result;

    }
}

