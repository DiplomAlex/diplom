<?php

class Shop_Form_ItemFilter extends App_Form
{

    public function init()
    {

        $this->setMethod('GET');

        $this->addElement('text', 'filter_desc_name', array(
            'label' => '123123',
            'attribs' => array(
                'maxlength' => 200,
                'class' => 'keywords',
            ),
            'validators' => array(
                array('StringLength', false, array(3,200))
            ),
            'filters' => array('StringTrim'),
        ));
    }
}