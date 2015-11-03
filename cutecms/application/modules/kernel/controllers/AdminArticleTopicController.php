<?php

class AdminArticleTopicController extends Zend_Controller_Action
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
        $this->_prepareMenuTop();
        $this->getHelper('ReturnUrl')->remember();
        $service = Model_Service::factory('article-topic');
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);        
        $this->view->topics = $service->paginatorGetAllByParent(
            $this->_getParam('parent'),
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        $this->view->gotoHtml = $this->view->partial('admin-article-topic/goto-form.phtml', array(
                                    'options'=>$this->_prepareGotoOptions(),
                                    'parent' =>$this->_getParam('parent'),
                                ));
        $this->view->parent = $this->_getParam('parent');
        $this->getHelper('AdminMultisite')->extendMassForm();
    }

    /**
     * @param array pages to append to top menu
     */
    protected function _prepareMenuTop(array $append = NULL)
    {
        $parents = Model_Service::factory('article-topic')->getParentsOf($this->_getParam('parent'), TRUE);
        $topMenuPages = array(
            array(
                'label' => $this->view->translate('Темы статей'),
                'route' => 'default',
                'action' => 'index',
                'controller' => 'admin-article-topic',
                'module' => 'kernel',
            )
        );
        foreach ($parents as $parent) {
            $topMenuPages[]= array(
                'label' => $parent->name,
                'route' => 'default',
                'action' => 'index',
                'controller' => 'admin-article-topic',
                'module' => 'kernel',
                'params' => array(
                    'parent' => $parent->id,
                ),
            );
        }
        if ($append !== NULL) {
            $topMenuPages = array_merge($topMenuPages, $append);
        }
        $topMenuPages = array_reverse($topMenuPages);
        $result = array();
        foreach ($topMenuPages as $page) {
            $newResult = $page;
            if ( ! empty($result)) {
                $newResult['pages'] = array($result);
            }
            $result = $newResult;
        }
        $this->view->menu()->setTopMenuPages(array($result));
        return $result;
    }


    /**
     * prepare options for "goto" select
     * @return array
     */
    protected function _prepareGotoOptions()
    {
        $list = Model_Service::factory('article-topic')->getFullTreeAsSelectOptions(NULL, TRUE);
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
            Model_Service::factory('article-topic')->activateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were activated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not activated'));
        }
    }

    protected function _mass_deactivate(array $massCheck)
    {
        try {
            Model_Service::factory('article-topic')->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deactivated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deactivated'));
        }
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            Model_Service::factory('article-topic')->deleteByIdArray($massCheck);
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
            $service = Model_Service::factory('article-topic');
            $topicIds = $service->getChildrenIdsByRootIdArray($massCheck);
            $service->getHelper('Multisite')->linkToSiteByIdArray($topicIds, $siteIds);
            $artService = Model_Service::factory('article');
            $artIds = $artService->getAllIdsByTopics($topicIds);
            $artService->getHelper('Multisite')->linkToSiteByIdArray($artIds, $siteIds);            
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
            $service = Model_Service::factory('article-topic');
            $topicIds = $service->getChildrenIdsByRootIdArray($massCheck);
            $service->getHelper('Multisite')->unlinkFromSiteByIdArray($topicIds, $siteIds);
            $artService = Model_Service::factory('article');
            $artIds = $artService->getAllIdsByTopics($topicIds);
            $artService->getHelper('Multisite')->unlinkFromSiteByIdArray($artIds, $siteIds);            
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
        Model_Service::factory('article-topic')->changeSorting($this->_getParam('id'), $this->_getParam('position'));
        $url = $this->view->stdUrl(array('id'=>NULL, 'position'=>NULL), 'index');
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }


    /**
     * edit page
     */
    public function editAction()
    {
        $menuPages = $this->_prepareMenuTop(array(array(
                                               'label' => $this->view->translate('Редактирование'),
                                               'uri' => $this->view->url(),
                                               'active' => TRUE,
                                           )));
        if ( ! $cancelUrl = $this->getHelper('ReturnUrl')->get()) {
            $cancelUrl = $this->view->stdUrl(array('id'=>NULL), 'index');
        }
        if ( ! $submitUrl = $this->getHelper('ReturnUrl')->get()) {
            $submitUrl = $this->view->stdUrl(array('id'=>NULL), 'index');
        }
        $service = Model_Service::factory('article-topic');
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);
        
        // init form
        $form = new Form_AdminArticleTopicEdit;
        $this->getHelper('AdminMultisite')->extendEditForm($form);
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Topic edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($cancelUrl);
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
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Group saved'));
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
        $service = Model_Service::factory('article-topic');
        $topic = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    if ( ! $this->getHelper('AdminMultisite')->isAllowedMultisite()) {
                        $service->getHelper('Multisite')->unlinkFromSite($topic->id, array($this->getHelper('AdminMultisite')->getSiteId()));
                    }
                    else {
                        $service->delete($topic);
                    }                                        
                                        
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Topic "%1$s" deleted', $topic->name));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete topic "%1$s"', $topic->name));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        }
        else {
            $this->view->topic = $topic;
            $this->view->form = $form;
        }
    }

}

