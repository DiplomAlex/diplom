<?php

class Form_AdminArticleEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');


        $this->addElement('hidden', 'id', array('required' => FALSE));


        $langs = Model_Service::factory('language')->getAllActive();                    
                    
        $seoIdFromFields = array();
        foreach ($langs as $lang) {
            $seoIdFromFields []= 'description_language_'.$lang->id.'_title';
        }
        $this->addElement('text', 'seo_id', array(
            'label' => $this->getTranslator()->_('Seo Id'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,200)),
                array(new Form_Element_Validate_IsUniqKey(Model_Service::factory('article'), 'seo_id', array($seoIdFromFields), TRUE, 'id'))
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
                    

        $types = $this  ->createElement('select', 'topics')
                          ->setLabel($this->getTranslator()->_('Topic'))
                          ->setRequired(TRUE)
                          ->setMultiOptions($this->_prepareTopics())
                          ;
        $this->addElement($types);
                    


        foreach ($langs as $lang) {

            $this->addElement('text', 'description_language_'.$lang->id.'_author', array(
                'label' => $this->getTranslator()->_('Автор').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 500,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', FALSE, array(1,500))
                ),
                'filters' => array('StringTrim'),
                'required' => TRUE
            ));            
            
            $this->addElement('text', 'description_language_'.$lang->id.'_title', array(
                'label' => $this->getTranslator()->_('Title').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 500,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', FALSE, array(1,500))
                ),
                'filters' => array('StringTrim'),
                'required' => TRUE
            ));

            $this->addElement('textarea', 'description_language_'.$lang->id.'_brief', array(
                'label' => $this->getTranslator()->_('Brief').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 10000,
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,10000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            
            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_text', array(
                'label' => $this->getTranslator()->_('Text').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 50000,
                    'width' => 750,
                    'height' => 500,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,50000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));


            
            $this->addElement('text', 'description_language_'.$lang->id.'_html_title', array(
                'label' => $this->getTranslator()->_('Html title').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', false, array(1,1000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('textarea', 'description_language_'.$lang->id.'_meta_keywords', array(
                'label' => $this->getTranslator()->_('Meta-keywords').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 3000,
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,3000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('textarea', 'description_language_'.$lang->id.'_meta_description', array(
                'label' => $this->getTranslator()->_('Meta-description').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 5000,
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,5000))
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
        $all = Model_Service::factory('article')->getStatusesList();
        foreach ($all as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }


    protected function _prepareTopics()
    {
        $list = Model_Service::factory('article-topic')->getFullTreeAsSelectOptions(NULL, TRUE);
        return $list;
    }

}
