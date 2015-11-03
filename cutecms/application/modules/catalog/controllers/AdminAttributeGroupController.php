<?php

class Catalog_AdminAttributeGroupController extends Zend_Controller_Action
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
        $this->view->groups = Model_Service::factory('catalog/attribute-group')->paginatorGetAllByParent(
            $this->_getParam('parent'),
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        $this->view->gotoHtml = $this->view->partial('admin-attribute-group/goto-form.phtml', array(
                                    'options'=>$this->_prepareGotoOptions(),
                                    'parent' =>$this->_getParam('parent'),
                                ));
        $this->view->parent = $this->_getParam('parent');
    }

    /**
     * @param array pages to append to top menu
     */
    protected function _prepareMenuTop(array $append = NULL)
    {
        $parents = Model_Service::factory('catalog/attribute-group')->getParentsOf($this->_getParam('parent'), TRUE);
        $topMenuPages = array(
            array(
                'label' => $this->view->translate('Наборы аттрибутов'),
                'route' => 'default',
                'action' => 'index',
                'controller' => 'admin-attribute-group',
                'module' => 'catalog',
            )
        );
        foreach ($parents as $parent) {
            $topMenuPages[]= array(
                'label' => $parent->name,
                'route' => 'default',
                'action' => 'index',
                'controller' => 'admin-attribute-group',
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
        $list = Model_Service::factory('catalog/attribute-group')->getFullTreeAsSelectOptions(NULL, TRUE);
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
            Model_Service::factory('catalog/attribute-group')->activateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were activated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not activated'));
        }
    }

    protected function _mass_deactivate(array $massCheck)
    {
        try {
            Model_Service::factory('catalog/attribute-group')->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deactivated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deactivated'));
        }
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            Model_Service::factory('catalog/attribute-group')->deleteByIdArray($massCheck);
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
        Model_Service::factory('catalog/attribute-group')->changeSorting($this->_getParam('id'), $this->_getParam('position'));
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
        $service = Model_Service::factory('catalog/attribute-group');
        // init form
        $form = new Catalog_Form_AdminAttributeGroupEdit;
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Group edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($cancelUrl);
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
        $service = Model_Service::factory('catalog/attribute-group');
        $group = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->delete($group);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Group "%1$s" deleted', $group->name));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete group "%1$s"', $group->name));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        }
        else {
            $this->view->group = $group;
            $this->view->form = $form;
        }
    }

}

