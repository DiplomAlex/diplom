<?php

class Catalog_Model_Object_ItemBundle extends Model_Object_Abstract
{
    
    public function init()
    {
        $this->addElements(array(
            'id',
            'hash',
            'code',
            'item_id',
            'status',
            'name', 
            'brief',
            'full',
            'item_id',
            'subitems', 'subitems_xml',
            'is_required',
            'current_subitem_name', 'current_subitem_id', 'current_subitem_price', 'current_subitem_qty',    
            'default_subitem_name', 'default_subitem_id', 'default_subitem_price', 'default_subitem_qty',
            'param1', 'param2', 'param3', 
        ));
    }
    
    
    public function getHash()
    {
        if (empty($this->_elements['hash'])) {
            $this->_elements['hash'] = uniqid('item_bundle');
        }
        return $this->_elements['hash'];
    }

    
    public function getCode()
    {
        if (empty($this->_elements['code'])) {
            $this->_elements['code'] = uniqid('item_bundle_');
        }
        return $this->_elements['code'];
    }
        
    
    public function getSubitems()
    {
        $service = Model_Service::factory('catalog/item-bundle');
        $val = $this->_elements['subitems'];
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $service->createSubitemsCollection();
        }
        else {
            $newVal = $service->parseSubitemsFromXML($val);
        }
        $this->_elements['subitems'] = $newVal;
        return $this->_elements['subitems'];
    }

    public function setSubitems($val)
    {
        $service = Model_Service::factory('catalog/item-bundle');
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $service->createSubitemsCollection();
        }
        else {
            $newVal = $service->parseSubitemsFromXML($val);
        }
        $this->_elements['subitems'] = $newVal;
        return $this;
    }

    public function getSubitems_array()
    {
        $subs = array();
        foreach ($this->_elements['subitems'] as $sub) {
            $subs[$sub->id] = $sub->spec_as_html;
        }
        return $subs;
    }
    
    public function getIs_required()
    {
        return (int) (bool) $this->_elements['is_required'];
    }
    
    public function isInputRequired()
    {    
        if ($this->subitems AND $this->current_subitem_id) {
            $result = FALSE;
        }
        else if ($this->subitems AND (($this->subitems->count() > 1) AND $this->is_required)) {
            $result = TRUE;
        }
        else {
            $result = FALSE;
        }
        return $result;
    } 
        
    
    public function getCurrent_subitem()
    {
        if ($this->subitems AND ($this->subitems->count() == 1)) {
            $result = $this->subitems->get(0);
        }
        else if ($this->subitems AND ($this->_elements['current_subitem_id'])) {
            $result = $this->subitems->findOneById($this->_elements['current_subitem_id']);
        }
        else {
            $result = NULL;
        }
        return $result;
    }

    public function getCurrent_subitem_id()
    {
        if ($subitem = $this->current_subitem) {
            $result = $subitem->id;
        }
        else {
            $result = NULL;
        }
        return $result;
    }

    public function getCurrent_subitem_name()
    {
        if ($subitem = $this->current_subitem) {
            $result = $subitem->alias?$subitem->alias:$subitem->name;
        }
        else {
            $result = NULL;
        }
        return $result;
    }

    public function getCurrent_subitem_price()
    {
        if ($subitem = $this->current_subitem) {
            $result = $subitem->price;
        }
        else {
            $result = NULL;
        }
        return $result;
    }

    
    
}