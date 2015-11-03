<?php

class Model_Mapper_Db_Plugin_Filter_Document extends Model_Mapper_Db_Plugin_Filter_Abstract
{

    protected function _filter_doctype_id(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $select->where($this->_getFieldName($filterName).'=?', $value);
        return $select;
    }

}