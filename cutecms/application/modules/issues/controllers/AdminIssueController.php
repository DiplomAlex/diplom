<?php

class Issues_AdminIssueController extends Zend_Controller_Action
{

    protected $_injector = NULL;
    protected $_session = NULL;


    protected $_defaultInjections = array(
        'Form_AdminTopics'  => 'Issues_Form_AdminIssueTopics',
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

    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace(__CLASS__);
        }
        if ( ! is_array($this->_session->topics)) {
            $this->_session->topics = Model_Service::factory('issues/issue')->getSubjects(TRUE);
        }
        return $this->_session;
    }

    public function init()
    {
        $this->injectDefaults();
        App_Event::factory('Issues_AdminController__init', array($this))->dispatch();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
            $this->getHelper('ViewRenderer')->setNoRender();
        }
    }


    public function topicAction()
    {
        if ( ! $cancelUrl = $this->getHelper('ReturnUrl')->get()) {
            /* what was the real reason of redirecting to admin_index ? */
            /*$cancelUrl = $this->view->url(array(), 'admin_index');*/
            $cancelUrl = $this->view->stdUrl(NULL, 'index', 'index', 'issues');
        }
        if ( ! $submitUrl = $this->getHelper('ReturnUrl')->get()) {
            /*$submitUrl = $this->view->url(array(), 'admin_index');*/
            $submitUrl = $this->view->stdUrl(NULL, 'index', 'index', 'issues');
        }
        // init form
        $form = $this->getInjector()->getObject('Form_AdminTopics');
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($cancelUrl);
        }
        if ( ! $this->getRequest()->isPost()) {
        // if it was just called (via get)
            $this->_session()->topics = NULL;
            $form->populate(array());
            $this->view->form = $form;
        }
        else {
        // if the form was posted
            Model_Service::factory('issues/issue')->setAllSubjects($this->_session()->topics);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Topics saved'));
            //redirect
            $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        }
        return;
    }


    /**
     * get one or all topics :
     * if $_REQUEST['rowId'] isset then recieves one topic by its hash otherwise - returns all
     */
    public function ajaxGetTopicAction()
    {
        $rowId = $this->_getParam('rowId');
        if (empty($rowId)) {
            $rows = array();
            foreach ($this->_session()->topics as $key=>$topic) {
                $rows[] = array(
                    'id' => $key,
                    'cell' => array($topic),
                );
            }
            $answer = array(
                'page' => '1',
                'total' => count($this->_session()->topics),
                'rows' => $rows,
            );
        }
        else {
            $rowId = substr($rowId, 3); /* remove "row" */
            $answer = array();
            foreach ($this->_session()->topics as $key=>$topic) {
                if ($key == $rowId) {
                    $answer = array('text'=>$topic);
                    break;
                }
            }
        }
        echo Zend_Json::encode($answer);
    }


    public function ajaxNewTopicAction()
    {
        $this->_session()->topics[uniqid()] = $this->_getParam('text');
        echo 'ok';
    }



    public function ajaxEditTopicAction()
    {
        $rowId = substr($this->_getParam('rowId'), 3);
        $duplicate = FALSE;
        foreach ($this->_session()->topics as $key=>$topic) {
            if ($key == $rowId) {
                $this->_session()->topics[$key] = $this->_getParam('text');
                break;
            }
        }
        echo 'ok';
    }

    public function ajaxDeleteTopicAction()
    {
        $rows = $this->_getParam('rows');
        foreach ($rows as $row) {
            $rowId = substr($row, 3); /*remove the prefix "row" from id*/
            foreach ($this->_session()->topics as $key=>$topic) {
                if ($key == $rowId) {
                    unset($this->_session()->topics[$key]);
                }
            }
        }
        echo 'ok';
    }


}