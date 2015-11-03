<?php

class AdminArticleController extends Zend_Controller_Action
{

    protected $_session = NULL;
    protected $_service = NULL;
    protected $_serviceTopic = NULL;
    
    protected $_defaultInjections = array(
        'Form_Edit' => 'Form_AdminArticleEdit',
        'Form_Delete' => 'App_Form_Question',
    );

    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
        $this->_helper->Injector($this->_defaultInjections);
        $this->_helper->AdminGallery($this->_getService()->getHelper('Gallery'));
    }

    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace(__CLASS__);
        }
        return $this->_session;
    }
    
    protected function _getService()
    {
        if ($this->_service === NULL) {
            $this->_service = Model_Service::factory('article');
        }
        return $this->_service;
    }

    protected function _getTopicService()
    {
        if ($this->_serviceTopic === NULL) {
            $this->_serviceTopic = Model_Service::factory('article-topic');
        }
        return $this->_serviceTopic;
    }

    /**
     * show list
     */
    public function indexAction()
    {
        $this->getHelper('ReturnUrl')->remember();
        $topicId = $this->_getParam('topic');
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $this->_getService()->getHelper('Multisite')->setCurrentSiteId($siteId);        
        
        $this->view->articles = $this->_getService()->paginatorGetAllByTopic(
            $topicId,
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        if ($topicId) {
            $this->view->topicId = $topicId;
        }
        else {
            $this->view->topicId = NULL;
        }
        $this->view->gotoHtml = $this->view->partial('admin-article/goto-form.phtml', array(
                                    'options'=>$this->_prepareGotoOptions(),
                                    'topic' =>$topicId,
                                ));
        $this->getHelper('AdminMultisite')->extendMassForm();
    }


    /**
     * prepare options for "goto" select
     * @return array
     */
    protected function _prepareGotoOptions()
    {
        $list = $this->_getTopicService()->getFullTreeAsSelectOptions(NULL, TRUE);
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
            $this->_getService()->activateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were activated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not activated'));
        }
    }

    protected function _mass_deactivate(array $massCheck)
    {
        try {
            $this->_getService()->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deactivated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deactivated'));
        }
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            $this->_getService()->deleteByIdArray($massCheck);
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
            $service = $this->_getService();
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
            $service = $this->_getService();
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
        $this->_getService()->changeSorting($this->_getParam('id'), $this->_getParam('position'));
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
        $service = $this->_getService();
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);
        if ( ! $this->getHelper('AdminMultisite')->isAllowedMultisite()) {
            Model_Service::factory('article-topic')->getHelper('Multisite')->setCurrentSiteId($siteId);
        }     
        // init form
        $form = $this->getHelper('Injector')->getInjector()->getObject('Form_Edit');
        $this->getHelper('AdminGallery')->extendEditForm($form);
        $this->getHelper('AdminMultisite')->extendEditForm($form);        
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Article edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($cancelUrl);
        }
        if ( ! $this->getRequest()->isPost()) {
        // if it was just called (via get)
            $this->getHelper('AdminGallery')->clearLinked();
            $values = array();
            $id = (int) $this->_getParam('id'); 
            if ($id) {
                //load $values from db
                $values = $service->getEditFormValues($id);
                $this->getHelper('AdminGallery')->loadLinked($id);
				$values['id'] = (int) $values['id'];
                $sites = $service->getHelper('Multisite')->getLinkedSites($values['id']);              
                $this->getHelper('AdminMultisite')->setSiteIdsValueToArray($values, $sites);                
            }
            else {
                //init $values
                $values = $service->create()->toArray();
                if ($this->_getParam('topic')) {
                    $topic = $this->_getTopicService()->getComplex($this->_getParam('topic'));
                    $values['topics'] = array($topic->id => $topic->id);
                }
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
        $obj = $service->saveFromValues($values, TRUE);
        $this->getHelper('AdminGallery')->saveLinked($obj);
        // add message to flash queue
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Article saved'));
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
        $service = $this->_getService();
        $obj = $service->getComplex($this->_getParam('id'));
        $form = $this->getHelper('Injector')->getInjector()->getObject('Form_Delete');
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
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Article "%1$s" deleted', $obj->title));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete article "%1$s")', $obj->title));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        }
        else {
            $this->view->article = $obj;
            $this->view->form = $form;
        }
    }


}

