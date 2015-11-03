<?php

class AdminFaqController extends Zend_Controller_Action
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
        $service = Model_Service::factory('faq');
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);                
        $this->view->faqs = $service->paginatorGetAll(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        $this->getHelper('AdminMultisite')->extendMassForm();
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
            Model_Service::factory('faq')->activateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were activated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not activated'));
        }
    }

    protected function _mass_deactivate(array $massCheck)
    {
        try {
            Model_Service::factory('faq')->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deactivated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deactivated'));
        }
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            Model_Service::factory('faq')->deleteByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deleted'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deleted'));
        }
    }
    
    protected function _mass_linkToSite(array $massCheck)
    {
        try {
            $siteIds = $this->_getParam('mass_site_ids');
            $service = Model_Service::factory('faq');
            $service->getHelper('Multisite')->linkToSiteByIdArray($massCheck, $siteIds);            
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were linked'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not linked'));
        }        
    }

    protected function _mass_unlinkFromSite(array $massCheck)
    {
        try {
            $siteIds = $this->_getParam('mass_site_ids');
            $service = Model_Service::factory('faq');
            $service->getHelper('Multisite')->unlinkFromSiteByIdArray($massCheck, $siteIds);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were unlinked'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not unlinked'));
        }                
    }         
    

    /**
     * change object's sorting position in list
     */
    public function sortingAction()
    {
        Model_Service::factory('faq')->changeSorting($this->_getParam('id'), $this->_getParam('position'));
        $url = $this->view->stdUrl(array('id'=>NULL, 'position'=>NULL), 'index');
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }


    /**
     * edit page
     */
    public function editAction()
    {
        $service = Model_Service::factory('faq');
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);                 
        // init form
        $form = new Form_AdminFaqEdit;
        $this->getHelper('AdminMultisite')->extendEditForm($form);
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('FAQ edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'))->sendResponse();
        }
        if ( ! $this->getRequest()->isPost()) {
        // if it was just called (via get)
            $values = array();
            if ( (int) $this->_getParam('id')) {
                //load $values from db
                $values = $service->getEditFormValues($this->_getParam('id'));
				$values['id'] = (int) $values['id'];
                $sites = $service->getHelper('Multisite')->getLinkedSites($values['id']);            
                $this->getHelper('AdminMultisite')->setSiteIdsValueToArray($values, $sites);                
            }
            else {
                //init $values
                $values = $service->create()->toArray();
                $this->getHelper('AdminMultisite')->addDefaultSiteIdsValueToArray($values);
            }
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
            return;
        }
        else {
        // if the form was posted
            $values = $this->getRequest()->getParams();
            $this->getHelper('AdminMultisite')->checkSiteIds($values);
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
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('FAQ saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('reset'=>TRUE), 'index', 'admin-faq'))->sendResponse();
        return;

    }


    /**
     * delete page
     */
    public function deleteAction()
    {
        $service = Model_Service::factory('faq');
        $faq = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    if ( ! $this->getHelper('AdminMultisite')->isAllowedMultisite()) {
                        $service->getHelper('Multisite')->unlinkFromSite($faq->id, array($this->getHelper('AdminMultisite')->getSiteId()));
                    }
                    else {
                        $service->delete($faq);
                    }                                        
                                        
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('FAQ "%1$s" deleted', $faq->quest));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete faq "%1$s" (%2$s)', $faq->quest, $faq->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'index'));
        }
        else {
            $this->view->faq = $faq;
            $this->view->form = $form;
        }
    }

}

