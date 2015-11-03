<?php

class Issues_Form_AdminIssueTopicEdit extends App_Form
{

    public function init()
    {
        $this->addElement('text', 'text', array(
            'label' => $this->getTranslator()->_('Текст'),
            'attribs' => array(
                'maxlength' => 2000,
                'size' => 90,
                'style' => 'width: 200px',
            ),
            'validators' => array(
                array('StringLength', FALSE, array(1,2000))
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE,
        ));
    }

}