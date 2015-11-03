<?php

class Model_Mapper_Db_Plugin_Description extends Model_Mapper_Db_Plugin_Abstract
{

	/**
	 * @var string
	 */
	protected $_refColumn = NULL;

	/**
	 * description fields
	 * @var array
	 */
	protected $_descFields = array(
		'title',
		'brief',
		'full',
		'html_title',
		'meta_keywords',
		'meta_description',
	);

    protected $_preparedFields = NULL;
    protected $_preparedDescFields = NULL;

    /**
	 * @var Model_Object_Language_Interface
	 */
	protected $_language = NULL;

	/**
	 * @var Model_Collection_Language_Interface
	 */
	protected $_languages = NULL;


	/**
	 * @param array  - config of format as in setConfig
	 */
	public function __construct(array $config = NULL)
	{
		if ($config !== NULL)
		{
			$this->setConfig($config);
		}
	}

	/**
	 * @return Model_Object_Language_Interface
	 */
	public function getCurrentLanguage()
	{
        if ($this->_language === NULL) {
            $this->_throwException('language was not set but is requested (usually init method of service initializes it)');
        }
		return $this->_language;
	}

	/**
	 * @param Model_Object_Language_Interface
	 * @return $this
	 */
	public function setCurrentLanguage(Model_Object_Language_Interface $lang)
	{
		$this->_language = $lang;
		return $this;
	}

	/**
	 * @return Model_Collection_Language_Interface
	 */
	public function getLanguages()
	{
		return $this->_languages;
	}

	/**
	 * @param Model_Collection_Language_Interface
	 * @return $this
	 */
	public function setLanguages(Model_Collection_Language_Interface $langs)
	{
		$this->_languages = $langs;
		return $this;
	}

	/**
	 * compose key for flat array when fetching or saving descriptions
	 * @param int
	 * @return string
	 */
	protected function _getFlatArrayKey($langId)
	{
		if ( ! $langId) {
			throw new Model_Mapper_Db_Exception(get_class($this).':langId should be set');
		}
		return 'description_language_'.$langId;
	}

	/**
	 * setter for _refColumn
	 * @param string
	 * @return $this
	 */
	public function setRefColumn($rc)
	{
		$this->_refColumn = $rc;
		return $this;
	}

	/**
	 * gets column name of reference to main table in description table
	 * @return string
	 */
	protected function _getRefColumn()
	{
		if ($this->_refColumn === NULL) {
			$this->_refColumn = strtolower($this->_getMapper()->getModelName()) . '_id';
		}
		return $this->_refColumn;
	}


	/**
	 * get all descriptions of one object
	 * @param int object's id
	 * @return array()
	 */
	public function fetchDescriptions($id, $returnFlatArray = TRUE)
	{
		if ( ! $id) {
			throw new Model_Mapper_Db_Exception('id should be set');
		}
		$table = $this->_table;

		$select = $table->select()
					   ->from(
							$table->getTableName(),
							array_values($this->_preparedFields)
					   )
					   ->where($this->_preparedFields[$this->_getRefColumn()].' = ?', $id)
					   ;
		$descs = $select->query()->fetchAll();

        $result = array();
		if ($returnFlatArray === TRUE) {
			foreach ($descs as $desc) {
				$key = $this->_getFlatArrayKey($desc[$this->_preparedFields['language_id']]);
				foreach ($this->_descFields as $field) {
					$result[$key.'_'.$field] = $desc[$this->_preparedFields[$field]];
				}
			}
		}
		else {
			foreach ($descs as $desc) {
				$key = $desc[$this->_preparedFields['language_id']];
				$result[$key] = array();
				foreach ($this->_descFields as $field) {
					$result[$key][$field] = $desc[$this->_preparedFields[$field]];
				}
			}
		}
		return $result;
	}

	/**
	 * save all descriptions of one object
	 * @param int object's id
	 *
	 */
	public function saveDescriptions($id, $values, $inputFlatArray = TRUE)
	{
		if ( ! $id) {
			throw new Model_Mapper_Db_Exception('id should be set');
		}
		$table = $this->_table;

		$descs = $this->fetchDescriptions($id, FALSE);

		foreach ($this->getLanguages() as $lang) {
			$descArray = array();
			foreach ($this->_descFields as $field) {
				if ($inputFlatArray === TRUE) {
                    if (isset($values[$this->_getFlatArrayKey($lang->id).'_'.$field])) {
					   $descArray[$this->_preparedFields[$field]] = $values[$this->_getFlatArrayKey($lang->id).'_'.$field];
                    }
                    else {
                        $descArray[$this->_preparedFields[$field]] = NULL;
                    }
				}
				else {
                    if (isset($values[$lang->id]) AND isset($values[$lang->id][$field])) {
					   $descArray[$this->_preparedFields[$field]] = $values[$lang->id][$field];
                    }
                    else {
                       $descArray[$this->_preparedFields[$field]] = NULL;
                    }
				}
			}
			$descArray[$this->_preparedFields['language_id']] = $lang->id;
			$descArray[$this->_preparedFields[$this->_getRefColumn()]] = $id;
			if ( ! empty($descs[$lang->id])) {
				$table->update(
							$descArray,
							array(
								$this->_preparedFields['language_id'].' = ?'=>$lang->id,
								$this->_preparedFields[$this->_getRefColumn()].' = ?'=>$id,
							)
						);
			}
			else {
				$table->createRow($descArray)->save();
			}
		}

		return $this;
	}
	
	/**
	 * sets current $obj fields
	 * @see application/library/App/Model/Mapper/Db/Plugin/Model_Mapper_Db_Plugin_Abstract::onBeforeSaveComplex()
	 */
    public function onBeforeSaveComplex(Model_Object_Interface $obj, array $values, $isNew = FALSE)
    {
        $lang = $this->getCurrentLanguage();
        foreach ($this->_descFields as $field) {
            $obj->{$field} = $values[$this->_getFlatArrayKey($lang->id).'_'.$field];
        }
        return $obj;
    }	


    /**
     * calls saveDescriptions
     */
    public function onAfterSaveComplex(Model_Object_Interface $obj, array $values, $isNew = FALSE)
    {
        $this->saveDescriptions($obj->id, $values);
    }


	/**
	 * join description row for current language (for _onFetchComplexAddons)
	 * @param Zend_Db_Select
	 * @return Zend_Db_Select
	 */
	public function onFetchComplex(Zend_Db_Select $select)
	{
        if ( ! $this->getCurrentLanguage()) {
            throw new Model_Mapper_Db_Plugin_Exception('current language should be set to build complex object with descripition, but it was not');
        }
		$table = $this->_table;
		$select -> joinLeft(
							array($table->getColumnPrefix() => $table->getTableName()),
							$this->_preparedFields[$this->_getRefColumn()].' = '.$this->_getRefColumn().' AND '.$this->_preparedFields['language_id'].' = '.$this->getCurrentLanguage()->id,
							$this->_getPreparedDescFields()
				   );
		return $select;

	}

	/**
	 * map description fields of current language to object
	 * @param Model_Object_Interface object itself
	 * @param array values to map
	 * @return Model_Object_Interface
	 */
	public function onBuildComplex(Model_Object_Interface $object, array $values)
	{
        if ( ! $this->getCurrentLanguage()) {
            throw new Model_Mapper_Db_Plugin_Exception('current language should be set to build complex object with descripition, but it was not');
        }
		foreach ($this->_descFields as $field) {
            if (isset($values[$this->_preparedFields[$field]])) {
			    $object->$field = $values[$this->_preparedFields[$field]];
            }
		}
		return $object;
	}


	/**
	 * setter for _descFields
	 * @param array
	 * return $this
	 */
	public function setDescFields(array $descFields)
	{
		$this->_descFields = $descFields;
        $arr = array_merge($descFields, array('language_id', $this->_getRefColumn()));
        foreach ($arr as $field) {
            $this->_preparedFields[$field] = $this->_table->getColumnPrefix().'_'.$field;
        }
		return $this;
	}

    protected function _getPreparedDescFields()
    {
        if ($this->_preparedDescFields === NULL) {
            $result = array();
            foreach ($this->_descFields as $field) {
                $result[$field] = $this->_preparedFields[$field];
            }
            $this->_preparedDescFields = array_values($result);
        }
        return $this->_preparedDescFields;
    }

	/**
	 * setting configuration
	 * @param array('mapper'(*)=>, 'table'(*)=>, 'descFields'=>, 'refColumn'=>)
	 */
	public function setConfig(array $config)
	{
		$this->setMapper($config['mapper'])
			 ->setTable($config['table'])
			 ;
        if (isset($config['refColumn'])) {
            $this->setRefColumn($config['refColumn']);
        }
		if (isset($config['descFields'])) {
			$this->setDescFields($config['descFields']);
		}
		return $this;
	}

}