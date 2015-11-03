<?php

class Model_Mapper_Db_EmailQueue extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Model_Db_Table_EmailQueue',
        'Model_Object_Interface' => 'Model_Object_EmailQueue',
        'Model_Collection_Interface' => 'Model_Collection_EmailQueue',
    );


    /**
     * fetch several emails from queue top
     * @param int
     * @param bool
     * @return Model_Collection_Interface
     */
    public function fetchTop($limit, $fetch = TRUE)
    {
        $select = $this->fetchComplex(NULL, FALSE)->limit($limit)->order('email_date_added ASC');
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }

}

