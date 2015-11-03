<?php
class Social_AdvertController extends Zend_Controller_Action
{


    public function init()
    {
        App_Event::factory('Social_Controller__init', array($this));
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        }
    }


    public function indexAction()
    {
        $this->view->adverts = Model_Service::factory('social/advert')->paginatorGetAll(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
        $this->view->user = Model_Service::factory('user')->getCurrent();
    }

    public function editAction()
    {
        $service = Model_Service::factory('social/advert');
        // init form
        $form = new Social_Form_AdvertEdit;
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Advert edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'));
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
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Advert saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'));
        return;
    }

    public function deleteAction()
    {
        $service = Model_Service::factory('social/advert');
        $obj = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->delete($obj);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Advert "%1$s" deleted', $this->view->advert_Title($obj)));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete advert "%1$s" (%2$s)', $this->view->advert_Title($obj), $obj->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'index'));
        }
        else {
            $this->view->advert = $obj;
            $this->view->form = $form;
        }
    }

    public function ajaxDeactivateAction()
    {
        $id = $this->_getParam('id');
        if ($this->view->advert = Model_Service::factory('social/advert')->deactivateById($id)) {
            $this->view->success  = TRUE;
        }
        else {
            $this->view->success  = FALSE;
        }
    }


}
