<?php

class View_Helper_AdminMassForm extends App_View_Helper_MassForm_Abstract
{

    protected $_massFormHelper = 'adminMassForm';
    protected $_scriptsPath = 'admin-index/mass';
    
    protected $_scriptActionsWithPath = NULL;

    public function adminMassForm($type = NULL, $params = NULL)
    {
        return $this->massForm($type, $params);
    }
    
    public function setScriptActionsWithPath($value)
    {
        $this->_scriptActionsWithPath = $value;
        return $this;
    }

    protected function _actions($params = NULL)
    {
        if (isset($params['script'])) {
            $script = $params['script'];
        }
        else if ($this->_scriptActionsWithPath !== NULL) {
            $script = $this->_scriptActionsWithPath;
        }
        else {
            $script = $this->_getScriptPath($this->_scriptActions);
        }
        return $this->_render($script, $params);
    }

    protected function _select($params = NULL)
    {
        if (isset($params['script'])) {
            $script = $params['script'];
        }
        else {
            $script = $this->_getScriptPath($this->_scriptSelect);
        }
        if ( ! isset($params['options'])) {
            $params['options'] = $this->_selectActions();
        }
        return $this->_render($script, $params);
    }


}