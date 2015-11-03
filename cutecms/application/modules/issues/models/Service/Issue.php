<?php

class Issues_Model_Service_Issue extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Mapper_Interface' => 'Issues_Model_Mapper_Db_Issue',
        'Model_Object_Interface' => 'Issues_Model_Object_Issue',
        'Model_Object_Comment'   => 'Issues_Model_Object_Comment',
        'Model_Mapper_Comment'   => 'Issues_Model_Mapper_Db_Comment',
    );

    public function getSubjects($uniqKey = FALSE)
    {
        $cacheKey = 'issue_subjects';
        $cache = Zend_Registry::get('Zend_Cache');
        if ( ! $subjs = $cache->load($cacheKey)) {
            $conf = Model_Service::factory('config')->read('var/issue.xml', 'subjects');
            $subjs = array();
            foreach ($conf->subject as $subj) {
                if ($uniqKey === TRUE) {
                    $subjs[uniqid()] = $subj;
                }
                else {
                    $subjs[] = $subj;
                }
            }
            asort($subjs);
            $cache->save($subjs, $cacheKey);
        }
        return $subjs;
    }

    public function addSubject($subj)
    {
        $arr = $this->getSubjects();

        $normas = array();
        foreach ($arr as $s) {
            $normas[] = App_Utf8::normalize($s);
        }
        if (( ! empty($subj)) AND ( ! in_array(App_Utf8::normalize($subj), $normas))) {
            $arr []= $subj;
            asort($arr);
            Zend_Registry::get('Zend_Cache')->save($arr, 'issue_subjects');
            $conf = new Zend_Config(array('subjects'=>array('subject'=>$arr)), TRUE);
            Model_Service::factory('config')->write('var/issue.xml', $conf);
        }
        return $this;
    }


    public function setAllSubjects(array $subjs)
    {
        $arr = array();
        foreach ($subjs as $subj) {
            $arr[] = $subj;
        }
        Zend_Registry::get('Zend_Cache')->save($arr, 'issue_subjects');
        $conf = new Zend_Config(array('subjects'=>array('subject'=>$arr)), TRUE);
        Model_Service::factory('config')->write('var/issue.xml', $conf);
        return $this;
    }


    public function getStatusById($id, $translate = TRUE)
    {
        $list = $this->getStatusList($translate);
        return $list[$id];
    }

    public function getStatusList($translate = TRUE)
    {
        $sts = Zend_Registry::get('issues_config')->statuses;
        $list = array();
        foreach($sts as $status=>$id) {
            if ($translate) {
                $list[$id] = $this->getTranslator()->_('issueStatus.'.$status);
            }
            else {
                $list[$id] = $status;
            }
        }
        return $list;
    }

    public function addStatusHistory(Model_Object_Interface $issue, array $history, $status, $comment = NULL)
    {
        $user = Model_Service::factory('user')->getCurrent();
        $history[]= array(
            'status' => $status,
            'date' => date('Y-m-d H:i:s'),
            'user' => array('id'=>$user->id, 'login'=>$user->login, 'name'=>$user->name),
            'comment' => $comment,
        );
        $issue->status_history = $history;
        return $issue;
    }

    public function addDateDueHistory(Model_Object_Interface $issue, array $history, $dateDue, $comment = NULL)
    {
        $user = Model_Service::factory('user')->getCurrent();
        $history[] = array(
            'date_due' => $dateDue,
            'date' => date('Y-m-d H:i:s'),
            'user' => array('id'=>$user->id, 'login'=>$user->login, 'name'=>$user->name),
            'comment' => $comment,
        );
        $issue->date_due_history = $history;
        return $issue;
    }


    /**
     * get objects fields values for edit form
     * @return array
     */
    public function getEditFormValues($id)
    {
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        $values['users'] = array_keys($this->getIssueUsersList($id));
        return $values;
    }

    /**
     * @param int - issue id
     * @param bool
     * @return array(user_id => user_name, ...) or array(user_id => array(user_name, user_email, ...))
     */
    public function getIssueUsersList($id, $asArray = FALSE)
    {
        return $this->getMapper()->fetchIssueUsersList($id, $asArray);
    }


}