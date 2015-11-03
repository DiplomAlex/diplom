<?php

class Social_Form_AdminAdvertAutomates extends App_Form
{

    public function init()
    {

        $this->addElement('textarea', 'list', array(
            'label' => $this->getTranslator()->_('Список моделей'),
            'attribs' => array(
                'cols' => 25,
                'rows' => 20,
            ),
        ));

        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));


    }

}