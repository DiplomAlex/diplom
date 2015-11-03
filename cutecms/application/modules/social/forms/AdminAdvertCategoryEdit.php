<?php

class Social_Form_AdminAdvertCategoryEdit extends App_Form
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

        foreach (Model_Service::factory('language')->getAllActive() as $lang) {

            $this->addElement('text', 'description_language_'.$lang->id.'_name', array(
                'label' => $this->getTranslator()->_('Title').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 200,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', false, array(3,200))
                ),
                'filters' => array('StringTrim'),
                'required' => TRUE
            ));

            $this->addElement('textarea', 'description_language_'.$lang->id.'_brief', array(
                'label' => $this->getTranslator()->_('Brief').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(3,1000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_full', array(
                'label' => $this->getTranslator()->_('Full').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 6000,
                    'width' => 550,
                    'height_koef' => 2,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,2000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

        }


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
        foreach (Model_Service::factory('social/advert-category')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }


}
