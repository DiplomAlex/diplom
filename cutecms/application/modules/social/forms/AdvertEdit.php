<?php

class Social_Form_AdvertEdit extends App_Form
{

    public function init()
    {
        $this->setMethod('POST');


        $select = new Zend_Form_Element_Select('status');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('Status'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );


        $select = new Zend_Form_Element_Select('category_id');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('Категория'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareCategories())
                    );

        /*
        $this->addElement('text', 'automate', array(
            'label' => $this->getTranslator()->_('Модель автомата'),
            'attribs' => array(
                'class' => 'text',
            ),
            'validators' => array(
                array('StringLength', false, array(1,1000))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
        */

        $select = new Zend_Form_Element_Select('automate');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('Модель автомата'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareAutomates())
                    );


        $this->addElement('text', 'qty', array(
            'label' => $this->getTranslator()->_('Кол-во'),
            'attribs' => array(
                'class' => 'text',
            ),
            'validators' => array(
                array('StringLength', false, array(1,10))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));

        $this->addElement('text', 'price', array(
            'label' => $this->getTranslator()->_('Цена'),
            'attribs' => array(
                'class' => 'text',
            ),
            'validators' => array(
                array('StringLength', false, array(1,10))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));



        $this->addElement('textarea', 'text', array(
            'label' => $this->getTranslator()->_('Текст объявления'),
            'attribs' => array(
                'maxlength' => 1000,
                'cols' => 50,
                'rows' => 4,
                'class' => 'textarea',
            ),
            'validators' => array(
                array('StringLength', false, array(3,1000))
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));

        $this->addElement('submitLink', 'send', array(
            'label' => $this->getTranslator()->_('Send'),
            'attribs' => array(
                'noAjaxSubmit' => TRUE,
            ),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
            'attribs' => array(
                'noAjaxSubmit' => TRUE,
            ),
        ));

    }



    protected function _prepareStatuses()
    {
        $list = array();
        foreach (Model_Service::factory('social/advert')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }

    protected function _prepareCategories()
    {
        $list = array();
        foreach (Model_Service::factory('social/advert-category')->getAll() as $cat) {
            $list[$cat->id] = $cat->name;
        }
        return $list;
    }

    protected function _prepareAutomates()
    {
        return Model_Service::factory('social/advert')->getAutomateModelsList();
    }

}
