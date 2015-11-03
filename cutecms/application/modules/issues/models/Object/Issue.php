<?php

class Issues_Model_Object_Issue extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'id',
            'status', 'status_history', 'status_history_serialized', 'reopened',
            'subject', 'brief', 'text',
            'date_added', 'date_changed', 'date_complete',
            'date_due', 'date_due_history', 'date_due_history_serialized',
            'adder_id', 'adder_name', 'adder_login',
            'changer_id', 'changer_name', 'changer_login', 'changer_comment',
            'comments', 'comments_count',
            'users', 'users_serialized',
            'isAllowedEdit', 'isAllowedChangeStatus', 'isAllowedChangeDateDue',
        ));
    }

    public function getStatus_history()
    {
        if ( ! is_array($this->_elements['status_history'])) {
            $this->_elements['status_history'] = unserialize($this->_elements['status_history_serialized']);
            if ( ! is_array($this->_elements['status_history'])) {
                $this->_elements['status_history'] = array();
            }
        }
        return $this->_elements['status_history'];
    }

    public function getStatus_history_serialized()
    {
        $this->_elements['status_history_serialized'] = serialize($this->_elements['status_history']);
        return $this->_elements['status_history_serialized'];
    }

    public function setStatus_history(array $history)
    {
        $this->_elements['status_history'] = $history;
        $this->_elements['status_history_serialized'] = serialize($this->_elements['status_history']);
        return $this;
    }


    public function getDate_due_history()
    {
        if ( ! is_array($this->_elements['date_due_history'])) {
            $this->_elements['date_due_history'] = unserialize($this->_elements['date_due_history_serialized']);
            if ( ! is_array($this->_elements['date_due_history'])) {
                $this->_elements['date_due_history'] = array();
            }
        }
        return $this->_elements['date_due_history'];
    }

    public function getDate_due_history_serialized()
    {
        $this->_elements['date_due_history_serialized'] = serialize($this->_elements['date_due_history']);
        return $this->_elements['date_due_history_serialized'];
    }

    public function setDate_due_history(array $history)
    {
        $this->_elements['date_due_history'] = $history;
        $this->_elements['date_due_history_serialized'] = serialize($this->_elements['date_due_history']);
        return $this;
    }


    public function getUsers()
    {
        if ( ! is_array($this->_elements['users'])) {
            $this->_elements['users'] = unserialize($this->_elements['users_serialized']);
            if ( ! is_array($this->_elements['users'])) {
                $this->_elements['users'] = array();
            }
        }
        return $this->_elements['users'];
    }

    public function getUsers_serialized()
    {
        $this->_elements['users_serialized'] = serialize($this->_elements['users']);
        return $this->_elements['users_serialized'];
    }

    public function setUsers(array $users)
    {
        $this->_elements['users'] = $users;
        $this->_elements['users_serialized'] = serialize($this->_elements['users']);
        return $this;
    }


    public function getIsAllowedEdit()
    {
        $userService = Model_Service::factory('user');
        $user = $userService->getCurrent();
        if (($user->id == $this->adder_id) OR ($userService->isAllowedByAcl($user, __CLASS__, 'foreign_update'))) {
            $result = TRUE;
        }
        else {
            $result = FALSE;
        }
        return $result;
    }

    public function getIsAllowedChangeStatus()
    {
        $userService = Model_Service::factory('user');
        $user = $userService->getCurrent();
        if (($user->id == $this->adder_id) OR ($userService->isAllowedByAcl($user, __CLASS__.'__status', 'foreign_update'))
            OR in_array($user->id, $this->users)) {
            $result = TRUE;
        }
        else {
            $result = FALSE;
        }
        return $result;
    }

    public function getIsAllowedChangeDateDue()
    {
        $userService = Model_Service::factory('user');
        $user = $userService->getCurrent();
        if (($user->id == $this->adder_id) OR ($userService->isAllowedByAcl($user, __CLASS__.'__date_due', 'update_foreign'))
            OR in_array($user->id, $this->users)) {
            $result = TRUE;
        }
        else {
            $result = FALSE;
        }
        return $result;
    }

}