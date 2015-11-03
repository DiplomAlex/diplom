<?php

class Checkout_Form_Shipment_TransportPrepare extends App_Form
{
    
    const SHIPMENT_TO_TR_COMPANY = 'tr_company';
    const SHIPMENT_TO_CLIENT = 'client';

    public function init()
    {
        $this->addElement('select', 'params__tr_city', array(
            'label' => $this->getTranslator()->_('Город'),
            'required' => TRUE,
        ));
        $this->params__tr_city->addMultiOptions($this->_prepareTrCity());

        $this->addElement('select', 'params__tr_company_id', array(
            'label' => $this->getTranslator()->_('Название транспортной компании'),
            'required' => TRUE,
        ));
        $this->params__tr_company_id->setRegisterInArrayValidator(FALSE);
        
        $this->addElement('select', 'params__ship_to', array(
            'label' => $this->getTranslator()->_('Доставка'),
        ));
        $this->params__ship_to->addMultiOptions($this->_prepareShipTo());
        
        $this->addElement('textarea', 'params__ship_address', array(
            'label' => $this->getTranslator()->_('Адрес доставки'),
            'attribs' => array(
            	'cols' => '40',
                'rows' => '2',
            ),
        ));
    }

    protected function _prepareShipTo()
    {
        $list = array(
            self::SHIPMENT_TO_TR_COMPANY => $this->getTranslator()->_('shipment.transport.ship-to.tr_company'),
            self::SHIPMENT_TO_CLIENT     => $this->getTranslator()->_('shipment.transport.ship-to.client'),
        );
        return $list;
    }


    protected function _prepareTrCity()
    {
        $list = App_Event::factory('Checkout_Model_Service_Helper_Brule_Shipment_Transport__getCitiesList')->dispatch()->getResponse();
        return $list;
    }


}