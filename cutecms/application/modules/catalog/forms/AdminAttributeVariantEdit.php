<?php

class Catalog_Form_AdminAttributeVariantEdit extends App_Form
{

    public function init()
    {
        $this->addElement('text', 'text', array(
            'label' => $this->getTranslator()->_('Text'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 30,
                'style' => 'width: 200px',
            ),
            'validators' => array(
                array('StringLength', FALSE, array(1,200))
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));

        $this->addElement('text', 'value', array(
            'label' => $this->getTranslator()->_('Value'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 30,
                'style' => 'width: 200px',
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));

    }

}

