<?php

class Form_AdminNewsTopicEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');



        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                       // -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );
                    

        $langs = Model_Service::factory('language')->getAllActive();                    
                    
        $seoIdFromFields = array();
        foreach ($langs as $lang) {
            $seoIdFromFields []= 'description_language_'.$lang->id.'_name';
        }
        $this->addElement('text', 'seo_id', array(
            'label' => $this->getTranslator()->_('Seo Id'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,200)),
                array(new Form_Element_Validate_IsUniqKey(Model_Service::factory('news-topic'), 'seo_id', array($seoIdFromFields), TRUE, 'id'))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
                               

        foreach ($langs as $lang) {

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
                    'width' => '80%',
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
            'class' => 'btn-primary',
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));

    }



    protected function _prepareStatuses()
    {
        $list = array();
        foreach (Model_Service::factory('news-topic')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }

}
