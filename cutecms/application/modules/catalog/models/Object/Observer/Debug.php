<?php

class Catalog_Model_Object_Observer_Debug extends App_Event_Observer
{

    public function onBeforeSaveAttribute()
    {
        $object = $this->getData(0);
        $values = $this->getData(1);
    }

}