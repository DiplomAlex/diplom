<?php

class Catalog_View_Helper_Bundle_Draw extends Zend_View_Helper_Abstract
{

    protected $_defaultInputHtmlAttribs = array('class' => 'bundle');
    
    protected $_inputTypes = array(
        'select', 'radio', 'checkbox',
    );        
    protected $_inputType = 'radio';
    
    protected $_scriptInputRequired = 'item-bundle/input-required.phtml';
    protected $_scriptInputNotRequired = 'item-bundle/input-not-required.phtml';
    
    
    public function bundle_Draw(Model_Object_Interface $bundle = NULL) 
    {
        if ($bundle === NULL) {
            return $this;
        }
        $this->view->bundle = $bundle;
        if ($bundle->isInputRequired()) {
            $script = $this->_scriptInputRequired;
            $this->view->inputType = $this->_inputType;
            $subs = array();
            foreach ($bundle->subitems as $sub) {
                $subs[$sub->id] = $sub->alias?$sub->alias:$sub->name;
            }
            $_defaultInputHtmlAttribs = $this->_defaultInputHtmlAttribs;
            if ($this->_inputType == 'select') {
                $input = $this->view->formSelect($bundle->code, $bundle->default_subitem_id, $_defaultInputHtmlAttribs, $subs);
            } 
            else if ($this->_inputType == 'radio') {
                $input = $this->view->formRadio($bundle->code, $bundle->default_subitem_id, $_defaultInputHtmlAttribs, $subs);                
            }
            else if ($this->_inputType == 'checkbox') {
                $input = $this->view->formMultiCheckbox($bundle->code, $bundle->default_subitem_id, $_defaultInputHtmlAttribs, $subs);                
            }
            else {
                $input = '';
            }
            $this->view->input = $input;
        }
        else {
            $script = $this->_scriptInputNotRequired;
            if ($bundle->subitems AND $bundle->subitems->count()) {
                $sub = $bundle->subitems->get(0);
                $this->view->value =  $sub->alias?$sub->alias:$sub->name;
            }
        }
        return $this->view->render($script);
    }
    
        
    public function setInputType($type)
    {
        if ( ! in_array($type, $this->_inputTypes)) {
            throw new Zend_View_Exception('in '.__CLASS__.'::'.__FUNCTION__.' trying to use invalid type:'.$type);
        }
        $this->_inputType = $type;
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
    
    
}