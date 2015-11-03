<?php

class Model_Mapper_Db_Plugin_Filter_User extends Model_Mapper_Db_Plugin_Filter_Abstract
{

    protected function _filter_role_id(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $select->where($this->_getFieldName($filterName).'=?', $value);
        return $select;
    }

    protected function _filter_name(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $select->where($this->_getFieldName($filterName).' LIKE ?', '%'.$value.'%');
        return $select;
    }

    protected function _filter_email(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $select->where($this->_getFieldName($filterName).' LIKE ?', '%'.$value.'%');
        return $select;
    }


    protected function _filter_login(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $arr = explode('|', $value);
        $where = array();
        $field = $this->_getFieldName($filterName);
        foreach ($arr as $v) {
            $where []= $field . " LIKE ". "'%".$v."%'";
        }
        $select->where(implode(' OR ', $where));
        return $select;
    }



    /**
     * in specificFields it should be presented as array('filter_date_added'=>array('filter_date_added_from', 'filter_date_added_to'))
     */
    protected function _filter_date_added(Zend_Db_Table_Select $select, $filterName, $value, $params)
    {
        $from = trim($params['filter_date_added_from']);
        $to = trim($params['filter_date_added_to']);
        if ($from) {
            $select->where($this->_getFieldName($filterName).' >= ?', $from);
        }
        if ($to) {
            $select->where($this->_getFieldName($filterName).' <= ?', $to);
        }
        return $select;
    }

}