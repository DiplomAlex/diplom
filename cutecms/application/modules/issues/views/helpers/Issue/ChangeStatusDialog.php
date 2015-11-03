<?php

class Issues_View_Helper_Issue_ChangeStatusDialog extends Zend_View_Helper_Abstract
{
    protected $_options = array(
        'dialogId' => 'change_status_dialog',
        'dateDueField' => 'date_due',
        'statusField' => 'status',
        'linkClass' => 'change-status-link',
        'statusClass' => 'status',
        'dateDueClass' => 'date-due',
    );

    public function issue_ChangeStatusDialog($render = FALSE)
    {
        if ( ! $render) {
            return $this;
        }
        $params = $this->_options;
        $params['changeStatusForm'] = new Issues_Form_IssueChangeStatus;
        $xhtml = $this->view->partial('box/change-status-dialog.phtml', $params);
        return $xhtml;
    }

    public function __call($name, $params)
    {
        if (substr($name, 0, 3)=='get') {
            $optName = lcfirst(substr($name, 3));
            if (array_key_exists($optName, $this->_options)) {
                return $this->_options[$optName];
            }
            else {
                throw new Zend_View_Exception('no such option "'.$optName.'" in '.__CLASS__);
            }
        }
    }

}