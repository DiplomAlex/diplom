<?php

class Catalog_Observer_AdminItem_Bundle extends App_Event_Observer
{
    
    public function editOnLoad()
    {
        $controller = $this->getData(0);
        $values = $this->getData(1);
        if (array_key_exists('id', $values)) {
            $controller->getHelper('AdminItem')->session()->editingBundles = 
                Model_Service::factory('catalog/item-bundle')->getBundlesForItem($values['id']);
        }
    }
    
    public function editOnSave()
    {
        $controller = $this->getData(0);
        $values = $this->getData(1);
        if (array_key_exists('bundles', $values)) {
            $values['bundles'] = $controller->getHelper('AdminItem')->session()->editingBundles;
        }
        $this->getEvent()->setData(array($controller, $values))->setResponse($values);        
    }
    
    public function editOnAfterSave()
    {
        $controller = $this->getData(0);
        $values = $this->getData(1);
        $item = $this->getData(2);
        $values['bundles'] = $controller->getHelper('AdminItem')->session()->editingBundles;
        Model_Service::factory('catalog/item-bundle')->saveBundlesForItem($item->id, $values['bundles']);
        $this->getEvent()->setData(array($controller, $values))->setResponse($values);        
    }
    
}