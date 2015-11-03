<?php

class Form_AdminPageEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');

        
        $this->addElement('hidden', 'id');

        $this->addElement('text', 'code', array(
            'label' => $this->getTranslator()->_('Внутренний код страницы'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1,200)),
            ),
            'required' => FALSE
        ));
        
        


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
                array(new Form_Element_Validate_IsUniqKey(Model_Service::factory('page'), 'seo_id', array($seoIdFromFields), TRUE, 'id'))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
           

        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                       // -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );


        $dSelect = new Zend_Form_Element_Select('driver');
        $this->addElement( $dSelect
                        -> setLabel($this->getTranslator()->_('Driver'))
                        -> setAttrib('width','50')
                        -> setRequired(FALSE)
                        -> addMultiOptions($this->_prepareDrivers())
                    );
                    
        $this->addElement('resource', 'resource_rc_id', array(
            'label' => $this->getTranslator()->_('Image'),
            'validators' => array(
				array('Extension', false, Zend_Registry::get('config')->imageValidExtension->toArray())
			)
        ));


        foreach ($langs as $lang) {
            $this->addElement('textarea', 'description_language_'.$lang->id.'_video', array(
                'label' => $this->getTranslator()->_('Video from YouTube'),
                'attribs' => array(
                    'cols' => 90,
                    'rows' => 4,
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_banner', array(
                'label' => $this->getTranslator()->_('Banner'),
                'attribs' => array(
                    'width' => '80%',
                    'height_koef' => 2,
                ),
                'required' => FALSE
            ));

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

            $this->addElement('textarea', 'description_language_'.$lang->id.'_brief', array(
                'label' => $this->getTranslator()->_('Brief').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'cols' => 90,
                    'rows' => 4,
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_full', array(
                'label' => $this->getTranslator()->_('Full').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'width' => '80%',
                    'height_koef' => 2,
                ),
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


/*
        $this->addElement('checkbox', 'flag1', array(
            'label' => $this->getTranslator()->_('В меню "Доставка"'),
        ));


        $this->addElement('checkbox', 'flag2', array(
            'label' => $this->getTranslator()->_('В меню "Для клиентов"'),
        ));
*/
        
        
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
