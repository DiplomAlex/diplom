<?php

class Catalog_Model_Object_Attribute extends Model_Object_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Variant'     => 'Catalog_Model_Object_AttributeVariant',
        'Model_Collection_Variant' => 'Catalog_Model_Collection_AttributeVariant',
    );

    protected $_variantsParsed = array();

    public function init()
    {

        $this->addElements(array(
            'id',
            'status',
            'sort',
            'code',
            'name',
            'brief',
            'date_adder', 'date_changed',
            'adder_id', 'changer_id',
            'type',
            'variants','variants_xml','variants_text', 'variants_array',
            'default_value',
            'current_value',
            'param1','param2','param3',
            'hash',
            'attribute_groups',
        ));

        $langs = Model_Service::factory('language')->getAllActive();
        foreach ($langs as $lang) {
            $this->addElement('description_language_'.$lang->code2.'_name');
        }
        foreach ($langs as $lang) {
            $this->addElement('description_language_'.$lang->id.'_name');
        }

    }

    public function getName()
    {
        $lang = Model_Service::factory('language')->getCurrent();
        if (( ! $name = @$this->_elements['description_language_'.$lang->code2.'_name'])
            AND
            ( ! $name = @$this->_elements['description_language_'.$lang->id.'_name'])
           ) {
            $name = $this->_elements['name'];
        }
        return $name;
    }

    public function setName($value)
    {
        $this->_elements['name'] = $value;
        return $this;
    }
    
    public function getCode()
    {
        if (empty($this->_elements['code'])) {
            $this->_elements['code'] = uniqid('attribute_');
        }
        return $this->_elements['code'];
    }


    protected function _validTypes()
    {
        return Model_Service::factory('catalog/attribute')->getAllTypes();
    }

    protected function _validType($int)
    {
        $types = $this->_validTypes();
        return $types[$int];
    }

    public function getHash()
    {
        if (empty($this->_elements['hash'])) {
            $this->_elements['hash'] = uniqid('attribute_');
        }
        return $this->_elements['hash'];
    }

    public function getVariants()
    {
        $attrService = Model_Service::factory('catalog/attribute');
        $val = $this->_elements['variants'];
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $attrService->createVariantCollection();
        }
        else {
            $newVal = $attrService->parseVariantsFromXML($val);
        }
        $this->_elements['variants'] = $newVal;
        return $this->_elements['variants'];
    }

    public function setVariants($val)
    {
        $attrService = Model_Service::factory('catalog/attribute');
        if ($val instanceof Model_Collection_Interface) {
            $newVal = $val;
        }
        else if (empty($val)) {
            $newVal = $attrService->createVariantCollection();
        }
        else {
            $newVal = $attrService->parseVariantsFromXML($val);
        }
        $this->_elements['variants'] = $newVal;
        return $this;
    }

    public function getVariants_array()
    {
        $vars = array();
        foreach ($this->_elements['variants'] as $var) {
            $vars[$var->value] = $var->text;
        }
        return $vars;
    }
    
    public function isInputRequired($checkRequired = FALSE)
    {    
        $result = empty($this->current_value);
        if ($checkRequired) {
            $result = (( (bool) $result) AND ( (bool) $this->required)/* AND ( (bool) $this->status)*/);
        }
        return $result;
    }
    
    public function getCurrent_variant()
    {
        if (( ! $this->current_value) OR ($this->type != 'variant') OR ( ! $this->variants)) {
            $result = NULL;
        }
        else {
            $result = $this->variants->findOneByValue($this->current_value);
        }
        return $result;
    }

}