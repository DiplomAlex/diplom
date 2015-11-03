<?php

class Issues_Model_Mapper_Db_Issue extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Collection_Interface' => 'Issues_Model_Collection_Issue',
        'Model_Collection_Comment'   => 'Issues_Model_Collection_Comment',
        'Model_Object_Interface'     => 'Issues_Model_Object_Issue',
        'Model_Object_Comment'       => 'Issues_Model_Object_Comment',
        'Model_Db_Table_Interface'   => 'Issues_Model_Db_Table_Issues',
        'Issues_Model_Db_Table_IssueUsers',
        'Model_Db_Table_Users',
    );

    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        $userService = Model_Service::factory('user');
        $user = $userService->getCurrent();
        $select->joinLeft(
                    array('adder'=>'user'),
                    'adder.user_id = issue_adder_id',
                    array('issue_adder_login'=>'adder.user_login', 'issue_adder_name'=>'adder.user_name')
                 )
               ->joinLeft(
                    array('changer'=>'user'),
                    'changer.user_id = issue_changer_id',
                    array('issue_changer_login'=>'changer.user_login', 'issue_changer_name'=>'changer.user_name')
                 )
               ->joinLeft(
                    array('binded'=>'issue_user'),
                    'issue_id = iu_issue_id AND iu_user_id = '.$user->id,
                    array('current_in_binded'=>'COUNT(iu_id)')
                 )
               ->order('issue_date_added DESC')
               ->group('issue_id')
                 ;
        if ( ! $userService->isAllowedByAcl($user, 'Issues_Model_Object_Issue', 'foreign_read')) {
            $select->having('issue_adder_id = '. (int) $user->id.' OR current_in_binded > 0');
        }

        return $select;
    }

    protected function _preSaveComplex(Model_Object_Interface $obj, array $values)
    {
        $users = array();
        foreach ($values['users'] as $user) {
            $users []= $user;
        }
        $obj->users = $users;
        return $obj;
    }


    /**
     * @param Model_Object_Interface
     * @param array
     * @return Model_Object_Interface
     */
    protected function _postSaveComplex(Model_Object_Interface $obj, array $values)
    {
        $this->getTable('issues/issue-users')->delete(array('iu_issue_id = ?'=>$obj->id));
        foreach ($values['users'] as $user) {
            $this->getTable('issues/issue-users')->insert(array(
                'iu_user_id' => $user,
                'iu_issue_id' => $obj->id,
            ));
        }
        return $obj;
    }
    

    /**
     * @param int - issue id
     * @return array(user_id => user_name, ...)
     */
    public function fetchIssueUsersList($id, $asArray = FALSE)
    {
        $iuTable = $this->getTable('issues/issue-users');
        $userTable = $this->getTable('users');
        $cols = $userTable->info('cols');
        $prefixLen = strlen($userTable->getColumnPrefix()) + 1; /* +1 for "_" */
        $columns = array();
        foreach ($cols as $col) {
            $columns[substr($col, $prefixLen)] = $col;
        }
        $select = $iuTable->select()->from($iuTable->getTableName(), array())
                                  ->setIntegrityCheck(FALSE)
                                  ->joinLeft($userTable->getTableName(), 'user_id = iu_user_id',  $columns)
                                  ->where('iu_issue_id = ?', $id)
                                  ->order('user_name ASC');
        $rows = $select->query()->fetchAll();
        $list = array();
        foreach ($rows as $row) {
            if ($asArray === TRUE) {
                $list[$row['id']] = $row;
            }
            else {
                $list[$row['id']] = $row['name'];
            }
        }
        return $list;
    }


}