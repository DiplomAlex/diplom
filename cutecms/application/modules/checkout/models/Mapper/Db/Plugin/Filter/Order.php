<?php

class Checkout_Model_Mapper_Db_Plugin_Filter_Order extends Model_Mapper_Db_Plugin_Filter_Abstract
{

    protected function _filter_client(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $arr = explode('|', $value);
        $where = array();
        $field = $this->_getFieldName($filterName);
        foreach ($arr as $v) {
            $where []= '(client.user_login LIKE \'%'.$v.'%\' OR client.user_name LIKE \'%'.$v.'%\')';
        }
        $select->where(implode(' OR ', $where));
        return $select;
    }

    protected function _filter_number(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $select->where('order_id = ?', $value);
        return $select;
    }

}
