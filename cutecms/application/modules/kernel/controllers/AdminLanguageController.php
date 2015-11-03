<?php

class AdminLanguageController extends Zend_Controller_Action
{ 
    
    protected $_defaultInjections = array(
        'Form_Edit'   => 'Form_AdminLanguageEdit',
        'Form_Delete' => 'App_Form_Question',
    );
    
    protected function _getService()
    {
        return Model_Service::factory('language');
    }
    
    public function init()
    {
        $this->_helper->Injector($this->_defaultInjections);
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }
    
    public function indexAction()
    {
        $service = $this->_getService();
        $this->view->languages = $service->getAll();
    }
    
    public function editAction()
    {
        $service = $this->_getService();
        // init form
        $form = $this->_helper->Injector()->getObject('Form_Edit');
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Language edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index', 'admin-language'));
        }
        if ($this->getRequest()->isPost()) {
        // if the form was posted
            $values = $this->getRequest()->getParams();
            // validate it
            if ($form->isValid($values)) {
                // save 
                $service->saveFromValues($values);
                // add message to flash queue
                $this->getHelper('flashMessenger')->addMessage($this->view->translate('Language saved'));
                //redirect
                $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('reset'=>TRUE), 'index', 'admin-language'));
            }
            else {
                $form->populate($values);
                $this->view->form = $form;
                $this->view->values = $values;
                $this->getHelper('flashMessenger')->addMessage($this->view->translate('Form validation failed'));            
            }
        }
        else {
        // if it was just called (via get)
            $values = array();
            $id = $this->_getParam('id');
            if ( (int) $id) {
                //load $values from db
                $values = $service->getEditFormValues($id);
            }
            else {
                //init $values
                $values = $service->create()->toArray();
            }
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
        }
        
    }
    
    public function deleteAction()
    {
        $service = $this->_getService();
        $obj = $service->getComplex($this->_getParam('id'));
        $form = $this->_helper->Injector()->getObject('Form_Delete');
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->delete($obj);                                        
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Language "%1$s" deleted', $obj->title));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete language "%1$s" (%2$s)', $obj->title, $obj->code2));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'index', 'admin-language'));
        }
        else {
            $this->view->lang = $obj;
            App_Debug::dump($this->view->lang->toArray());
            $this->view->form = $form;
        }
        
    }
    
    /**
     * change object's sorting position in list
     */
    public function sortingAction()
    {
        Model_Service::factory('language')->changeSorting($this->_getParam('id'), $this->_getParam('position'));
        $url = $this->view->stdUrl(array('id'=>NULL, 'position'=>NULL), 'index');
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }

    
    
}