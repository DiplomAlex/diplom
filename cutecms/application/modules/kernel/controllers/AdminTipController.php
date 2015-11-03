<?php

class AdminTipController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }


    /**
     * show list
     */
    public function indexAction()
    {
        $this->view->tips = Model_Service::factory('tip')->paginatorGetAllActive(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
    }

    public function archiveAction()
    {
        $this->view->tips = Model_Service::factory('tip')->paginatorGetAllArchive(
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


    protected function _mass_delete(array $massCheck)
    {
        try {
            Model_Service::factory('tip')->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deleted'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deleted'));
        }
    }


    protected function _mass_deleteForever(array $massCheck)
    {
        try {
            Model_Service::factory('tip')->deleteByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deleted'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deleted'));
        }
    }


    /**
     * edit page
     */
    public function editAction()
    {
        $service = Model_Service::factory('tip');
        // init form
        $form = new Form_AdminTipEdit;
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Tip edition cancelled'));
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
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Tip saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('reset'=>TRUE), 'index', 'admin-tip'))->sendResponse();
        return;

    }


    /**
     * delete page
     */
    public function deleteAction()
    {
        $service = Model_Service::factory('tip');
        $tip = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->deactivate($tip);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Tip "%1$s" deleted', $tip->title));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete tip "%1$s" (%2$s)', $tip->title, $tip->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'index'));
        }
        else {
            $this->view->tip = $tip;
            $this->view->form = $form;
        }
    }


    public function deleteForeverAction()
    {
        $service = Model_Service::factory('tip');
        $tip = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->delete($tip);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Tip "%1$s" deleted', $tip->title));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete tip "%1$s" (%2$s)', $tip->title, $tip->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'archive'));
        }
        else {
            $this->view->tip = $tip;
            $this->view->form = $form;
        }
    }


    public function ajaxGetDestinationsAction()
    {
        $dests = Model_Service::factory('tip')->getAvailableDestinations($this->_getParam('role'));
        $html = $this->view->formSelect('destination', NULL, array('class'=>'select'), $dests);
        $this->getResponse()->setBody($html);
        $this->view->layout()->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
    }

}

