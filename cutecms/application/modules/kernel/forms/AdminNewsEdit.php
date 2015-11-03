<?php

class Form_AdminNewsEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');



        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                        //-> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );



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
                array(new Form_Element_Validate_IsUniqKey(Model_Service::factory('news'), 'seo_id', array($seoIdFromFields), TRUE, 'id'))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
           


        $this->addElement('text', 'date_publish', array(
            'label' => $this->getTranslator()->_('Date Publish'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 45,
                'readonly' => TRUE,
            ),
            'validators' => array(
                array('StringLength', false, array(3,200)),
            ),
            'required' => TRUE
        ));



        $this->addElement('resource', 'resource_rc_id', array(
            'label' => $this->getTranslator()->_('Image'),
            'required' => FALSE,
            'validators' => array(
				array('Extension', false, Zend_Registry::get('config')->imageValidExtension->toArray())
			)
        ));


        $select = new Zend_Form_Element_Select('ntopic_id');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('News topic'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareNewsTopics())
                    );


        foreach ($langs as $lang) {

            $this->addElement('text', 'description_language_'.$lang->id.'_title', array(
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

            $this->addElement('text', 'description_language_'.$lang->id.'_title2', array(
                'label' => $this->getTranslator()->_('Second title').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 200,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', false, array(3,200))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('textarea', 'description_language_'.$lang->id.'_announce', array(
                'label' => $this->getTranslator()->_('Announce').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(3,10000))
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



        $this->addElement('checkbox', 'send_to_subscribers', array(
            'label' => $this->getTranslator()->_('Рассылать подписчикам'),
            'required' => FALSE
        ));
        
        $this->addElement('checkbox', 'main_page', array(
            'label' => $this->getTranslator()->_('Main page new'),
            'required' => FALSE
        ));

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
        foreach (Model_Service::factory('news')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }



    protected function _prepareNewsTopics()
    {
        $list = array(''=>' << '.$this->getTranslator()->_('Select topic').' >> ');
        foreach (Model_Service::factory('news-topic')->getAll() as $topic) {
            $list[$topic->id] = $topic->name;
        }
        return $list;
    }

}
