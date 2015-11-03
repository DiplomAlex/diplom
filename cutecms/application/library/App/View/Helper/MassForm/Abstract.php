<?php

/**
 * helper to create mass actions for operating lists
 */

class App_View_Helper_MassForm_Abstract extends Zend_View_Helper_Abstract
{

    /**
     * id attribute of form
     * @var string
     */
    protected $_formId = 'mass_actions_form';

    /**
     * form method
     * @var string
     */
    protected $_formMethod = 'post';

    /**
     * action of controller to call
     * @var string
     */
    protected $_controllerAction = 'mass';

    /**
     * actions available in select
     * @var array
     */
    protected $_massActions = array(
        'activate', 'deactivate', 'delete',
    );

    /**
     * name of helper 'massForm'
     * @var string
     */
    protected $_massFormHelper = 'massForm';

    /**
     * view path for scripts of this helper
     * @var string
     */
    protected $_scriptsPath = 'index/mass';

    /**
     * scripts filenames
     * @var string
     */
    protected $_scriptFormStart = 'form_start.phtml';
    protected $_scriptFormEnd = 'form_end.phtml';
    protected $_scriptCheck = 'check.phtml';
    protected $_scriptCheckAll = 'check_all.phtml';
    protected $_scriptSelect = 'select.phtml';
    protected $_scriptActions = 'actions.phtml';


    /**
     * main helper function - renders different parts of mass actions form
     * executes helper action represented by $type with transmitted $params to it
     * @param string
     * @param array
     * @return string
     */
    public function massForm($type = NULL, $params = NULL)
    {
        if ($type === NULL) return $this;

        if (isset($params['formId'])) {
            $this->_formId = $params['formId'];
        }
        return $this->{'_'.$type}($params);
    }

    public function setMassActions(array $actions)
    {
        $this->_massActions = $actions;
        return $this;
    }

    protected function _render($script, $params)
    {
        return $this->view->partial($script, array(
            'params' => $params,
            'formId' => $this->_formId(),
            'formMethod' => $this->_formMethod(),
            'formAction' => $this->_formAction($params),
            'massFormHelperName' => $this->_massFormHelper,
        ));
    }

    protected function _formId($params = NULL)
    {
        return $this->_formId;
    }

    protected function _formMethod($params = NULL)
    {
        return $this->_formMethod;
    }

    protected function _formAction($params = NULL)
    {
        return $this->view->stdUrl(NULL, $this->_controllerAction);
    }

    protected function _getScriptPath($script)
    {
        if ( ! empty($this->_scriptsPath )) {
            $path = $this->_scriptsPath . '/';
        }
        else {
            $path = '';
        }
        return $path . $script;
    }

    protected function _formStart($params = NULL)
    {
        return $this->_render($this->_getScriptPath($this->_scriptFormStart), $params);
    }

    protected function _formEnd($params = NULL)
    {
        return $this->_render($this->_getScriptPath($this->_scriptFormEnd), $params);
    }

    protected function _check($params = NULL)
    {
        return $this->_render($this->_getScriptPath($this->_scriptCheck), $params);
    }

    protected function _checkAll($params = NULL)
    {
        return $this->_render($this->_getScriptPath($this->_scriptCheckAll), $params);
    }

    protected function _actions($params = NULL)
    {
        return $this->_render($this->_getScriptPath($this->_scriptActions), $params);
    }

    protected function _select($params = NULL)
    {
        if ( ! isset($params['options'])) {
            $params['options'] = $this->_selectActions();
        }
        return $this->_render($this->_getScriptPath($this->_scriptSelect), $params);
    }

    protected function _selectActions()
    {
        $arr = array(' << '.$this->view->translate('Select action').' >> ');
        foreach ($this->_massActions as $action) {
            $arr[$action] = $this->view->translate(ucfirst($action));
        }
        return $arr;
    }
    
}

