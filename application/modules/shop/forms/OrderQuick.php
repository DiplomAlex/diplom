<?php

class Shop_Form_OrderQuick extends App_Form
{

    public function init()
    {
        $this->setName('OrderQuick');
        $this->setMethod('POST');

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
    }

}