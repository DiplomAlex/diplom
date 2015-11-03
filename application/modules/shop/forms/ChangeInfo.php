<?php

class Shop_Form_ChangeInfo extends App_Form
{

    public function init()
    {
		$this->setName('ChangeInfo');
		$this->setMethod('POST');

        $this->addElement('text', 'firstname', array(
            'label' => 'Имя',
            'attribs' => array(
                'maxlength' => 50,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', FALSE, array(3,50)),
                array('Alnum'),
            ),
            'required' => TRUE
        ));
		
		$this->addElement('text', 'fathersname', array(
            'label' => 'Отчество',
            'attribs' => array(
                'maxlength' => 50,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', FALSE, array(3,50)),
            ),
            'required' => FALSE
        ));
		
		$this->addElement('text', 'lastname', array(
            'label' => 'Фамилия',
            'attribs' => array(
                'maxlength' => 50,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', FALSE, array(3,50)),
                array('Alnum'),
            ),
            'required' => TRUE
        ));
		
		$this->addElement('text', 'email_address', array(
            'label' => 'Ваш Email',
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1,200)),
                array('EmailAddress'),
            ),
            'required' => TRUE
        ));
		
		$this->addElement('text', 'city', array(
            'label' => 'Город',
            'attribs' => array(
                'maxlength' => 50,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', FALSE, array(3,50)),
            ),
            'required' => TRUE
        ));

		$this->addElement('text', 'telephone', array(
            'label' => 'Телефон',
            'attribs' => array(
                'maxlength' => 200,
                'size' => 45,
            ),
            'validators' => array(
                array('StringLength', false, array(1,200)),
				array('Digits'),
            ),
            'required' => TRUE
        ));
        
        $this->addElement('submit', 'send', array(
            'label' => 'Сохранить',
            'attribs' => array(
                    'width' => '200px',
            ),
        ));        
         
    }
    
}
