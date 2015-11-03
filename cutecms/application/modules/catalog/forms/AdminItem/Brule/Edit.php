<?php

class Catalog_Form_AdminItem_Brule_Edit extends Catalog_Form_AdminItem_Abstract
{

    public function init()
    {

        $selectStatus = new Zend_Form_Element_Select('code');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Business rule'))
                        -> setRequired(TRUE)
                        -> setAttrib('style', 'width: 200px')
                        -> addMultiOptions($this->_prepareBrules())
                    );


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


    protected function _prepareBrules()
    {
        return Model_Service::factory('catalog/brule')->getAllAvailableForItem();
    }

}