<?php

class Catalog_AdminCategoryController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }
    
    /**
     * show list of objects
     */
    public function indexAction()
    {
        $this->_prepareMenuTop();

        $this->getHelper('ReturnUrl')->remember();
        
        $service = Model_Service::factory('catalog/category');
        
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);
        $parentId = $this->_getParam('parent', NULL);
        if ( (int) $siteId AND ! (int) $parentId) {
            $this->view->asTree = TRUE;
            $this->view->categories = $service->paginatorGetFullTree(
                $this->getHelper('RowsPerPage')->saveValue()->getValue(),
                $this->_getParam('page')
            );            
        }
        else {
            $this->view->categories = $service->paginatorGetAllByParent(
                $parentId,
                $this->getHelper('RowsPerPage')->saveValue()->getValue(),
                $this->_getParam('page')
            );
        }
        $this->view->currentSiteId = $siteId;
        $this->view->gotoHtml = $this->view->partial('admin-category/goto-form.phtml', array(
                                    'options'=>$this->_prepareGotoOptions(),
                                    'parent' =>$this->_getParam('parent'),
                                ));
        $this->view->parent = $this->_getParam('parent');

        /*
        if ($this->getHelper('AdminMultisite')->isAllowedMultisite()) {
            $this->view->adminMassForm()->setMassActions(array('activate', 'deactivate', 'delete', 'linkToSite', 'unlinkFromSite'));
            $this->view->massActionsConfig = array('script' => 'admin-category/mass/actions.phtml');
        }
         *
         */
    }


    /**
     * @param array pages to append to top menu
     */
    protected function _prepareMenuTop(array $append = NULL)
    {
        $parents = Model_Service::factory('catalog/category')->getParentsOf($this->_getParam('parent'), TRUE);
        $topMenuPages = array(
            array(
                'label' => $this->view->translate('Категории'),
                'route' => 'default',
                'action' => 'index',
                'controller' => 'admin-category',
                'module' => 'catalog',
            )
        );
        foreach ($parents as $parent) {
            $topMenuPages[]= array(
                'label' => $parent->name,
                'route' => 'default',
                'action' => 'index',
                'controller' => 'admin-category',
                'module' => 'catalog',
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
        $list = Model_Service::factory('catalog/category')->getFullTreeAsSelectOptions(NULL, TRUE);
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
            Model_Service::factory('catalog/category')->activateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were activated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not activated'));
        }
    }

    protected function _mass_deactivate(array $massCheck)
    {
        try {
            Model_Service::factory('catalog/category')->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deactivated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deactivated'));
        }
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            Model_Service::factory('catalog/category')->deleteByIdArray($massCheck);
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
            $catService = Model_Service::factory('catalog/category');
            $catIds = $catService->getChildrenIdsByRootIdArray($massCheck, $siteIds);
            $catService->getHelper('Multisite')->linkToSiteByIdArray($catIds, $siteIds);
            $itemService = Model_Service::factory('catalog/item');
            $itemIds = $itemService->getAllIdsByCategories($catIds);
            $itemService->getHelper('Multisite')->linkToSiteByIdArray($itemIds, $siteIds);
            
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
            $catService = Model_Service::factory('catalog/category');
            $catIds = $catService->getChildrenIdsByRootIdArray($massCheck, $siteIds);
            $catService->getHelper('Multisite')->unlinkFromSiteByIdArray($catIds, $siteIds);
            $itemService = Model_Service::factory('catalog/item');
            $itemIds = $itemService->getAllIdsByCategories($catIds);
            $itemService->getHelper('Multisite')->unlinkFromSiteByIdArray($itemIds, $siteIds);
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
        Model_Service::factory('catalog/category')->changeSorting($this->_getParam('id'), $this->_getParam('position'));
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
        $service = Model_Service::factory('catalog/category');
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);
        
        // init form
        $form = new Catalog_Form_AdminCategoryEdit;
        //$this->getHelper('AdminMultisite')->extendEditForm($form);
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Category edition cancelled'));
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
                $values['status'] = 1;
                $parentId = $this->_getParam('parent');
                if ($parentId) {
                    $values['parent_id'] = $service->get($parentId)->tree_id;
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
        $service->saveFromValues($values);
        // add message to flash queue
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Category saved'));
        //redirect
        try {
            $parentId = $service->getByTreeId($values['parent_id'])->id;
        }
        catch (Model_Service_Exception $e) {
            $parentId = NULL;
        }
        $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        return;

    }


    /**
     * delete page
     */
    public function deleteAction()
    {
        $menuPages = $this->_prepareMenuTop(array(array(
                                               'label' => $this->view->translate('Удаление'),
                                               'uri' => $this->view->url(),
                                               'active' => TRUE,
                                           )));

        if ( ! $submitUrl = $this->getHelper('ReturnUrl')->get()) {
            $submitUrl = $this->view->stdUrl(array('id'=>NULL), 'index');
        }
        $service = Model_Service::factory('catalog/category');
        $category = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    if ( ! $this->getHelper('AdminMultisite')->isAllowedMultisite()) {
                        $service->getHelper('Multisite')->unlinkFromSite($category->id, array($this->getHelper('AdminMultisite')->getSiteId()));
                    }
                    else {
                        $service->delete($category);
                    }                                        
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Category "%1$s" deleted', $category->name));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete category "%1$s" (%2$s)', $category->name, $category->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        }
        else {
            $this->view->category = $category;
            $this->view->form = $form;
        }
    }
    
    
    public function importAction()
    {
        $this->view->layout()->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();        
        Model_Service::factory('catalog/category')->processImport();
    }

}

