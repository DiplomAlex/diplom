<?php

class Form_AdminWhiteIpEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');

        $this->addElement('text', 'ip', array(
            'label' => $this->getTranslator()->_('IP-адрес'),
            'attribs' => array(
                'maxlength' => 20,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1,20))
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));

        $this->addElement('text', 'provider', array(
            'label' => $this->getTranslator()->_('Имя сети'),
            'attribs' => array(
                'maxlength' => 500,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1,500))
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));


        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));

    }

}
