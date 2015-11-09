<?php
class Labs_ArduinoController extends App_Event_Observer
{
    public function init()
    {
        App_Event::factory('Controller__init', array($this, $this->_getParam('layoutName'), 'layout'))->dispatch();
    }

    public function lab1Action()
    {

    }
}