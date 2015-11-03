<?php

class Form_AdminCurrencyEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');

        $this->addElement('text', 'rub_eur', array(
            'label' => $this->getTranslator()->_('Стоимость 1р. в евро'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,200)),
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));
        
        $this->addElement('text', 'rub_usd', array(
            'label' => $this->getTranslator()->_('Стоимость 1р. в долларах США'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,200)),
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
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
