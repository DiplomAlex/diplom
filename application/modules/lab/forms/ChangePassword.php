<?php

class Lab_Form_ChangePassword extends App_Form
{

    public function init()
    {
		$this->setName('ChangePassword');
		$this->setMethod('POST');

       	$this->addElement('password', 'password_current', array(
            'label' => 'Текущий пароль',
            'attribs' => array(
                'maxlength' => 50,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', FALSE, array(3,20)),
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));
		
        $this->addElement('password', 'password_new', array(
            'label' => 'Новый пароль',
            'attribs' => array(
                'maxlength' => 50,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', FALSE, array(3,20)),
                array('PasswordRepeat', FALSE, array('password_confirmation')),
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));

        $this->addElement('password', 'password_confirmation', array(
            'label' => 'Подтверждение',
            'attribs' => array(
                'maxlength' => 50,
                'size' => 90
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));
        
        $this->addElement('submit', 'send', array(
            'label' => 'Изменить',
            'attribs' => array(
                    'width' => '200px',
            ),
        ));        
                
    }
    
}
