<?php

class Checkout_Form_OrderPayment extends App_Form
{

    public function init()
    {
        $this->addElement('radio', 'method', array(
            'label' => $this->getTranslator()->_('Метод оплаты'),
        ));
    }

}