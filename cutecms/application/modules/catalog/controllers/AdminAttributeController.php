<?php

class Catalog_AdminAttributeController extends Zend_Controller_Action
{

    protected $_session = NULL;

    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
            $this->getHelper('ViewRenderer')->setNoRender();
        }
    }

    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace(__CLASS__);
        }
        return $this->_session;
    }
    
    
    
    public function ajaxGetValueFieldsAction()
    {
        $type = $this->_getParam('type', 'variant');
        $defValue = $this->_getParam('default_value', NULL);
        if ($type == 'variant') {
            $variantsArr = array('' => ' -- ');
            foreach ($this->_session()->editingVariants as $var) {
                $variantsArr[$var->value] = $var->text;
            }
            $answer = array(
                'fieldDefaultValue' => $this->view->formSelect('default_value', $defValue, array(
                    'class' => 'select',
                ), $variantsArr),
            );            
        }
        else if ($type == 'text') {
            $answer = array(
                'fieldDefaultValue' => $this->view->formTextarea('default_value', $defValue, array(
                    'class' => 'input',
                    'rows' => 2,
                ), $variantsArr),
            );                        
        }
        else {
            if ($type == 'int') {
                $defValue = (int) $defValue;
            }
            else if ($type == 'decimal') {
                $defValue = (float) $defValue;
            }
            else if ($type == 'datetime') {
                if ( ! strtotime($defValue)) {
                    $defValue = '';
                }
            }
            $answer = array(
                'fieldDefaultValue' => $this->view->formText('default_value', $defValue, array(
                    'maxlength' => 200,
                    'size' => 90,
                    'class' => 'input',            
                )),
            );
        }
        echo Zend_Json::encode($answer);
    }
    
    
    

    /**
     * show list of pages
     */
    public function indexAction()
    {
        $this->getHelper('ReturnUrl')->remember();
        $groupId = $this->_getParam('group');
        $this->view->attributes = Model_Service::factory('catalog/attribute')->paginatorGetAllByGroup(
            $groupId,
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        if ($groupId) {
            $this->view->groupId = $groupId /*Model_Service::factory('catalog/attribute-group')->getComplex($groupId)*/;
        }
        else {
            $this->view->groupId = NULL;
        }
        $this->view->gotoHtml = $this->view->partial('admin-attribute/goto-form.phtml', array(
                                    'options'=>$this->_prepareGotoOptions(),
                                    'group' =>$groupId,
                                ));

    }


    /**
     * prepare options for "goto" select
     * @return array
     */
    protected function _prepareGotoOptions()
    {
        $list = array(0=> ' << '.$this->view->translate('All').' >> ');
        foreach (Model_Service::factory('catalog/attribute-group')->getAll() as $group) {
            $list[$group->id] = '    -    ' . $group->name;
        }
        return $list;
    }


    /**
     * serve mass actions from list
     */
    public function massAction()
    {
        if ( ! $massAction = $this->_getParam('mass_action')) {
            throw new Zend_Controller_Action_Exception('parameter mass_action should be set');
        }

        if (( ! $massCheck = $this->_getParam('mass_check')) OR ( ! is_array($massCheck))) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('No rows were selected for mass action'));
        }
        else {
            $this->{'_mass_'.$massAction}($massCheck);
        }

        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL, 'mass_check'=>NULL, 'mass_action'=>NULL), 'index'));
    }

    protected function _mass_activate(array $massCheck)
    {
        try {
            Model_Service::factory('catalog/attribute')->activateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were activated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not activated'));
        }
    }

    protected function _mass_deactivate(array $massCheck)
    {
        try {
            Model_Service::factory('catalog/attribute')->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deactivated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deactivated'));
        }
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            Model_Service::factory('catalog/attribute')->deleteByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deleted'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deleted'));
        }
    }

    /**
     * change object's sorting position in list
     */
    public function sortingAction()
    {
        Model_Service::factory('catalog/attribute')->changeSorting($this->_getParam('id'), $this->_getParam('position'));
        $url = $this->view->stdUrl(array('id'=>NULL, 'position'=>NULL), 'index');
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }


    /**
     * edit page
     */
    public function editAction()
    {
        if ( ! $cancelUrl = $this->getHelper('ReturnUrl')->get()) {
            $cancelUrl = $this->view->stdUrl(array('id'=>NULL), 'index');
        }
        if ( ! $submitUrl = $this->getHelper('ReturnUrl')->get()) {
            $submitUrl = $this->view->stdUrl(array('id'=>NULL), 'index');
        }
        $service = Model_Service::factory('catalog/attribute');
        // init form
        $form = new Catalog_Form_AdminAttributeEdit;
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Attribute edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($cancelUrl);
        }
        if ( ! $this->getRequest()->isPost()) {
        // if it was just called (via get)
            $values = array();
            if ( (int) $this->_getParam('id')) {
                //load $values from db
                $values = $service->getEditFormValues($this->_getParam('id'));
            }
            else {
                //init $values
                $values = $service->create()->toArray();
                if ($this->_getParam('group')) {
                    $group = Model_Service::factory('catalog/attribute-group')->getComplex($this->_getParam('group'));
                    $values['attribute_groups'] = array($group->id => $group->id);
                }
            }
            $this->_session()->editingVariants = $values['variants'];
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
            return;
        }
        else {
        // if the form was posted
            $values = $this->getRequest()->getParams();
            $values['variants'] = $this->_session()->editingVariants;
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
        }
        // validate it
        if ( ! $form->isValid($values)) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Form validation failed'));
            return;
        }
        // save
        $service->saveFromValues($values);
        // add message to flash queue
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Attribute saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        return;

    }


    /**
     * delete page
     */
    public function deleteAction()
    {
        if ( ! $submitUrl = $this->getHelper('ReturnUrl')->get()) {
            $submitUrl = $this->view->stdUrl(array('id'=>NULL), 'index');
        }
        $service = Model_Service::factory('catalog/attribute');
        $attr = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->delete($attr);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Attribute "%1$s" deleted', $attr->name));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete attribute "%1$s")', $attr->name, $attr->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        }
        else {
            $this->view->attr = $attr;
            $this->view->form = $form;
        }
    }


    public function ajaxGetVariantAction()
    {
        $rowId = $this->_getParam('id');
        $isSimple = (bool) $this->_getParam('simple');
        $answer = array();
        if (empty($rowId)) {
            $rows = array();
            foreach ($this->_session()->editingVariants as $row) {
                $rows[] = array(
                    'id' => $row['hash'],
                    'cell' => array($row['text'], $row['value'], $row['param1']),
                );
                if ($isSimple) {
                    $answer[$row['value']] = $row['text'];
                }
            }
            if ( ! $isSimple) {
                $answer = array(
                    'page' => '1',
                    'total' => ($this->_session()->editingVariants instanceof Countable?$this->_session()->editingVariants->count():'0'),
                    'rows' => $rows,
                );
            }
        }
        else {
            foreach ($this->_session()->editingVariants as $key=>$var) {
                if ($var['hash'] == $rowId) {
                    $answer = $var->toArray();
                    break;
                }
            }
        }
        echo Zend_Json::encode($answer);
    }


    public function ajaxEditVariantAction()
    {
        $service = Model_Service::factory('catalog/attribute');
        $rowId = $this->_getParam('id');
        $values = $this->getRequest()->getParams();
        $found = FALSE;
        foreach ($this->_session()->editingVariants as $key=>$var) {
            if ($var['hash'] == $rowId) {
                $var['text'] = $values['text'];
                $var['value'] = $values['value'];
                $var['param1'] = $values['param1'];
                $found = TRUE;
                break;
            }
        }
        if ( ! $found) {
            $new = $service->createVariantFromValues(array(
                'value'  => $values['value'],
                'text'   => $values['value'], /* it should be $values['text'] but imho using just value is easier for end user */
                'param1'  => $values['param1'],
            ));
            $this->_session()->editingVariants->add($new);
        }
        echo 'ok';
    }

    public function ajaxDeleteVariantAction()
    {
        $rows = $this->_getParam('rows');
        foreach ($rows as $rowId) {
            foreach ($this->_session()->editingVariants as $key=>$var) {
                if ($var['hash'] == $rowId) {
                    $this->_session()->editingVariants->remove($key);
                    break;
                }
            }
        }
        echo 'ok';
    }

}

