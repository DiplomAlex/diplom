<?php

class AdminUserController extends Zend_Controller_Action
{

    protected $_injector = NULL;

    protected $_defaultInjections = array(
        'Form_AdminUserFilter',
        'Form_AdminUserEdit',
        'Form_AdminUserDelete' => 'App_Form_Question',
    );


    /**
     * @return App_DIContainer
     */
    public function getInjector()
    {
        if ($this->_injector === NULL) {
            $this->_injector = new App_DIContainer($this);
        }
        return $this->_injector;
    }


    /**
     * @return $this
     */
    public function injectDefaults()
    {
        foreach ($this->_defaultInjections as $interface=>$class) {
            $this->getInjector()->inject($interface, $class);
        }
    }


    public function init()
    {
        $this->injectDefaults();
        try {
            App_Event::factory('AdminController__init', array($this))->dispatch();
        }
        catch (Zend_Acl_Exception $e) {
            if ($this->getRequest()->getAction() == 'index') {
                $this->getHelper('Redirector')->gotoUrlAndExit($this->view->url(array('login' => Zend_Auth::getInstance()->getIdentity()->id), 'user_profile'));
            }
            else {
                throw new Zend_Acl_Exception($e->getMessage());
            }
        }
    }

    /**
     * show list of users
     */
    public function indexAction()
    {
        $filter = $this->getInjector()->getObject('Form_AdminUserFilter');
        $filter->populate($this->getRequest()->getParams());
        $this->view->filter = $this->view->renderForm($filter, 'admin-user/filter.phtml');
        $bindingUserId = $this->_getParam('binded_id');
        if ( ! empty($bindingUserId)) {
            $user = Model_Service::factory('user')->getComplex($bindingUserId);
        }
        if (empty($bindingUserId) OR empty($user->binding)) {
            $this->view->users = Model_Service::factory('user')->paginatorGetAll(
                $this->getHelper('RowsPerPage')->saveValue()->getValue(),
                $this->_getParam('page')
            );
        }
        else {
            $this->view->users = Model_Service::factory('user')->paginatorGetBinded(
                $user,
                $this->getHelper('RowsPerPage')->saveValue()->getValue(),
                $this->_getParam('page')
            );
        }
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
            Model_Service::factory('user')->activateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were activated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not activated'));
        }
    }

    protected function _mass_deactivate(array $massCheck)
    {
        try {
            Model_Service::factory('user')->deactivateByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deactivated'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deactivated'));
        }
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            Model_Service::factory('user')->deleteByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deleted'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deleted'));
        }
    }

    protected function _mass_bind(array $massCheck)
    {
        try {
            Model_Service::factory('user')->bindUsersByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were binded'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not binded'));
        }
    }

    protected function _mass_unbind(array $massCheck)
    {
        try {
            Model_Service::factory('user')->unbindUsersByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were unbinded'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not unbinded'));
        }
    }


    /**
     * change object's sorting position in list
     */
    public function sortingAction()
    {
        Model_Service::factory('user')->changeSorting($this->_getParam('id'), $this->_getParam('position'));
        $url = $this->view->stdUrl(array('id'=>NULL, 'position'=>NULL), 'index');
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }


    /**
     * edit user
     */
    public function editAction()
    {
        $service = Model_Service::factory('user');
        // init form
        $form = $this->getInjector()->getObject('Form_AdminUserEdit');

        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('User edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(null, 'index'));
        }

        if (!$this->getRequest()->isPost()) {
            // if it was just called (via get)
            $values = array();

            if ((int)$this->_getParam('id')) {
                //load $values from model
                $values = $service->getEditFormValues($this->_getParam('id'));
            } else {
                //init $values
                $values = $service->create()->toArray();
                $values['guid'] = App_Uuid::get();
            }

            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;

            return;
        } else {
            // if the form was posted
            $values = $this->getRequest()->getParams();
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
        }

        // validate it
        if (!$form->isValid($values)) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Form validation failed'));

            return;
        }

        $values['export'] = 1;
        // save
        $service->saveFromValues($values);
        // add message to flash queue
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('User saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(null, 'index'));

        return;

    }


    /**
     * delete user
     */
    public function deleteAction()
    {
        $service = Model_Service::factory('user');
        $user = $service->getComplex($this->_getParam('id'));

        $form = $this->getInjector()->getObject('Form_AdminUserDelete');
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->delete($user);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('User "%1$s" deleted', $user->login));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete user "%1$s" (%2$s)', $user->name, $user->login));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'index'));
        }
        else {
            $this->view->user = $user;
            $this->view->form = $form;
        }
    }

    public function historyAction()
    {
        $this->view->history = Model_Service::factory('user-history')->getHistoryByUserId($this->_getParam('id'));
    }

}

