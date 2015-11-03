<?php

class AdminSiteController extends Zend_Controller_Action
{
    
    protected $_defaultInjections = array(
        'Form_Edit' => 'Form_AdminSiteEdit',
        'Form_Delete' => 'App_Form_Question',
    );

    public function init()
    {
        $this->_helper->Injector($this->_defaultInjections);
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }


    /**
     * show list of pages
     */
    public function indexAction()
    {
        $this->view->sites = Model_Service::factory('site')->paginatorGetAll(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
    }


    /**
     * edit page
     */
    public function editAction()
    {
        $service = Model_Service::factory('site');
        // init form
        $form = $this->_helper->Injector()->getObject('Form_Edit');
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Site edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index', 'admin-site'));
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
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Page saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('reset'=>TRUE), 'index', 'admin-site'));
        return;

    }


    /**
     * delete page
     */
    public function deleteAction()
    {
        $service = Model_Service::factory('site');
        $site = $service->getComplex($this->_getParam('id'));
        $form = $this->_helper->Injector()->getObject('Form_Delete');
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->delete($site);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Site "%1$s" deleted', $site->host.'/'.$site->base_url));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete site "%1$s"', $site->host.'/'.$site->base_url));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'index', 'admin-site'));
        }
        else {
            $this->view->site = $site;
            $this->view->form = $form;
        }
    }

}

