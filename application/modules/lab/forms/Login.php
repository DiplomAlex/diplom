<?php

class Lab_Form_Login extends App_Form
{

    public function init()
    {
		$this->setName('form_login');
        $this->setMethod('POST');

		$this->addElement('text', 'login', array(
            'label' => 'Email',
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
        	'label' => '�����',
        ));
        

    }

}