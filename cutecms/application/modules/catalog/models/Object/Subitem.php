<?php

class Catalog_Model_Object_Subitem extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'hash',
            'id', 'seo_id', 'sku',
            'name', 'alias', 'brief',
            'date_added', 'date_changed',
            'adder_id', 'changer_id',
            'rc_id', 'rc_id_filename', 'rc_id_preview',  'rc_id_preview2',
            'param1', 'param2', 'param3',
            'qty', 'stock_qty', 'min_qty', 'max_qty',       
            'price', 'old_price',                
            'attributes', 'attributes_html', 'attributes_text', 
        ));
    }
    
    public function getMin_qty()
    {
        if ( ! $this->_elements['min_qty']) {
            $this->_elements['min_qty'] = 1;
        }
        return $this->_elements['min_qty'];
    } 
    
    public function getMax_qty()
    {
        if ( ! $this->_elements['max_qty']) {
            $this->_elements['max_qty'] = 1;
        }
        return $this->_elements['max_qty'];
    } 
    
    public function getSpec_as_text()
    {
        return $this->name . ' ' . $this->attributes_text;
    }
    
    public function getSpec_as_html()
    {
        return $this->name . ' ' . $this->attributes_html;
    }
    
    public function getHash()
    {
        if (empty($this->_elements['hash'])) {
            $this->_elements['hash'] = uniqid('subitem');
        }
        return $this->_elements['hash'];
    }
    

    public function getAttributes()
    {
        $service = Model_Service::factory('catalog/subitem');
        $val = $this->_elements['attributes'];
        if (is_array($val)) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = array();
        }
        else {
            $newVal = $service->parseAttributesFromXML($val);
        }
        $this->_elements['attributes'] = $newVal;
       return $this->_elements['attributes'];
    }


    public function setAttributes($val)
    {
        $service = Model_Service::factory('catalog/subitem');
        
        /*
        if (is_array($val)) {
            $newVal = $val;
            $this->_elements['attributes'] = $newVal;
        }
        else if (empty($val)) {
            $newVal = array();
            $this->_elements['attributes'] = $newVal;
            $this->_elements['attributes_text'] = '';
            $this->_elements['attributes_html'] = '';
        }
        else {
            $newVal = $service->getAttributesSpecification($val);
            $this->_elements['attributes'] = $newVal['array'];
            $this->_elements['attributes_text'] = $newVal['text'];
            $this->_elements['attributes_html'] = $newVal['html'];
        }
        */

        if (empty($val)) {
            $newVal = array();
            $this->_elements['attributes'] = $newVal;
            $this->_elements['attributes_text'] = '';
            $this->_elements['attributes_html'] = '';
        }
        else if (is_string($val)) {            
            if (($newVal = unserialize($val)) !== FALSE) {
                $this->_elements['attributes'] = $newVal;            
            }
            else {
                $newVal = $service->getAttributesSpecification($val);
                $this->_elements['attributes'] = $newVal['array'];
                $this->_elements['attributes_text'] = $newVal['text'];
                $this->_elements['attributes_html'] = $newVal['html'];
            }
        }
        else {
            $newVal = $val;
            $this->_elements['attributes'] = $newVal;            
        }
        
        
        return $this;
    }

    
        
}