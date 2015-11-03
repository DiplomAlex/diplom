<?php

class Catalog_AdminItem_IndexController extends Zend_Controller_Action
{
    
    protected $_defaultInjections = array(
        'Form_Create' => 'Catalog_Form_AdminItem_Create',
        'Form_Edit'   => 'Catalog_Form_AdminItem_Edit', 
        'Form_Delete' => 'App_Form_Question',
    );
    
        
    protected $_session = NULL;
    
    protected $_itemService = NULL;
    protected $_categoryService = NULL;
    protected $_userService = NULL;
    protected $_siteService = NULL;
    
    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace(__CLASS__);
        }
        return $this->_session;
    }
    
    protected function _getItemService()
    {
        if ($this->_itemService === NULL) {
            $this->_itemService = Model_Service::factory('catalog/item');
        }
        return $this->_itemService;
    }

    protected function _getCategoryService()
    {
        if ($this->_categoryService === NULL) {
            $this->_categoryService = Model_Service::factory('catalog/category');
        }
        return $this->_categoryService;
    }

    protected function _getUserService()
    {
        if ($this->_userService === NULL) {
            $this->_userService = Model_Service::factory('user');
        }
        return $this->_userService;
    }


    protected function _getSiteService()
    {
        if ($this->_siteService === NULL) {
            $this->_siteService = Model_Service::factory('site');
        }
        return $this->_siteService;
    }
    
       
    
    public function init()
    {
        $this->_helper->AdminItem($this->_defaultInjections);        
        $this->_helper->AdminGallery($this->_getItemService()->getHelper('Gallery'));
    }
    
    
    public function indexAction()
    {
        $filter = new Catalog_Form_AdminItemFilter;
        $filter->populate($this->getRequest()->getParams());
        $this->view->filter = $this->view->renderForm($filter, 'admin-item/index/filter.phtml');
        
        $this->getHelper('ReturnUrl')->remember();
        $category = $this->_getParam('category');
        
        $service = $this->_getItemService();
        
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);
        
        $this->_getCategoryService()->getHelper('Multisite')->setCurrentSiteId($siteId);
        
        $this->getFrontController()->getRouter()->setGlobalParam('category', $category);
        
        $this->view->items = $service->paginatorGetAllByCategory(
            $category,
            $this->getHelper('RowsPerPage')->saveValue($this->_getParam('rows_per_page'))->getValue(),
            $this->_getParam('page'),
            TRUE,
			FALSE
        );
        
        foreach ($this->view->items as $item){
            $galleries[$item->id] = $service->getHelper('Gallery')->getLinkedToContent($item['id']);
        }
        $this->view->galleries = $galleries; 
        
        $this->view->gotoHtml = $this->view->partial('admin-item/index/goto-form.phtml', array(
                                    'options'=>$this->_prepareGotoOptions(),
                                    'category' =>$this->_getParam('category'),
                                ));
        $this->view->category = $category;        
        $this->view->currentSiteId = $siteId;
        
        //$this->getHelper('AdminMultisite')->extendMassForm();
    }

    /**
     * prepare options for "goto" select
     * @return array
     */
    protected function _prepareGotoOptions()
    {
        $list = $this->_getCategoryService()->getFullTreeAsSelectOptions(NULL, TRUE);
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
            $this->_getItemService()->activateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were activated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not activated'));
        }
    }

    protected function _mass_deactivate(array $massCheck)
    {
        try {
            $this->_getItemService()->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deactivated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deactivated'));
        }
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            $this->_getItemService()->deleteByIdArray($massCheck);
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
            $service = $this->_getItemService();
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
            $service = $this->_getItemService();
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
        $this->_getItemService()->changeSorting($this->_getParam('id'), $this->_getParam('position'));
        $url = $this->view->stdUrl(array('id'=>NULL, 'position'=>NULL), 'index');
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }
    
    
    /**
     * create new item - form for requesting item type settings (configurable or not, downloadable or not, )
     */
    public function createAction()
    {
        $form = $this->_helper->Injector()->getObject('Form_Create');
        $this->view->form = $form;
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
        $service = $this->_getItemService();
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId); 
        
        // init form
        $form = $this->_helper->Injector()->getObject('Form_Edit');
        $this->_extendEditForm($form); 
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Item edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($cancelUrl);
        }
        if ( ! $this->getRequest()->isPost()) {
        // if it was just called (via get)
            $this->getHelper('AdminGallery')->clearLinked();        
            $id = (int) $this->_getParam('id');
            if ($id) {
                //load $values from db
                $values = $service->getEditFormValues($id);
                $values['id'] = (int) $values['id'];
                $sites = $service->getHelper('Multisite')->getLinkedSites($values['id']);
                $this->getHelper('AdminMultisite')->setSiteIdsValueToArray($values, $sites);
                $this->getHelper('AdminGallery')->loadLinked($id);
            }
            else {
                //init $values
                $values = $service->createDefault()->toArray();
                $values['is_configurable'] = (int) $this->_getParam('is_configurable');
                $values['is_downloadable'] = (int) $this->_getParam('is_downloadable');
                if ($this->_getParam('category')) {
                    $category = $this->_getCategoryService()->getComplex($this->_getParam('category'));
                    $values['item_categories'] = array($category->id => $category->id);
                }
                $this->getHelper('AdminMultisite')->addDefaultSiteIdsValueToArray($values);
            }
            App_Event::factory('Catalog_AdminItem_IndexController__editAction__onLoad', array($this, $values))->dispatch();
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
            return;
        }
        else {
        // if the form was posted
            $values = $this->getRequest()->getParams();
            $this->getHelper('AdminMultisite')->checkSiteIds($values);
            $values = App_Event::factory('Catalog_AdminItem_IndexController__editAction__onSave', array($this, $values))->dispatch()->getResponse();
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
        $item = $service->saveFromValues($values, TRUE);
        $this->getHelper('AdminGallery')->saveLinked($item);
        App_Event::factory('Catalog_AdminItem_IndexController__editAction__onAfterSuccessSave', array($this, $values, $item))->dispatch()->getResponse();
        // add message to flash queue
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Item saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        return;
    }

    protected function _extendEditForm(Zend_Form $form)
    {
        $this->getHelper('AdminGallery')->extendEditForm($form);
        $form->addDisplayGroup(array('gallery'), 'tab_gallery', array('label'=>$this->view->translate('Gallery')));
        /*
        $helperMulti = $this->getHelper('AdminMultisite');
        $helperMulti->extendEditForm($form);
        if ($helperMulti->isAllowedMultisite()) {
            $form->addDisplayGroup(array($helperMulti->getFormElementNameSiteIds()), 'tab_sites', array('label'=>$helperMulti->getFormElementLabelSiteIds()));
        }
         */
    }
    
    
    /**
     * delete page
     */
    public function deleteAction()
    {
        if ( ! $submitUrl = $this->getHelper('ReturnUrl')->get()) {
            $submitUrl = $this->view->stdUrl(array('id'=>NULL), 'index');
        }
        $service = $this->_getItemService();
        $item = $service->getComplex($this->_getParam('id'));
        $form = $this->_helper->Injector()->getObject('Form_Delete');
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    if ( ! $this->getHelper('AdminMultisite')->isAllowedMultisite()) {
                        $service->getHelper('Multisite')->unlinkFromSite($item->id, array($this->getHelper('AdminMultisite')->getSiteId()));
                    }
                    else {
                        $service->delete($item);
                    }
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Item "%1$s" deleted', $item->name));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete item "%1$s" (%2$s)', $item->name, $item->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        }
        else {
            $this->view->item = $item;
            $this->view->form = $form;
        }
    }


    public function importAction()
    {
        $this->view->layout()->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        $this->_getItemService()->processImport();
    }


    public function ajaxGetRemainsAction()
    {
        $this->view->layout()->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();

        if ($sku = $this->_getParam('sku')) {
            $rowsPerPage = $this->_getParam('rows', 20);
            $page = $this->_getParam('page', 1);
            $remains = Model_Service::factory('api/remain')->paginatorRemainsBySku($sku, $rowsPerPage, $page);
            $rows = array();
            foreach ($remains as $row) {
                $rows[] = array(
                    'id'   => $row->id,
                    'cell' => array(
                        $row->sku,
                        $row->code,
                        $row->material,
                        $row->probe,
                        $row->size,
                        $row->characteristics,
                        $row->weight,
                        $row->price,
                        $row->in_stock
                    )
                );
            }
            $answer = array(
                'page'  => (int)$page,
                'total' => $remains->count(),
                'rows'  => $rows,
            );
        } else {
            $answer = array();
        }

        $this->getHelper('Json')->sendJson($answer);
    }
        
    
}