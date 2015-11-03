<?php

class Checkout_Model_Service_Observer_Brule_Shipment_Transport extends App_Event_Observer
{

    public function onGetCitiesList()
    {
        $result = App_Event::factory('Tickets_Model_Service_TransportPrice__getCitiesList')
                           ->dispatch()
                           ->getResponse();                           
        $this->getEvent()->setResponse($result);
    }

    public function onGetCompaniesList()
    {
        $city = $this->getData(0);
        $result = App_Event::factory('Tickets_Model_Service_TransportPrice__getCompaniesList', array($city))
                           ->dispatch()
                           ->getResponse();
        $this->getEvent()->setResponse($result);
    }


}