<?php

class Form_AdminLanguageEdit extends App_Form
{
    
    public function init()
    {

        $this->setMethod('POST');

        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );
        
        
        $this->addElement('text', 'title', array(
            'label' => $this->getTranslator()->_('Название'),
            'attribs' => array(
                'maxlength' => 255,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1,255)),
            ),
            'required' => TRUE
        ));

        
        $this->addElement('checkbox', 'is_default', array(
            'label' => $this->getTranslator()->_('По-умолчанию'),
        ));
                
        
        $this->addElement('text', 'code2', array(
            'label' => $this->getTranslator()->_('2-символьный код'),
            'attribs' => array(
                'maxlength' => 2,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(2,2)),
            ),
            'required' => TRUE
        ));
        
        $this->addElement('text', 'code3', array(
            'label' => $this->getTranslator()->_('3-символьный код'),
            'attribs' => array(
                'maxlength' => 3,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3,3)),
            ),
            'required' => TRUE
        ));

        
        
        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));
        
    }

    protected function _prepareStatuses()
    {
        $list = array();
        foreach (Model_Service::factory('language')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }
    
    
}