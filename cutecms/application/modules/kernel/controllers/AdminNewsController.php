<?php

class AdminNewsController extends Zend_Controller_Action
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
        $service = Model_Service::factory('news');
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);        
        $this->view->news = $service->paginatorGetAll(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        //$this->view->totalSubscribers = Model_Service::factory('news-topic')->getTotalSubscribersCount();
        $this->view->totalSubscribers = count(Model_Service::factory('news-topic')->getEmailSubscribersList());
        //$this->getHelper('AdminMultisite')->extendMassForm();
    }
    
    public function subscribersListAction()
    {
        $this->view->subscribers = Model_Service::factory('email-news-subscription')->paginatorGetAll(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
    }
    
    public function sendSubscribersAction()
    {
        $id = $this->getRequest()->getParam('id');
        $news = Model_Service::factory('news')->getComplex($id);
        
        $subscribedUsers = Model_Service::factory('news-topic')->getSubscribersList(5);
        foreach($subscribedUsers as $subscribedUser){
            $emailArray1[] = $subscribedUser['email'];
        }
        $subscribedEmails = Model_Service::factory('news-topic')->getEmailSubscribersList();
        foreach($subscribedEmails as $subscribedEmail){
            $emailArray2[] = $subscribedEmail['ens_email'];
        }
        $emailsArray = array_merge($emailArray1, $emailArray2);
        $emailsArray = array_unique($emailsArray);
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
        $config = Zend_Registry::get('config');
        $viewData = array(
            'siteName' => $config->www->siteName,
            'siteHref' => $config->www->siteHref,
            'news' => $news,
        );
        $subj = $view->partial('admin-news/mail-news-to-subscribers-subj.phtml', $viewData);
        $emailService = Model_Service::factory('email-queue');
        foreach ($emailsArray as $singleEmail) {
            $viewData['unsubscribeLink'] = $view->stdUrl(array('reset' => true, 'email'=>$singleEmail), 'unsubscribe', 'user', 'shop');
            $viewData['email'] = $singleEmail;
            $bodyHtml = $view->partial('admin-news/mail-news-to-subscribers-html.phtml', $viewData);
            $bodyText = $view->partial('admin-news/mail-news-to-subscribers-text.phtml', $viewData);
            $email = $emailService->create();
            $email->from = $config->email->support;
            $email->from_name = $config->email->supportName;
            $email->to = $singleEmail;
            $email->to_name = $singleEmail;
            $email->subject = $subj;
            $email->body_text = $bodyText;
            $email->body_html = $bodyHtml;
            $emailService->addToQueue($email);
        }
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'));
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
            Model_Service::factory('news')->activateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were activated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not activated'));
        }
    }

    protected function _mass_deactivate(array $massCheck)
    {
        try {
            Model_Service::factory('news')->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deactivated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deactivated'));
        }
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            Model_Service::factory('news')->deleteByIdArray($massCheck);
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
            $service = Model_Service::factory('news');
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
            $service = Model_Service::factory('news');
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
        Model_Service::factory('news')->changeSorting($this->_getParam('id'), $this->_getParam('position'));
        $url = $this->view->stdUrl(array('id'=>NULL, 'position'=>NULL), 'index');
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }


    /**
     * edit page
     */
    public function editAction()
    {
        $service = Model_Service::factory('news');
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);
        if ( ! $this->getHelper('AdminMultisite')->isAllowedMultisite()) {
            Model_Service::factory('news-topic')->getHelper('Multisite')->setCurrentSiteId($siteId);
        }                 
        
        // init form
        $form = new Form_AdminNewsEdit;
        //$this->getHelper('AdminMultisite')->extendEditForm($form);
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('News edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'))->sendResponse();
            echo 'redirected??';/*exit;*/
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
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('News saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'))->sendResponse();
        return;

    }


    /**
     * delete page
     */
    public function deleteAction()
    {
        $service = Model_Service::factory('news');
        $news = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    if ( ! $this->getHelper('AdminMultisite')->isAllowedMultisite()) {
                        $service->getHelper('Multisite')->unlinkFromSite($news->id, array($this->getHelper('AdminMultisite')->getSiteId()));
                    }
                    else {
                        $service->delete($news);
                    }                                        
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('News "%1$s" deleted', $news->title));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete page "%1$s" (%2$s)', $news->title, $news->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'index'));
        }
        else {
            $this->view->news = $news;
            $this->view->form = $form;
        }
    }


}

