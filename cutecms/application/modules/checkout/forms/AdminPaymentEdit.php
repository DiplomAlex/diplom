<?php

class Checkout_Form_AdminPaymentEdit extends App_Form
{

    public function init()
    {
        $langs = Model_Service::factory('language')->getAllActive();
        foreach ($langs as $lang) {
            $this->addElement('text', 'title'.'__'.$lang->code2, array(
                'label' => $this->getTranslator()->_('Название').' ('.strtoupper($lang->code2).')',
            ));
        }
        foreach ($langs as $lang) {
            $this->addElement('textarea', 'description'.'__'.$lang->code2, array(
                'label' => $this->getTranslator()->_('Описание').' ('.strtoupper($lang->code2).')',
                'cols' => '40',
                'rows' => '3',
            ));
        }
        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));
    }

}