<?php

class Issues_Form_IssueChangeDateDue extends App_Form
{

    public function init()
    {
        $this->addElement('text', 'date_due', array(
            'label' => $this->getTranslator()->_('Дата завершения'),
        ));

        $this->addElement('textarea', 'comment', array(
            'label' => $this->getTranslator()->_('Комментарий'),
        ));

    }
}
