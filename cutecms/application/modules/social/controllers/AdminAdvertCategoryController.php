<?php

class Social_AdminAdvertCategoryController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }

    /**
     * show list
     */
    public function indexAction()
    {
        $this->view->categories = Model_Service::factory('social/advert-category')->paginatorGetAll(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
    }


    /**
     * change object's sorting position in list
     */
    public function sortingAction()
    {
        Model_Service::factory('social/advert-category')->changeSorting($this->_getParam('id'), $this->_getParam('position'));
        $url = $this->view->stdUrl(array('id'=>NULL, 'position'=>NULL), 'index');
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }


    public function editAction()
    {
        $service = Model_Service::factory('social/advert-category');
        // init form
        $form = new Social_Form_AdminAdvertCategoryEdit;
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Category edition cancelled'));
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
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Category saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(NULL, 'index'));
        return;
    }

    public function deleteAction()
    {
        $service = Model_Service::factory('social/advert-category');
        $obj = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->delete($obj);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Category "%1$s" deleted', $obj->name));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete category "%1$s" (%2$s)', $obj->name, $obj->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL), 'index'));
        }
        else {
            $this->view->category = $obj;
            $this->view->form = $form;
        }
    }


}