<?php

class AdminCommentController extends Zend_Controller_Action
{
    
    protected $_defaultInjections = array(
        'Form_Edit' => 'Form_AdminCommentEdit',
        'Form_Delete' => 'App_Form_Question',
    );
    
    protected $_service = NULL; 
    
    /**
     * (non-PHPdoc)
     * @see application/library/Zend/Controller/Zend_Controller_Action::init()
     */
    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
        $this->_helper->Injector($this->_defaultInjections);
        $this->_helper->AdminComment();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('ViewRenderer')->setNoRender();
            $this->view->layout()->disableLayout();
        }
    }
    
    /**
     * main service getter
     * @return Model_Service_Interface
     */
    public function getService()
    {
        if ($this->_service === NULL) {
            $this->_service = Model_Service::factory('comment');
        }
        return $this->_service;
    }
    
    /**
     * main service setter
     * @param Model_Service_Interface $service
     * @return Zend_Controller_Action $this
     */
    public function setService(Model_Service_Interface $service) 
    {
        $this->_service = $service;
        return $this;
    }
        
    /**
     * sets helper's service
     * @param Model_Service_Interface $service
     * @return Zend_Controller_Action $this
     */
    public function setHelperService(Model_Service_Interface $service) 
    {
        $this->getHelper('AdminComment')->setServiceHelper($service->getHelper('Comment'));
        return $this;
    }
    
    /**
     * factory service by contentType
     * @param string $contentType
     */
    public function getServiceByType($contentType)
    {
        
        $service = Model_Service::factory($contentType);        
        return $service;
    }
    
    
    public function indexAction()
    {
        $commentId = $this->_getParam('id');
        $contentType = base64_decode($this->_getParam('contentType')); 
        $contentId = $this->_getParam('contentId');
        $contentService = $this->getServiceByType($contentType);
        $this->setHelperService($contentService);
        $this->view->content = $contentService->getComplex($contentId);
        $this->view->contentType = $contentType;
        $this->view->contentId = $contentId;
        $this->view->comments = $this->getHelper('AdminComment')->getLinked($contentId);
        if ($commentId) {
            $expanded = $this->getService()->getParentIds($commentId) + array($commentId);
            $this->view->currentId = $commentId;
        }
        else {
            $expanded = array();
        }
        $this->view->expanded = $expanded;
    }
    
    public function indexTopNewAction()
    {
        $contentId = $this->_getParam('contentId');
        $contentType = $this->_getParam('contentType');
        $page = $this->_getParam('page');
        if ($contentType) {
            // get comments of content
            $contentType = base64_decode($contentType);
            $contentService = $this->getServiceByType($contentType);
            $this->setHelperService($contentService);
            $this->getHelper('AdminComment')->setSortingMode(array('new'));
            $this->view->comments = $this->getHelper('AdminComment')->paginatorGetLinked(
                $contentId, 
                $this->getHelper('RowsPerPage')->saveValue()->getValue(),
                $page
            );
            $this->view->contentType = $contentType;
            $this->view->content = $contentService->getComplex($contentId);
        }
        else {
            // get all comments 
            $this->getService()->getMapper()->setSortingOrder(array('new'));
            $this->view->comments = $this->getService()->paginatorGetAll(
                $this->getHelper('RowsPerPage')->saveValue()->getValue(),
                $page
            );
        }
    }
    
    
    public function editAction()
    {
        $service = $this->getService();
        $form = $this->getHelper('Injector')->getInjector()->getObject('Form_Edit');
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Comment edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'));
        }
        else if ( ! $this->getRequest()->isPost()) {
        // if it was just called (via get)
            $values = array();
            if ($id = $this->_getParam('id')) {
                //load $values from db
                $values = $service->getEditFormValues($id);
            }
            else {
                //create values 
                $values = $service->create()->toArray();
                $values['parent_id'] = $this->_getParam('parentId');
                $values['content_type'] = base64_decode($this->_getParam('contentType'));
                $values['content_id'] = $this->_getParam('contentId');
            }
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
        }
        else {
        // if the form was posted
            $values = $this->getRequest()->getParams();
            // validate it
            if ($form->isValid($values)) {
                // save
                $service->saveFromValues($values);
                // add message to flash queue
                $this->getHelper('flashMessenger')->addMessage($this->view->translate('Comment saved'));
                //redirect
                $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'));
            }
            else {
                // show form again filled with recieved values
                $form->populate($values);
                $this->view->form = $form;
                $this->view->values = $values;
                $this->getHelper('flashMessenger')->addMessage($this->view->translate('Form validation failed'));
            }
        }
    }
    
    public function deleteAction()
    {
        $service = $this->getService();
        $comment = $service->getComplex($this->_getParam('id'));
        $form = $this->getHelper('Injector')->getInjector()->getObject('Form_Delete');
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->delete($comment);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Comment "%1$s" deleted', $comment->id));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete comment "%1$s"', $comment->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'index'));
        }
        else {
            $this->view->comment = $comment;
            $this->view->form = $form;
        }        
    }
    
    public function approveAction()
    {
        
    }
    
}

 