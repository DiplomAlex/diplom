<?php

class Catalog_Observer_AdminItem_Xsell extends App_Event_Observer
{
    
    public function editOnLoad()
    {
        $controller = $this->getData(0);
        $values = $this->getData(1);
        $controller->getHelper('AdminItem')->session()->editingXsells = @$values['xsells'];
    }
    
    public function editOnSave()
    {
        $controller = $this->getData(0);
        $values = $this->getData(1);
        $values['xsells'] = $controller->getHelper('AdminItem')->session()->editingXsells;
        $this->getEvent()->setData(array($controller, $values))->setResponse($values);        
    }
    
}