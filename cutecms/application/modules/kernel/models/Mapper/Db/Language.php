<?php

class Model_Mapper_Db_Language extends Model_Mapper_Db_Abstract implements Model_Mapper_Language
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Model_Db_Table_Languages',
        'Model_Object_Interface' => 'Model_Object_Language',
        'Model_Collection_Interface' => 'Model_Collection_Language',
        'Model_Mapper_Db_Plugin_Sorting',
    );
        

    public function init()
    {
        $this->addPlugin('Sorting', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Sorting'));
    }
    
    /**
     * @param Model_Object_Interface
     * @param array
     * @return Model_Object_Interface
     */
    protected function _preSaveComplex(Model_Object_Interface $obj, array $values)
    {
        if ( (int) $obj->is_default) {
            $this->updateAllDefault(0);
        }
        else if ( ! $this->countDefaults($obj->id)) {
            $obj->is_default = TRUE;
        }
        return $obj;
    }
    
    public function updateAllDefault($isDefault)
    {
        $this->getTable()->update(array('language_is_default' => (int) $isDefault));
        return $this;
    }
    
    public function countDefaults($exceptId = NULL)
    {
        $table = $this->getTable();
        $select = $table->select()->from('language', array('cnt'=>'COUNT(DISTINCT language_id)'))
                        ->where('language_is_default > 0');
        if ($exceptId) {
            $select->where('language_id <> ?', $exceptId);
        }
        $row = $select->query()->fetch();
        $result = $row['cnt'];
        return $result;
    }
    
    
	public function fetchDefault()
	{
		if ( ! $rows = $this->fetchComplex(array('language_is_default = ?' => 1))) {
			throw new Model_Mapper_Db_Exception('Error in db - one languages OUGHT to be default');
		}
		$object = $rows->current();
		return $object;
	}

    public function fetchAllActive()
    {
        return $this->fetchComplex(array('language_status = ?' => 1));
    }

    /**
     * @param string[2] $code2
     * @return Model_Object_Interface
     */
    public function fetchOneByCode2($code2)
    {
        if (empty($code2)) {
            $this->_throwException('code should be set');
        }
        $object = $this->fetchComplex(array('language_code2 = ?'=>strtolower($code2)))->current();
        return $object;
    }
    
    
}