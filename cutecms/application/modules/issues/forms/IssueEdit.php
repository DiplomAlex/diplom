<?php

class Issues_Form_IssueEdit extends App_Form
{

    public function init()
    {

        $this->addElement('text', 'subject', array(
            'label' => $this->getTranslator()->_('Тема'),
            'required' => TRUE,
        ));


        /*
        $this->addElement('textarea', 'text', array(
            'label' => $this->getTranslator()->_('Текст'),
            'required' => TRUE,
        ));
        */


        $this->addElement('fckeditor', 'text', array(
            'label' => $this->getTranslator()->_('Текст'),
            'attribs' => array(
                'maxlength' => 20000,
                'width' => 550,
                'height_koef' => 2,
            ),
            'validators' => array(
                array('StringLength', false, array(1,20000))
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));



        $select = new Zend_Form_Element_Select('status');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('Состояние'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );


        $this->addElement('text', 'date_added', array(
            'label' => $this->getTranslator()->_('Создано'),
            'attribs' => array(
                'readonly' => TRUE,
            ),
            'required' => FALSE,
        ));

        $this->addElement('text', 'date_due', array(
            'label' => $this->getTranslator()->_('Дедлайн'),
            'attribs' => array(
                'readonly' => TRUE,
            ),
            'required' => TRUE,
        ));

        $this->addElement('multiCheckbox', 'users', array(
            'label' => $this->getTranslator()->_('Исполнители'),
            'required' => TRUE,
        ));
        $this->users->setMultiOptions($this->_prepareUsers());




        $this->addElement('textarea', 'changer_comment', array(
            'label' => $this->getTranslator()->_('Комментарий к изменениям'),
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
        return Model_Service::factory('issues/issue')->getStatusList();
    }

    protected function _prepareUsers()
    {
        return Model_Service::factory('user')->getListOfCoworkers();
    }

}