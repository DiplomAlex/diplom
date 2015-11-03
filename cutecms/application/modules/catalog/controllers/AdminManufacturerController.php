<?php

class Catalog_AdminManufacturerController extends Zend_Controller_Action
{
    
    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }
    

    /**
     * show list of pages
     */
    public function indexAction()
    {
        //var_dump(class_exists('Catalog_Model_Mapper_Db_Manufacturer'));exit;
        $this->view->manufacturers = Model_Service::factory('catalog/manufacturer')->paginatorGetAll(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
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
            Model_Service::factory('catalog/manufacturer')->activateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were activated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not activated'));
        }
    }

    protected function _mass_deactivate(array $massCheck)
    {
        try {
            Model_Service::factory('catalog/manufacturer')->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deactivated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deactivated'));
        }
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            Model_Service::factory('catalog/manufacturer')->deleteByIdArray($massCheck);
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
        Model_Service::factory('catalog/manufacturer')->changeSorting($this->_getParam('id'), $this->_getParam('position'));
        $url = $this->view->stdUrl(array('id'=>NULL, 'position'=>NULL), 'index');
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }


    /**
     * edit page
     */
    public function editAction()
    {
        $service = Model_Service::factory('catalog/manufacturer');
        // init form
        $form = new Catalog_Form_AdminManufacturerEdit;
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'))->sendResponse();
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
            }
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
            return;
        }
        else {
        // if the form was posted
            $values = $this->getRequest()->getParams();
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
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Manufacturer saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('reset'=>TRUE), 'index', 'admin-manufacturer', 'catalog'));
        return;

    }


    /**
     * delete page
     */
    public function deleteAction()
    {
        $service = Model_Service::factory('catalog/manufacturer');
        $manufacturer = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->delete($manufacturer);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Manufacturer "%1$s" deleted', $manufacturer->name));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete manufacturer "%1$s" (%2$s)', $manufacturer->name, $manufacturer->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'index'));
        }
        else {
            $this->view->manufacturer = $manufacturer;
            $this->view->form = $form;
        }
    }
    
    
}