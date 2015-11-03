<?php

class Form_AdminSiteEdit extends App_Form
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
        

        $this->addElement('text', 'host', array(
            'label' => $this->getTranslator()->_('Имя хоста'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(4,200)),
            ),
            'required' => TRUE
        ));


        $this->addElement('text', 'base_url', array(
            'label' => $this->getTranslator()->_('Базовый каталог'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'required' => FALSE
        ));

        $select = new Zend_Form_Element_Select('vertical_id');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('Скин'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareVerticals())
                    );

        $select = new Zend_Form_Element_Select('default_language_id');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('Язык по-умолчанию'))
                        -> setRequired(FALSE)
                        -> addMultiOptions($this->_prepareLanguages())
                    );
                    
                    
        $langs = $this->_getLanguageService()->getAllActive();
        foreach ($langs as $lang) {

            $this->addElement('text', 'description_language_'.$lang->id.'_title', array(
                'label' => $this->getTranslator()->_('Название').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 200,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', false, array(3,200))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE,
            ));

        }
        
        $this->addElement('checkbox', 'is_linked_by_default', array(
            'label' => $this->getTranslator()->_('Привязан по-умолчанию'),
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
        foreach (Model_Service::factory('site')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }
    
    protected function _prepareVerticals()
    {
        $list = array();
        $vs = Model_Service::factory('vertical')->getAll();
        foreach ($vs as $v) {
            $list[$v->id] = $v->skin;
        }
        return $list;
    }
    
    protected function _prepareLanguages()
    {
        $service = $this->_getLanguageService();
        $list = array('' => ' -- '.$this->getTranslator()->_('None').' -- ');
        $langs = $service->getAll();
        foreach ($langs as $lang) {
            $list[$lang->id] = $lang->title;
        }
        return $list;
    } 
    
    protected function _getLanguageService()
    {
        $service = Model_Service::factory('language');
        return $service;
    }
    
    public function populate(array $values)
    {
        if ((( ! array_key_exists('id', $values)) OR ( ! $values['id'])) AND ( ! $values['default_language_id'])) {
            $values['default_language_id'] = $this->_getLanguageService()->getDefault()->id;
        }
        return parent::populate($values);
    }

}
