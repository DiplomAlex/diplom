<?php

class Catalog_Form_AdminItem_Attribute_VariantEdit extends Catalog_Form_AdminItem_Abstract
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


        $this->addElement('text', 'param1', array(
            'label' => $this->getTranslator()->_('Parameter 1'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90,
                'style' => 'width: 200px',
            ),
            'validators' => array(
                array('StringLength', FALSE, array(1,1000))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
        $this->addElement('text', 'param2', array(
            'label' => $this->getTranslator()->_('Parameter 2'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90,
                'style' => 'width: 200px',
            ),
            'validators' => array(
                array('StringLength', FALSE, array(1,1000))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
        $this->addElement('text', 'param3', array(
            'label' => $this->getTranslator()->_('Parameter 3'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90,
                'style' => 'width: 200px',
            ),
            'validators' => array(
                array('StringLength', FALSE, array(1,1000))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));

    }

}

