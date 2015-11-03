<?php

class Social_AdminAdvertController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }

    public function automatesAction()
    {
        $service = Model_Service::factory('social/advert');
        // init form
        $form = new Social_Form_AdminAdvertAutomates;
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Automates list edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('reset'=>TRUE)));
        }
        if ( ! $this->getRequest()->isPost()) {
        // if it was just called (via get)
            $values = array('list'=>$service->getAutomateModelsList(FALSE));
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
        $service->saveAutomateModelsList($values['list']);
        // add message to flash queue
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Models list saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('reset'=>TRUE)));
        return;

    }

}