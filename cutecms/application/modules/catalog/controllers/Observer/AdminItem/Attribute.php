<?php

class Catalog_Observer_AdminItem_Attribute extends App_Event_Observer
{
    
    public function editOnLoad()
    {
        $controller = $this->getData(0);
        $values = $this->getData(1);
        if (array_key_exists('attributes', $values)) {
            $controller->getHelper('AdminItem')->session()->editingAttributes = $values['attributes'];
        }
    }
    
    public function editOnSave()
    {
        $controller = $this->getData(0);
        $values = $this->getData(1);
        $attrs = $controller->getHelper('AdminItem')->session()->editingAttributes;
        foreach ($attrs as $attr) {
            $key = 'attribute_value_'.$attr->code;
            if (array_key_exists($key, $values)) {
                $attr->current_value = $values[$key];
            }
        }
        $values['attributes'] = $attrs;
        $this->getEvent()->setData(array($controller, $values))->setResponse($values);
    }
    
}