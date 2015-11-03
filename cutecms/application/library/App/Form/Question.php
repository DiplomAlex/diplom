<?php

class App_Form_Question extends App_Form
{

    public function init() {

        $this->addElement('submitLink', 'yes',
            array(
                'label' => $this->getTranslator()->_('Yes'),
            )
        );

        $this->addElement('submitLink', 'no',
            array(
                'label' => $this->getTranslator()->_('No'),
            )
        );

    }


}