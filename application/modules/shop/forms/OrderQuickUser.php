<?php

class Shop_Form_OrderQuickUser extends App_Form
{

    public function init()
    {
        $this->setName('OrderQuickUser');
        $this->setMethod('POST');

        $this->addElement('text', 'name', array(
            'label' => 'Ваше Имя',
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', FALSE, array(3, 200)),
                //array('Alnum'),
            ),
            'filters' => array(
                array('StringTrim'),
            ),
            'required' => TRUE
        ));

        $this->addElement('text', 'email', array(
            'label' => 'Эл. Почта',
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1, 200)),
                array('EmailAddress'),
                //array('NotInArray', FALSE, array(Model_Service::factory('user')->getAllEmails()))
            ),
            'filters' => array(
                array('StringTrim'),
            ),
            'required' => TRUE
        ));

	$this->addElement('text', 'city', array(
            'label' => 'Город',
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', FALSE, array(3,200)),
            ),
            'filters' => array(
                array('StringTrim'),
            ),
            'required' => FALSE
        ));

        $this->addElement('text', 'telephone', array(
            'label' => 'Телефон',
            'attribs' => array(
                'maxlength' => 200,
                'size' => 45,
            ),
            'validators' => array(
                array('StringLength', false, array(1,200)),
                //array('Digits'),
            ),
            'filters' => array(
                array('StringTrim'),
            ),
            'required' => FALSE
        ));

        $this->addElement('textarea', 'message', array(
            'label' => 'Сообщение',
            'attribs' => array(
            	'cols' => '30',
                'rows' => '3',
            ),
            'validators' => array(
                array('StringLength', false, array(1,1000)),
            ),
            'filters' => array(
                array('StringTrim'),
            ),
            'required' => FALSE,
        ));

        $this->addElement('submit', 'send', array(
            'label' => $this->getTranslator()->_('Register'),
        ));
    }

}