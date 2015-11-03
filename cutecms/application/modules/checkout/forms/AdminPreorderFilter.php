<?php

class Checkout_Form_AdminPreorderFilter extends App_Form
{

    public function init()
    {

        $this->setMethod('GET');

        $this->addElement('text', 'filter_number', array(
            'label' => $this->getTranslator()->_('Номер'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 20,
                'class' => 'input',
            ),
            'validators' => array(
                array('StringLength', false, array(3,200))
            ),
            'filters' => array('StringTrim'),
        ));


        $this->addElement('text', 'filter_client', array(
            'label' => $this->getTranslator()->_('Client'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 20,
                'class' => 'input',
            ),
            'validators' => array(
                array('StringLength', false, array(3,200))
            ),
            'filters' => array('StringTrim'),
        ));

        $this->addElement('submitLink', 'filter', array(
            'label' => $this->getTranslator()->_('Filter'),
        ));


        $urlParams = array();

        foreach ($this->getElements() as $el) {
            $el->setDecorators(array('ViewHelper'));
            $urlParams[$el->getName()] = NULL;
        }

        $this->setAction($this->getView()->url($urlParams));
    }


}