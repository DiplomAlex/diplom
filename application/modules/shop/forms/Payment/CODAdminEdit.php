<?php

class Shop_Form_Payment_CODAdminEdit extends App_Form
{

    public function init()
    {

        $this->addElement('text', 'seller_requisites__name', array(
            'label' => $this->getTranslator()->_('Получатель'),
        ));

        /*$this->addElement('text', 'seller_requisites__bank', array(
            'label' => $this->getTranslator()->_('Банк получателя'),
        ));

        $this->addElement('text', 'seller_requisites__inn', array(
            'label' => $this->getTranslator()->_('ИНН'),
        ));

        $this->addElement('text', 'seller_requisites__kpp', array(
            'label' => $this->getTranslator()->_('КПП'),
        ));

        $this->addElement('text', 'seller_requisites__bik', array(
            'label' => $this->getTranslator()->_('БИК'),
        ));

        $this->addElement('text', 'seller_requisites__rs', array(
            'label' => $this->getTranslator()->_('Р/с'),
        ));

        $this->addElement('text', 'seller_requisites__ks', array(
            'label' => $this->getTranslator()->_('К/с'),
        ));*/
    }

}