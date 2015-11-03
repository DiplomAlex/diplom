<?php

class Form_AdminFaqEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');



        $langs = Model_Service::factory('language')->getAllActive();                    
                    
        $seoIdFromFields = array();
        foreach ($langs as $lang) {
            $seoIdFromFields []= 'description_language_'.$lang->id.'_quest';
        }
        $this->addElement('text', 'seo_id', array(
            'label' => $this->getTranslator()->_('Seo Id'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,200)),
                array(new Form_Element_Validate_IsUniqKey(Model_Service::factory('faq'), 'seo_id', array($seoIdFromFields), TRUE, 'id'))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
           

        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );



        foreach ($langs as $lang) {

            $this->addElement('text', 'description_language_'.$lang->id.'_quest', array(
                'label' => $this->getTranslator()->_('Question').'('.strtoupper($lang->code2).')',
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
                'label' => $this->getTranslator()->_('Short answer').'('.strtoupper($lang->code2).')',
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
                'label' => $this->getTranslator()->_('Full answer').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'width' => 550,
                    'height_koef' => 2,
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));


            $this->addElement('text', 'description_language_'.$lang->id.'_html_title', array(
                'label' => $this->getTranslator()->_('Html title').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', false, array(1,10000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('textarea', 'description_language_'.$lang->id.'_meta_keywords', array(
                'label' => $this->getTranslator()->_('Meta-keywords').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,30000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('textarea', 'description_language_'.$lang->id.'_meta_description', array(
                'label' => $this->getTranslator()->_('Meta-description').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,50000))
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
        foreach (Model_Service::factory('page')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }

    protected function _prepareDrivers()
    {
        $list = array(' << '.$this->getTranslator()->_('Select driver').' >> ');
        foreach (Model_Service::factory('page')->getDriversList() as $driver) {
            $list[$driver] = $this->getTranslator()->_($driver);
        }
        return $list;
    }


}
