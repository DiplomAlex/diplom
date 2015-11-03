<?php

class Checkout_Form_Shipment_TransportAdminEdit extends App_Form
{

    public function init()
    {

        $this->addElement('text', 'seller_requisites__name', array(
            'label' => $this->getTranslator()->_('Поставщик'),
        ));

        $this->addElement('text', 'seller_requisites__address', array(
            'label' => $this->getTranslator()->_('Адрес'),
        ));

        $this->addElement('text', 'seller_requisites__inn', array(
            'label' => $this->getTranslator()->_('ИНН'),
        ));

        $this->addElement('text', 'seller_requisites__kpp', array(
            'label' => $this->getTranslator()->_('КПП'),
        ));

        $this->addElement('text', 'seller_requisites__phone', array(
            'label' => $this->getTranslator()->_('Тел.'),
        ));

    }

}