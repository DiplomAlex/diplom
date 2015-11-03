<?php

class Checkout_Form_AdminOrderItemEdit extends App_Form
{

    public function init()
    {

        $this->addElement('hidden', 'catalog_item_id', array());
        

        $this->addElement('text', 'name', array(
            'label' => $this->getTranslator()->_('Название'),
            'attribs' => array(
                'size' => 90,
                /*'readonly' => TRUE,*/
            ),
            'required' => TRUE
        ));


        $this->addElement('textarea', 'attributes_text', array(
            'label' => $this->getTranslator()->_('Атрибуты'),
            'attribs' => array(
                'cols' => 90,
                'rows' => 5,
                /*'readonly' => TRUE,*/
            ),
            'required' => FALSE
        ));

        $this->addElement('text', 'price', array(
            'label' => $this->getTranslator()->_('Цена'),
            'attribs' => array(
                'size' => 90,
                /*'readonly' => TRUE,*/
            ),
            'required' => TRUE
        ));

        $this->addElement('text', 'qty', array(
            'label' => $this->getTranslator()->_('Кол-во'),
            'attribs' => array(
                'size' => 90,
            ),
            'required' => TRUE
        ));


    }


}
