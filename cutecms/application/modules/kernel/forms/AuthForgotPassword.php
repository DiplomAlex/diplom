<?php

class Form_AuthForgotPassword extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');

        $this->addElement('text', 'email', array(
            'label' => $this->getTranslator()->_('Email'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 30
            ),
            'validators' => array(
                array('StringLength', FALSE, array(2,200)),
                array('EmailAddress'),
                array('InArray', FALSE, array($this->_prepareEmailsArray()))
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));


        $captcha = new App_Form_Element_Captcha(
                'captcha',
                array(
                    'label' => $this->getTranslator()->_('Captcha'),
                    'attribs' => array(
                        'size' => '8',
                        'maxlen' => '8',
                		'style' => 'margin-bottom: 11px;',
                    ),
                    'required' => TRUE,
                )
            );

        $this->addElement($captcha);



        $this->addElement('submit', 'send', array(
            'label' => $this->getTranslator()->_('Send'),
        ));


    }


    protected function _prepareEmailsArray()
    {
        return Model_Service::factory('user')->getAllEmails();
    }

}
