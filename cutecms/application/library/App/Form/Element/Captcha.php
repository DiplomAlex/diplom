<?php

class App_Form_Element_Captcha extends Zend_Form_Element_Text {
    
    public $helper = 'formCaptcha';

    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);
        $this->addValidator('captcha');
    }

}

