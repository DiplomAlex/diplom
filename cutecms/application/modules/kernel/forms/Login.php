<?php

class Form_Login extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');

        $this->addElement('hidden', 'redirect_url');

        $this->addElement('text', 'login', array(
            'label' => $this->getTranslator()->_('Login'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 30
            ),
            'validators' => array(
                array('StringLength', false, array(3,200)),
            ),
            'required' => TRUE
        ));


        $this->addElement('password', 'password', array(
            'label' => $this->getTranslator()->_('Password'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 30
            ),
            'validators' => array(
                array('StringLength', false, array(3,200)),
            ),
            'required' => TRUE
        ));


        $this->addElement('submit', 'send', array(
            'label' => $this->getTranslator()->_('Log In'),
            'attribs' => array(
                    'width' => '200px',
            ),
        ));


    }

}