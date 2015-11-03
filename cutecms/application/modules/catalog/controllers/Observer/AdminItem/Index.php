<?php

class Catalog_Observer_AdminItem_Index extends App_Event_Observer
{
    
    public function editOnLoad()
    {
        $controller = $this->getData(0);
        $values = $this->getData(1);
        if (array_key_exists('brules', $values)) {
            $controller->getHelper('AdminItem')->session()->editingBrules = $values['brules'];
        }
    }
    
    public function editOnSave()
    {
        $controller = $this->getData(0);
        $values = $this->getData(1);
        $values['brules'] = $controller->getHelper('AdminItem')->session()->editingBrules;
        $this->getEvent()->setData(array($controller, $values))->setResponse($values);        
    }

}