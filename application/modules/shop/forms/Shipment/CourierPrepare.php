<?php

class Shop_Form_Shipment_CourierPrepare extends App_Form
{


    public function init()
    {
        
        $this->addElement('text', 'client_requisites__name', array(
            'label' => 'Имя получателя',
            'required' => TRUE,
            'attribs' => array(
                'class' => 'recipient_name',
            ),
        ));

        $this->addElement('text', 'client_requisites__phone', array(
            'label' => 'Телефон',
        	'required' => TRUE,
            'attribs' => array(
                'class' => 'phone',
            ),
        ));        
        
        $this->addElement('text', 'client_requisites__email', array(
            'label' => 'E-mail',
        	'required' => TRUE,
            'attribs' => array(
                'class' => 'email',
            ),
        ));        
        
        
        $this->addElement('text', 'client_requisites__address', array(
            'label' => 'Адрес доставки',
        	'required' => TRUE,
            'attribs' => array(
                'class' => 'delivery_address',
            ),
        ));  

        $this->addElement('textarea', 'params__comment', array(
            'label' => 'Комментарий',
            'attribs' => array(
            	'cols' => '30',
                'rows' => '3',
                'class' => 'comment',
            ),
            'required' => FALSE,
        ));
    }

}