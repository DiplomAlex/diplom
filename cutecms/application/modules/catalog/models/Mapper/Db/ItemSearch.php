<?php

class Catalog_Model_Mapper_Db_ItemSearch extends Model_Mapper_Db_Abstract
{

    /**
     * Default injections
     * 
     * @var array 
     */
    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Catalog_Model_Db_Table_ItemSearch'
    );

    /**
     * Get column list
     * 
     * @return array
     */
    public function getColumnList()
    {
        $columns = array();
        $table   = $this->getTable();
        $result  = $table->getAdapter()->getConnection()->query('SHOW COLUMNS FROM `' . $table->getTableName() . '`')->fetchAll();

        foreach ($result as $row) {
            $columns[] = $row['Field'];
        }

        return $columns;
    }

    /**
     * Add new column
     * 
     * @param int $attrId
     */
    public function addColumn($attrId)
    {
        $table  = $this->getTable();
        $column = $table->getColumnPrefix() . $table->getPrefixSeparator() . $attrId;

        if (!in_array($column, $this->getColumnList())) {
            $table->getAdapter()->getConnection()->query('ALTER TABLE `' . $table->getTableName() . '` ADD `' 
                . $column . '` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;'
                . 'ALTER TABLE `' . $table->getTableName() . '` ADD INDEX `' . $column . '` (`' . $column . '`);');
        }
    }

    /**
     * Delete column
     * 
     * @param int $attrId
     */
    public function deleteColumn($attrId)
    {
        $table  = $this->getTable();
        $column = $table->getColumnPrefix() . $table->getPrefixSeparator() . $attrId;

        if (in_array($column, $this->getColumnList())) {
            $table->getAdapter()->getConnection()->query('ALTER TABLE `' . $table->getTableName() . '` DROP INDEX`' 
                . $column . '`;' . 'ALTER TABLE `' . $table->getTableName() . '` DROP `' . $column . '`;');
        }
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
        $table  = $this->getTable();
        $value  = $table->getAdapter()->getConnection()->quote($value);
        $prefix = $table->getColumnPrefix() . $table->getPrefixSeparator();

        if (!$table->find($itemId)->count()) {
            $table->getAdapter()->getConnection()->query('INSERT INTO `' . $table->getTableName() 
                . '` (`' . $prefix . 'id`) VALUES (' . (int) $itemId . ');');
        }
        $table->getAdapter()->getConnection()->query('UPDATE `' . $table->getTableName() . '` SET `' 
            . $prefix . $attrId . '`=' . $value . ' WHERE `' . $prefix . 'id`="' . (int) $itemId . '";');
    }

    /**
     * Delete all values
     * 
     * @param int $itemId
     */
    public function deleteAllValues($itemId)
    {
        $table  = $this->getTable();
        $prefix = $table->getColumnPrefix() . $table->getPrefixSeparator();

        if ($table->find($itemId)->count()) {
            $table->getAdapter()->getConnection()->query('DELETE FROM `' . $table->getTableName() . '` WHERE `' 
            . $prefix . 'id`="' . (int) $itemId . '";');
        }
    }

}