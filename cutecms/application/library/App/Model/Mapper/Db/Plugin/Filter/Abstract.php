<?php

class Model_Mapper_Db_Plugin_Filter_Abstract extends Model_Mapper_Db_Plugin_Abstract implements Model_Mapper_Db_Plugin_Filter_Interface
{

    /**
     * @var array
     */
    protected $_filters = NULL;

    /**
     * array (filter => array(getParamName1, getParamName2, ...))
     * @var array
     */
    protected $_filterSubfields = NULL;


    protected $_hasTable = FALSE;

    /**
     * @param array filters to set
     */
    public function __construct(array $filters, array $filterSubfields = NULL)
    {
        $this->_filters = $filters;
        $this->_filterSubfields = $filterSubfields;
    }

    /**
     * perform filters on pagination
     * @param Zend_Db_Table_Select
     */
    public function onPagination(Zend_Db_Table_Select $select)
    {
        if ($this->_filters === NULL) {
            throw new Model_Mapper_Db_Plugin_Exception('filters for '.get_class($this->getMapper()).' were not set');
        }

        /**
         * @todo replace call to front_controller
         */
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        /**
         * if filter is acceptable then
         * if exists method with filter name prepended with '_' - call it,
         * otherwise call _default method
         */
        foreach ($this->_filters as $filter) {
            $methodName = '_'.$filter;
            if ( ! method_exists($this, $methodName)) {
                $methodName = '_default';
            }
            if ($this->_isAcceptable($filter, $params)) {
                if (isset($params[$filter])) {
                    if (is_string($params[$filter])) {
                        $value = trim($params[$filter]);
                    }
                    else {
                        $value = $params[$filter];
                    }
                }
                else {
                    $value = NULL;
                }
                call_user_func_array(array($this, $methodName), array($select, $filter, $value, $params));
            }
        }
    }

    /**
     * checks if filter or its specific fields present in GET
     * @param string filter_name
     * @param array all GET params
     * @return bool
     */
    protected function _isAcceptable($filter, $params)
    {
        /**
         * filter is acceptable when it is in parameters
         * and its value is not empty (for numbers it also can be 0)
         */

        if (isset($params[$filter])) {
            if (is_numeric($params[$filter])) {
                $this->_setGlobalParam($filter, $params[$filter]);
                return TRUE;
            }
            if (is_string($params[$filter])) {
                $params[$filter] = trim($params[$filter]);
            }
            if ( ! empty($params[$filter])) {
                $this->_setGlobalParam($filter, $params[$filter]);
                return TRUE;
            }
        }

        /**
         * filter is also acceptable when one of it subfields is in parameters and that value
         * is also not empty (or any numeric)
         */
        if (isset($this->_filterSubfields[$filter]) AND is_array($this->_filterSubfields[$filter])) {
            foreach ($this->_filterSubfields[$filter] as $field) {
                if (isset($params[$field])) {
                    if (is_numeric($params[$field])) {
                        $this->_setGlobalParam($field, $params[$field]);
                        return TRUE;
                    }
                    if (is_string($params[$field])) {
                        $params[$field] = trim($params[$field]);
                    }
                    if ( ! empty($params[$field])) {
                        $this->_setGlobalParam($field, $params[$field]);
                        return TRUE;
                    }
                }
            }
        }

        return FALSE;
    }

    /**
     * filter_name for table user
     * should became user_name
     *
     * @param string
     * @return string
     */
    protected function _getFieldName($filterName)
    {
        $fieldName = $this->getMapper()->getTable()->getColumnPrefix().'_'.substr($filterName, strlen('filter_'));
        return $fieldName;
    }

    /**
     * add parameter to global url parameters so it will be automatically added by view->url()
     *
     * @param string param name
     * @param string param value
     * @return $this
     */
    protected function _setGlobalParam($param, $value)
    {
        Zend_Controller_Front::getInstance()->getRouter()->setGlobalParam($param, $value);
        return $this;
    }


    /**
     * checks for full compliance of value in filter with value in db row
     *
     * @param Zend_Db_Table_Select - select
     * @param string - filterName
     * @param string - filterValue
     * @param array - all parameters (may be some times we'll need it)
     *
     */
    protected function _default(Zend_Db_Table_Select $select, $filterName, $filterValue, array $params = NULL)
    {
        $select -> where($this->_getFieldName($filterName) . ' = ?', $filterValue);
        return $select;
    }

}