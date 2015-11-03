<?php

class Catalog_AdminItem_XsellController extends Zend_Controller_Action
{
    
    protected $_defaultInjections = array();
    
    public function init()
    {
        $this->_helper->AdminItem($this->_defaultInjections);        
    }


    

    /*********************************************************************************/
    /********************* business rules ********************************************/
    /*********************************************************************************/


    /**
     * get one or all brules of currently editing item:
     * if $_REQUEST['rowId'] isset then recieves one brule by its hash otherwise - returns all
     */
    public function ajaxGetBruleAction()
    {
        $rowId = $this->_getParam('id');
        if (empty($rowId)) {
            $rows = array();
            foreach ($this->_session()->editingBrules as $row) {
                $rows[] = array(
                    'id' => $row['hash'],
                    'cell' => array($row['code'], $row['name'], $row['param1'], $row['param2'], $row['param3']),
                );
            }
            $answer = array(
                'page' => '1',
                'total' => $this->_session()->editingBrules->count(),
                'rows' => $rows,
            );
        }
        else {
            $answer = array();
            foreach ($this->_session()->editingBrules as $key=>$var) {
                if ($var['hash'] == $rowId) {
                    $answer = $var->toArray();
                    break;
                }
            }
        }
        echo Zend_Json::encode($answer);
    }


    /**
     * edit existing brule of item
     * checks duplications by code
     * $_REQUEST should contain a map of the brule ($field=>$value)
     */
    public function ajaxEditBruleAction()
    {
        $rowId = $this->_getParam('id');
        $values = $this->getRequest()->getParams();
        $found = FALSE;
        foreach ($this->_session()->editingBrules as $brule) {
            if ($brule['hash'] == $rowId) {
                foreach ($values as $valKey=>$valVal) {
                    if ($brule->hasElement($valKey)) {
                        $brule[$valKey] = $valVal;
                    }
                }
                $found = TRUE;
                break;
            }
        }
        if ( ! $found) {
            $brule = Model_Service::factory('catalog/brule')->create();
            foreach ($values as $valKey=>$valVal) {
                if ($brule->hasElement($valKey)) {
                    $brule[$valKey] = $valVal;
                }
            }
            $this->_session()->editingBrules->add($brule);
        }
        echo 'ok';
    }

    /**
     * $_REQUEST['rows'] contains array of brules hashes (each added with prefix "row")
     */
    public function ajaxDeleteBruleAction()
    {
        $rows = $this->_getParam('rows');
        foreach ($rows as $rowId) {
            foreach ($this->_session()->editingBrules as $key=>$attr) {
                if ($attr['hash'] == $rowId) {
                    $this->_session()->editingBrules->remove($key);
                    break;
                }
            }
        }
        echo 'ok';
    }

    
    
}