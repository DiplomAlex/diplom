<?php

class Form_AdminTipEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');


        $select = new Zend_Form_Element_Select('role');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('Role'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareRoles())
                    );

        $select = new Zend_Form_Element_Select('destination');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('Раздел сайта'))
                        -> setRequired(TRUE)
                        -> setMultiOptions(array('' => ' << '.$this->getTranslator()->_('Сначала выберите роль').' >> '))
                    );

        foreach (Model_Service::factory('language')->getAllActive() as $lang) {

            $this->addElement('text', 'description_language_'.$lang->id.'_title', array(
                'label' => $this->getTranslator()->_('Заголовок').'('.strtoupper($lang->code2).')',
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

            $this->addElement('textarea', 'description_language_'.$lang->id.'_text', array(
                'label' => $this->getTranslator()->_('Текст подсказки').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'cols' => 90,
                    'rows' => 4,
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            /*
            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_text', array(
                'label' => $this->getTranslator()->_('Текст подсказки').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'width' => 550,
                    'height_koef' => 2,
                ),
                'required' => FALSE
            ));
            */

        }


        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));

    }



    protected function _prepareRoles()
    {
        $list = array('' => ' << '.$this->getTranslator()->_('Выберите роль').' >> ') +
                Model_Service::factory('tip')->getAvailableRoles();
        return $list;
    }

    public function populate(array $values)
    {
        if ( ! empty($values['role'])) {
            $this->destination->setMultiOptions(Model_Service::factory('tip')->getAvailableDestinations($values['role']));
        }
        return parent::populate($values);
    }

}
