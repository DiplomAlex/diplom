<?php

class AdminCurrencyController extends Zend_Controller_Action
{

    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }

    public function editAction()
    {
        $currencies = Model_Service::factory('config')->read('var/currency.xml', NULL, FALSE);
        // init form
        $form = new Form_AdminCurrencyEdit;
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('User edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array(), 'admin_index'));
        }
        if ( ! $this->getRequest()->isPost()) {
            $values = array();
            $values['rub_eur'] = $currencies->currency->eur->rate;
            $values['rub_usd'] = $currencies->currency->usd->rate;
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
        $currencies->currency->eur->rate = $values['rub_eur'];
        $currencies->currency->eur->rateNonCache = $values['rub_eur'];
        $currencies->currency->usd->rate = $values['rub_usd'];
        $currencies->currency->usd->rateNonCache = $values['rub_usd'];
        Model_Service::factory('config')->write($currencies, 'var/currency.xml');
        // add message to flash queue
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Currency saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array(), 'admin_index'));
        return;
        
    }
}

