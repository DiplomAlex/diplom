<?php

class Model_Db_Table_Resources extends Model_Db_Table_Abstract
{

    protected $_name = 'resource';
    protected $_columnPrefix = 'rc';

    public function getPKName()
    {
        return 'rc_id';
    }

}