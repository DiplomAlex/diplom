<?php

class Form_AdminBannerEdit extends App_Form
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

        $selectStatus = new Zend_Form_Element_Select('place');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Place'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_preparePlaces())
                    );



        $this->addElement('resource', 'resource_image_id', array(
            'label' => $this->getTranslator()->_('Изображение'),
            'required' => FALSE,
            'validators' => array(
				array('Extension', false, Zend_Registry::get('config')->imageValidExtension->toArray())
			)
        ));

        $this->addElement('text', 'link', array(
            'label' => $this->getTranslator()->_('Ссылка'),
            'attribs' => array(
                'maxlength' => 1000,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1,1000))
            ),
            'filters' => array('StringTrim'),
            /*'required' => TRUE*/
        ));




        foreach (Model_Service::factory('language')->getAllActive() as $lang) {

            $this->addElement('text', 'description_language_'.$lang->id.'_name', array(
                'label' => $this->getTranslator()->_('Name').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 200,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', false, array(1,500))
                ),
                'filters' => array('StringTrim'),
                'required' => TRUE
            ));

            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_html', array(
                'label' => $this->getTranslator()->_('Html').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'width' => '80%',
                    'height_koef' => 2,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,10000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('textarea', 'description_language_'.$lang->id.'_text', array(
                'label' => $this->getTranslator()->_('Text').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,10000))
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
        foreach (Model_Service::factory('banner')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }

    protected function _preparePlaces()
    {
        $list = array();
        foreach (Model_Service::factory('banner')->getPlacesList() as $place=>$size) {
            $list[$place] = $this->getTranslator()->_($place).' /'.$size;
        }
        return $list;
    }
}
