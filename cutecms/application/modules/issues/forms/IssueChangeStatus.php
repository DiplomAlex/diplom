<?php

class Issues_Form_IssueChangeStatus extends App_Form
{

    public function init()
    {
        $this->addElement('select', 'status', array(
            'label' => $this->getTranslator()->_('Состояние'),
        ));
        $this->status->setMultiOptions($this->_prepareStatuses());

        $this->addElement('text', 'date_due', array(
            'label' => $this->getTranslator()->_('Конечный срок'),
        ));


        $this->addElement('textarea', 'comment', array(
            'label' => $this->getTranslator()->_('Комментарий'),
        ));

    }

    protected function _prepareStatuses()
    {
        return Model_Service::factory('issues/issue')->getStatusList();
    }


}
