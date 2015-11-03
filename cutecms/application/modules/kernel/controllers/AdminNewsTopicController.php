<?php

class AdminNewsTopicController extends Zend_Controller_Action
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
        $service = Model_Service::factory('news-topic');
        // $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        // $service->getHelper('Multisite')->setCurrentSiteId($siteId);        
        $this->view->topics = $service->paginatorGetAll(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        //$this->getHelper('AdminMultisite')->extendMassForm();
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
            Model_Service::factory('news-topic')->activateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were activated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not activated'));
        }
    }

    protected function _mass_deactivate(array $massCheck)
    {
        try {
            Model_Service::factory('news-topic')->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deactivated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deactivated'));
        }
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            Model_Service::factory('news-topic')->deleteByIdArray($massCheck);
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
            $service = Model_Service::factory('news-topic');
            $topicIds = $massCheck;
            $service->getHelper('Multisite')->linkToSiteByIdArray($topicIds, $siteIds);
            $newsService = Model_Service::factory('news');
            $newsIds = $newsService->getAllIdsByTopics($topicIds);
            $newsService->getHelper('Multisite')->linkToSiteByIdArray($newsIds, $siteIds);            
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
            $service = Model_Service::factory('news-topic');
            $topicIds = $massCheck;
            $service->getHelper('Multisite')->unlinkFromSiteByIdArray($topicIds, $siteIds);
            $newsService = Model_Service::factory('news');
            $newsIds = $newsService->getAllIdsByTopics($topicIds);
            $newsService->getHelper('Multisite')->unlinkFromSiteByIdArray($newsIds, $siteIds);            
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
        Model_Service::factory('news-topic')->changeSorting($this->_getParam('id'), $this->_getParam('position'));
        $url = $this->view->stdUrl(array('id'=>NULL, 'position'=>NULL), 'index');
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }


    /**
     * edit page
     */
    public function editAction()
    {
        $service = Model_Service::factory('news-topic');
        // $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        // $service->getHelper('Multisite')->setCurrentSiteId($siteId);
        
        // init form
        $form = new Form_AdminNewsTopicEdit;
        //$this->getHelper('AdminMultisite')->extendEditForm($form);
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('News topic edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'));
            echo 'redirected??';/*exit;*/
        }
        if ( ! $this->getRequest()->isPost()) {
        // if it was just called (via get)
            $values = array();
            if ( (int) $this->_getParam('id')) {
                //load $values from db
                $values = $service->getEditFormValues($this->_getParam('id'));
				$values['id'] = (int) $values['id'];
                // $sites = $service->getHelper('Multisite')->getLinkedSites($values['id']);
                // $this->getHelper('AdminMultisite')->setSiteIdsValueToArray($values, $sites);
                
            }
            else {
                //init $values
                $values = $service->create()->toArray();
                // $this->getHelper('AdminMultisite')->addDefaultSiteIdsValueToArray($values);
            }
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
            return;
        }
        else {
        // if the form was posted
            $values = $this->getRequest()->getParams();
            // $this->getHelper('AdminMultisite')->checkSiteIds($values);
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
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('News topic saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'));
        return;

    }


    /**
     * delete page
     */
    public function deleteAction()
    {
        $service = Model_Service::factory('news-topic');
        $obj = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    if ( ! $this->getHelper('AdminMultisite')->isAllowedMultisite()) {
                        $service->getHelper('Multisite')->unlinkFromSite($obj->id, array($this->getHelper('AdminMultisite')->getSiteId()));
                    }
                    else {
                        $service->delete($obj);
                    }                                        
                                        
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('News topic "%1$s" deleted', $obj->name));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete topic "%1$s" (%2$s)', $obj->name, $obj->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'index'));
        }
        else {
            $this->view->topic = $obj;
            $this->view->form = $form;
        }
    }

}

