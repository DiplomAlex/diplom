<?php

require_once 'Zend/Db/Table/Abstract.php';
require_once 'App/Model/Db/Table/Interface.php';

class Model_Db_Table_Abstract extends Zend_Db_Table_Abstract implements Model_Db_Table_Interface
{

    /**
     * column prefix separator used by default
     * @var string
     */
    const DEFAULT_PREFIX_SEPARATOR = '_';
    
    /**
     * @var string - current separator between column prefix and column name
     */
    protected $_prefixSeparator = NULL;

	/**
	 * @var string prefix for column names
	 */
	protected $_columnPrefix = NULL;

	/**
	 * @return string
	 */
    public function getColumnPrefix()
    {
        if ($this->_columnPrefix === NULL) {
        	$this->setColumnPrefix($this->info('name'));
        }
        return $this->_columnPrefix;
    }

    /**
     * @param string
     * @return Model_Db_Table_Abstract $this
     */
    public function setColumnPrefix($prefix)
    {
    	$this->_columnPrefix = $prefix;
    	return $this;
    }


    /**
     * just returns table name
     * @return string
     */
    public function getTableName()
    {
        return $this->_name;
    }
    
    /**
     * get current separtor between column name and prefix
     * in "prefix_column" separator is "_"
     * @return string
     */
    public function getPrefixSeparator()
    {
        if ($this->_prefixSeparator === NULL) {
            $this->setPrefixSeparator(self::DEFAULT_PREFIX_SEPARATOR);
        }
        return $this->_prefixSeparator;        
    }
    
    /**
     * sets current separator
     * @param string $separator
     * @return $this
     */
    public function setPrefixSeparator($separator)
    {
        $this->_prefixSeparator = $separator;
        return $this->_prefixSeparator;
    }
    

}