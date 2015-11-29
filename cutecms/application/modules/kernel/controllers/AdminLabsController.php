<?php

class AdminLabsController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('AdminController__init', array($this, 'admin'))->dispatch();
    }

    public function indexAction()
    {
        $this->view->users = Model_Service::factory('user')->paginatorGetUsersOfLabs(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
    }

    public function labsAction()
    {
        $this->view->user = Model_Service::factory('user')->get($this->_getParam('id'));
        $this->view->labs = Model_Service::factory('arduino')->paginatorGetByUser(
            $this->_getParam('id'),
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
    }

    public function labAction()
    {
        $this->view->user = Model_Service::factory('user')->get($this->_getParam('user_id'));
        $this->view->lab = Model_Service::factory('arduino')->get($this->_getParam('id'));
        $this->view->inOut = Model_Service::factory('arduinoIO')->getByLab($this->_getParam('id'));
    }
}