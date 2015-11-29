<?php

class Model_Mapper_Db_Arduino extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Model_Db_Table_Arduino',
        'Model_Collection_Interface' => 'Model_Collection_Arduino',
        'Model_Object_Interface' => 'Model_Object_Arduino',
    );

    public function paginatorFetchByUsers($user, $rowsPerPage, $page)
    {
        $query = $this->fetchComplex(array('a_adder_id = ?' => $user), false);

        return $this->paginator($query, $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
    }
}