<?php

class Issues_Model_Object_Observer_Issue extends App_Event_Observer
{

    protected $_service = NULL;

    public function __construct()
    {
        $this->_service = Model_Service::factory('issues/issue');
    }

    public function onBeforeSave()
    {
        $issue = $this->getData(0);

        if ($issue->id > 0) {
            $oldIssue = $this->_service->getComplex($issue->id);
        }
        $this->_rulesSubject($issue, $oldIssue);
        $this->_rulesStatus ($issue, $oldIssue);
        $this->_rulesDateDue($issue, $oldIssue);
    }

    /**
     * subject rules
     */
    protected function _rulesSubject(Model_Object_Interface $issue, Model_Object_Interface $oldIssue = NULL)
    {
        $this->_service->addSubject($issue->subject);
    }

    /**
     * status rules
     */
    protected function _rulesStatus(Model_Object_Interface $issue, Model_Object_Interface $oldIssue = NULL)
    {
        $statuses = Zend_Registry::get('issues_config')->statuses;
        if ($issue->id > 0) {
            if ($issue->status != $oldIssue->status) {
                $issue = $this->_service->addStatusHistory($issue, $oldIssue->status_history, $issue->status, $issue->changer_comment);
                if ($issue->status == $statuses->complete) {
                    $issue->date_complete = date('Y-m-d H:i:s');
                }
                if ($oldIssue->status == $statuses->closed) {
                    $issue->reopened = TRUE;
                }
                if ($issue->status == $statuses->closed) {
                    $issue->reopened = FALSE;
                }
            }
        }
        else {
            $issue->status = $statuses->created;
            $issue = $this->_service->addStatusHistory($issue, array(), $issue->status, $issue->changer_comment);
        }
    }

    /**
     * date due rules
     */
    protected function _rulesDateDue(Model_Object_Interface $issue, Model_Object_Interface $oldIssue = NULL)
    {
        if ($issue->id > 0) {
            if ($issue->date_due != $oldIssue->date_due) {
                $issue = $this->_service->addDateDueHistory($issue, $oldIssue->date_due_history, $issue->date_due, $issue->changer_comment);
            }
        }
        else {
            $issue = $this->_service->addDateDueHistory($issue, array(), $issue->date_due, $issue->changer_comment);
        }
    }

}

