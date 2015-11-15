<?php

class Model_Mapper_Db_ArduinoLine extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Model_Db_Table_ArduinoLine',
        'Model_Collection_Interface' => 'Model_Collection_ArduinoLine',
        'Model_Object_Interface' => 'Model_Object_ArduinoLine',
    );

    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        $select
            ->order('al_date_added ASC');

        return parent::_onFetchComplex($select);
    }

    public function fetchAllSortedWithoutCurrent($id)
    {
        $rows = $this->fetchComplex(array('al_sketch_id <> ?' => $id));

        return $rows;
    }

    public function deleteBySketchId($sketchId)
    {
        $row = $this->fetchComplex(array('al_sketch_id = ?' => $sketchId));

        if ($row->count()) {
            $this->delete($row->current());
        }
    }
}