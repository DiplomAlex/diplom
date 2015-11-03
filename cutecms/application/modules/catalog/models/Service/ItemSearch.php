<?php

class Catalog_Model_Service_ItemSearch extends Model_Service_Abstract
{

    /**
     * Default injections
     * 
     * @var array 
     */
    protected $_defaultInjections = array(
        'Model_Mapper_Interface' => 'Catalog_Model_Mapper_Db_ItemSearch'
    );

    /**
     * Get column list
     * 
     * @return array
     */
    public function getColumnList()
    {
        return $this->getMapper()->getColumnList();;
    }

    /**
     * Add new column
     * 
     * @param int $attrId
     */
    public function addColumn($attrId)
    {
        $this->getMapper()->addColumn($attrId);
    }

    /**
     * Delete column
     * 
     * @param int $attrId
     */
    public function deleteColumn($attrId)
    {
        $this->getMapper()->deleteColumn($attrId);
    }

    /**
     * Set value
     * 
     * @param int $itemId
     * @param int $attrId
     * @param mixed $value
     */
    public function setValue($itemId, $attrId, $value)
    {
        $this->getMapper()->setValue($itemId, $attrId, $value);
    }

    /**
     * Delete all values
     * 
     * @param int $itemId
     */
    public function deleteAllValues($itemId)
    {
        $this->getMapper()->deleteAllValues($itemId);
    }

}