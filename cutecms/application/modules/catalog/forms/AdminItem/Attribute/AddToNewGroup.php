<?php

class Catalog_Form_AdminItem_Attribute_AddToNewGroup extends Catalog_Form_AdminItem_Abstract
{

    public function init()
    {
        $this->addElement('select', 'parent_id', array(
            'label' => $this->getTranslator()->_('Родительская папка'),
            'required' => FALSE,
            'attribs' => array(
                'style' => 'width: 150px'
            ),
        ));
        $this->parent_id->setMultiOptions($this->_prepareOptions());

        $langs = Model_Service::factory('language')->getAllActive();
        foreach ($langs as $lang) {
            $this->addElement('text', 'description_language_'.$lang->id.'_name', array(
                'label' => $this->getTranslator()->_('Имя набора').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 200,
                    'style' => 'width: 150px',
                ),
                'validators' => array(
                    array('StringLength', false, array(3,200))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));
        }

        $this->addElement('hidden', 'status');
        $this->status->setValue(1);

    }


    protected function _prepareOptions()
    {
        $list = Model_Service::factory('catalog/attribute-group')->getFullTreeAsSelectOptions();
        return $list;
    }

    public function getGroupsElement()
    {
        return $this->parent_id;
    }

}