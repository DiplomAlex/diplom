<?php

class Issues_IndexController extends Zend_Controller_Action
{

    protected $_injector = NULL;

    protected $_defaultInjections = array(
        'Form_Create'        => 'Issues_Form_IssueCreate',
        'Form_Edit'          => 'Issues_Form_IssueEdit',
        'Form_Delete'        => 'App_Form_Question',
        'Form_ChangeStatus'  => 'Issues_Form_IssueChangeStatus',
        'Form_ChangeDateDue' => 'Issues_Form_IssueChangeDateDue',
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
        App_Event::factory('Issue_Controller__init', array($this))->dispatch();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
            $this->getHelper('ViewRenderer')->setNoRender();
        }
    }

    public function indexAction()
    {
        $this->view->issues = Model_Service::factory('issues/issue')->paginatorGetAll(
                $this->getHelper('RowsPerPage')->saveValue()->getValue(),
                $this->_getParam('page')
        );
        $this->view->changeStatusForm = $this->getInjector()->getObject('Form_ChangeStatus');
    }

    public function issueAction()
    {
        $service = Model_Service::factory('issues/issue');
        $issueId = $this->_getParam('id');
        $issue = $service->getComplex($issueId);

        $indexUrl = $this->view->stdUrl(NULL, 'index', 'index', 'issues');
        $this->view->menu()->setTopMenuPages(array(
            array(
                'label' => $this->view->translate('Цели и задачи'),
                'uri' => $indexUrl,
                'pages' => array(
                    array(
                        'label' => $this->view->translate('Задача #%1$s: %2$s', $issue->id, $issue->subject),
                        'uri' => $this->view->url(),
                        'active' => TRUE,
                    )
                ),
            ),
        ));

        $this->getHelper('RowsPerPage')->saveValue();

        $issue->users = $service->getIssueUsersList($issueId);
        $this->view->issue = $issue;
        $this->view->changeStatusForm = $this->getInjector()->getObject('Form_ChangeStatus');
    }

    public function ajaxGetHistoryAction()
    {
        $service = Model_Service::factory('issues/issue');
        $issueId = $this->_getParam('id');
        $issue = $service->getComplex($issueId);
        echo Zend_Json::encode(array(
            'status' => $this->view->issue_StatusHistoryBox($issue->status_history),
            'dateDue' => $this->view->issue_DateDueHistoryBox($issue->date_due_history),
        ));
    }

    public function ajaxChangeStatusAction()
    {
        $issueId = $this->_getParam('issueId');
        $status = $this->_getParam('status');
        $dateDue = $this->_getParam('date_due');
        $comment = $this->_getParam('comment');
        $service = Model_Service::factory('issues/issue');
        $issue = $service->getComplex($issueId);
        $issue->status = $status;
        $issue->date_due = $dateDue;
        $issue->changer_comment = $comment;
        $service->saveComplex($issue);
        $result = array(
            'issueId' => $issueId,
            'status' => array(
                'value' => $status,
                'text' => $this->view->issue_Status($status),
            ),
            'date_due' => array(
                'value' => $dateDue,
                'text' => $this->view->formatDate($dateDue),
            ),
        );
        echo Zend_Json::encode($result);
    }

    public function editAction()
    {
        if ( ! $cancelUrl = $this->getHelper('ReturnUrl')->get()) {
            $cancelUrl = $this->view->stdUrl(NULL, 'index', 'index', 'issues');
        }
        if ( ! $submitUrl = $this->getHelper('ReturnUrl')->get()) {
            $submitUrl = $this->view->stdUrl(NULL, 'index', 'index', 'issues');
        }

        $this->view->menu()->setTopMenuPages(array(
            array(
                'label' => $this->view->translate('Цели и задачи'),
                'uri' => $cancelUrl,
                'pages' => array(
                    array(
                        'label' => $this->view->translate('Редактирование задачи'),
                        'uri' => $this->view->url(),
                        'active' => TRUE,
                    )
                ),
            ),
        ));

        $service = Model_Service::factory('issues/issue');
        $this->view->subjectsList = $service->getSubjects();
        // init form
        if ( (int) $this->_getParam('id')) {
            $form = $this->getInjector()->getObject('Form_Edit');
            $this->getHelper('ViewRenderer')->setScriptAction('edit');
        }
        else {
            $form = $this->getInjector()->getObject('Form_Create');
            $this->getHelper('ViewRenderer')->setScriptAction('create');
        }
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Issue edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($cancelUrl);
        }
        if ( ! $this->getRequest()->isPost()) {
        // if it was just called (via get)
            /*$values = array();*/
            if ( (int) $this->_getParam('id')) {
                //load $values from model
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
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Issue saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        return;
    }

    public function deleteAction()
    {
        $submitUrl = $this->view->stdUrl(array('reset'=>TRUE), 'index', 'index', 'issues');
        $service = Model_Service::factory('issues/issue');
        $issue = $service->getComplex($this->_getParam('id'));
        $form = $this->getInjector()->getObject('Form_Delete');
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    $service->delete($issue);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Issue "%1$s" deleted', $issue->id));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete issue: %1', $issue->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        }
        else {
            $this->view->issue = $issue;
            $this->view->form = $form;
        }
    }

    public function ajaxGetSubjectsAction()
    {
        $subjs = Model_Service::factory('issues/issue')->getSubjects();
        echo Zend_Json::encode($subjs);
    }

}
