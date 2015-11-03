<?php

class Catalog_View_Helper_Attribute_Draw extends Zend_View_Helper_Abstract
{
    
    protected $_decimalFormat = '%01.2f';
    
    protected $_defaultInputHtmlAttribs = array('class' => 'attribute');
    
    protected $_variantInputTypes = array(
        'select', 'radio',
    );    
    protected $_variantInputType = 'select';
    
    protected $_addNoneVariant = FALSE;
    
    protected $_inputOnlyMode = FALSE;
    
    protected $_scriptInputRequired = 'attribute/input-required.phtml';
    protected $_scriptInputNotRequired = 'attribute/input-not-required.phtml';

    public function attribute_Draw(Model_Object_Interface $attr = NULL)
    {
        if ($attr === NULL) {
            return $this;
        }
        
        $this->view->attr = $attr;        

        if ($attr->isInputRequired() OR ($this->_inputOnlyMode === TRUE)) {
            $htmlAttribs = $this->_defaultInputHtmlAttribs;
            if (array_key_exists('name', $htmlAttribs)) {
                $inputName = $htmlAttribs['name'];
            }
            else {
                $inputName = $attr['code']; 
            }
            if ($attr['current_value']) {
                $inputValue = $attr['current_value'];
            }
            else {
                $inputValue = $attr['default_value'];
            }
            switch($attr['type']) {
                case 'int':
                case 'string':
                case 'decimal':
                case 'text':
                    $input = $this->view->formText($inputName, $inputValue, $htmlAttribs);
                    break;
                /*
                case 'text':
                    $input = $this->view->formTextarea($inputName, $inputValue, array_merge(array('rows'=>2, 'cols'=>40), $htmlAttribs));
                    break;
                */
                case 'variant':
                    $vars = array();
                    if ($this->_addNoneVariant === TRUE) {
                        $vars [''] = '  --  ';
                    }
                    foreach ($attr->variants as $v) {
                        $vars[$v['value']] = $v['text'];
                    }
                    $this->view->variantsArray = $vars;
                    if ($this->_variantInputType == 'select') {
                        $input = $this->view->formSelect($inputName, $inputValue, $htmlAttribs, $vars);
                    }
                    else if ($this->_variantInputType == 'radio') {
                        $input = $this->view->formRadio($inputName, $inputValue, $htmlAttribs, $vars);
                    }
                    else {
                        $input = '';
                    }
                    $this->view->inputType = $this->_variantInputType;
                    break;
            }
            $this->view->input = $input;
            $script = $this->_scriptInputRequired; 
        }        
        else {
            switch ($attr['type']) {
                case 'int':
                case 'string':
                case 'text':
                    $val = $attr['current_value'];
                    break;
                case 'decimal':
                    $val = sprintf($this->_decimalFormat, $attr['current_value']);
                    break;
                case 'datetime':
                    $val = $this->view->formatDate($attr['current_value']);
                    break;
                case 'variant':
                    if ($var = $attr->variants->findOneByValue($attr['current_value'])) {
                        $val = $var->text;
                    }
                    else {
                        $val = $attr['current_value'];
                    }
                    break;
            }
            $this->view->value = $val;
            $script = $this->_scriptInputNotRequired; 
        }
        return $this->view->render($script);
    }
	
    
    public function setVariantInputType($type)
    {
        if ( ! in_array($type, $this->_variantInputTypes)) {
            throw new Zend_View_Exception('in '.__CLASS__.'::'.__FUNCTION__.' trying to use invalid type:'.$type);
        }
        $this->_variantInputType = $type;
        return $this;
    }
    
    public function setScriptInputRequired($script)
    {
        $this->_scriptInputRequired = $script;
        return $this;
    }
    
    public function setScriptInputNotRequired($script)
    {
        $this->_scriptInputNotRequired = $script;
        return $this;
    }

    public function setDefaultInputHtmlAttribs(array $attribs)
    {
        $this->_defaultInputHtmlAttribs = $attribs;
        return $this;
    }
    
    public function setDecimalFormat($fmt)
    {
        $this->_decimalFormat = $fmt;
        return $this;
    }
    
    public function setAddNoneVariant($val)
    {
        $this->_addNoneVariant = $val;
        return $this;
    }
    
    public function setInputOnlyMode($val)
    {
        $this->_inputOnlyMode = $val;
        return $this;
    }
}