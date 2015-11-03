<?php

class App_Paginator_Adapter_ModelMapperDb extends Zend_Paginator_Adapter_DbSelect
{

    /**
     * @var Zend_Db_Select
     */
    protected $_select = NULL;

    /**
     * @var Model_Mapper_Db_Interface
     */
    protected $_mapper = NULL;

    /**
     * @var int
     */
    protected $_style = NULL;

    public function __construct(array $data)
    {
        $this->_select = $data['select'];
        /*$this->_totalCount = $data['total'];*/
        $this->_mapper = $data['mapper'];
        $this->_style = (int) $data['style'];
    }

    /**
     * Returns a collection of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $data = $this->_select->limit($itemCountPerPage, $offset)->query()->fetchAll();
        switch ($this->_style) {
            case Model_Object_Interface::STYLE_SIMPLE:
                $collection = $this->_mapper->makeSimpleCollection($data);
                break;
            case Model_Object_Interface::STYLE_COMPLEX:
                $collection = $this->_mapper->makeComplexCollection($data);
                break;
            case Model_Object_Interface::STYLE_CUSTOM:
                $collection = $this->_mapper->makeCustomCollection($data);
                break;
        }
        return $collection;
    }
    
}