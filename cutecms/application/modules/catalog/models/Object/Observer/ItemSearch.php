<?php

class Catalog_Model_Object_Observer_ItemSearch extends App_Event_Observer
{

    public function onAfterSave()
    {
        Model_Service::factory('catalog/item-search')->addColumn($this->getData(0)->id);
    }

    public function onAfterDelete()
    {
        Model_Service::factory('catalog/item-search')->deleteColumn($this->getData(0)->id);
    }

    public function afterDeleteCollection()
    {
        $collecton = $this->getData(0);
        $service   = Model_Service::factory('catalog/item-search');

        if ($collecton instanceof Catalog_Model_Collection_Attribute) {
            foreach ($collecton as $value) {
                $service->deleteColumn($value->id);
            }
        }
    }

}