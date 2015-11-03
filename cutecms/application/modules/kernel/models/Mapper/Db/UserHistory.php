<?php

class Model_Mapper_Db_UserHistory extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections= array(
        'Model_Db_Table_Interface' => 'Model_Db_Table_UserHistory',
        'Model_Object_Interface' => 'Model_Object_UserHistory',
        'Model_Collection_Interface' => 'Model_Collection_UserHistory',
    );

    public function fetchHistoryByUserId($id)
    {
        return $this->fetchComplex(array('uh_user_id = ?' => $id), false)
            ->order('uh_user_date_changed DESC')
            ->query()->fetchAll();
    }

}
