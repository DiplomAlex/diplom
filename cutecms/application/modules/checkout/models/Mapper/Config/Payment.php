<?php

class Checkout_Model_Mapper_Config_Payment extends Model_Mapper_Config_Abstract
{

    /**
     * @var array
     */
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Checkout_Model_Object_Payment',
    	'Model_Collection_Interface' => 'Checkout_Model_Collection_Payment',
    );

    /**
     * @var array
     */
    protected $_i18nFields = array(
        'title', 'description',
    );

    /**
     * @var array
     */
    protected $_arrayFields = array(
        'client_requisites',
        'seller_requisites',
        'params',
        'allowed_shipments'
    );

    /**
     * @var Model_Object_Interface
     */
    protected $_lang = NULL;

    /**
     * @var Model_Collection_Interface
     */
    protected $_langs = NULL;

    /**
     *
     * @param Model_Object_Interface $lang
     * @return $this
     */
    public function setLanguage(Model_Object_Interface $lang)
    {
        $this->_lang = $lang;
        return $this;
    }

    /**
     *
     * @param Model_Collection_Interface $lang
     * @return $this
     */
    public function setLanguages(Model_Collection_Interface $langs)
    {
        $this->_langs = $langs;
        return $this;
    }

    /**
     * @return array
     */
    public function getArrayFields()
    {
        return $this->_arrayFields;
    }

    /**
     * @return array
     */
    public function getI18nFields()
    {
        return $this->_i18nFields;
    }

    /**
     * (non-PHPdoc)
     * @see Model_Mapper_Config_Abstract::makeCustomObject()
     */
    public function makeCustomObject(Zend_Config $conf)
    {
        $obj = $this->getInjector()->getObject('Model_Object_Interface');
        foreach ($conf as $key=>$row) {
            if ($obj->hasElement($key)) {
                if (in_array($key, $this->_arrayFields)) {
                    if ($row instanceof Zend_Config) {
                        $row = $row->toArray();
                    }
                    else if (empty($row)) {
                        $row = array();
                    }
                    else {
                        $row = array($row);
                    }
                }
                else if (in_array($key, $this->_i18nFields)) {
                    if ($this->_lang === NULL) {
                        throw new Model_Mapper_Exception('language was not set for mapper "'.__CLASS__.'"');
                    }
                    $row = $row->{$this->_lang->code2};
                }
                $obj->$key = $row;
            }
        }
        return $obj;
    }

    /**
     * (non-PHPdoc)
     * @see Model_Mapper_Config_Abstract::makeComplexObject()
     */
    public function makeComplexObject(Zend_Config $conf)
    {
        $obj = $this->getInjector()->getObject('Model_Object_Interface');
        if ($this->_langs === NULL) {
            throw new Model_Mapper_Exception('languages collection was not set for mapper "'.__CLASS__.'"');
        }
        $emptyI18n = array();
        foreach ($this->_langs as $lang) {
            $emptyI18n[$lang->code2] = NULL;
        }
        foreach ($conf as $key=>$row) {
            if ($obj->hasElement($key)) {
                if (in_array($key, $this->_arrayFields)) {
                    if ($row instanceof Zend_Config) {
                        $row = $row->toArray();
                    }
                    else if (empty($row)) {
                        $row = array();
                    }
                    else {
                        $row = array($row);
                    }
                }
                else if (in_array($key, $this->_i18nFields)) {
                    if ($row instanceof Zend_Config) {
                        $row = $row->toArray();
                        $row = array_merge($emptyI18n, $row);
                    }
                    else if (empty($row)) {
                        $row = $emptyI18n;
                    }
                    else {
                        $row = array_merge($emptyI18n, array($this->_lang->code2 => $row));
                    }
                }
                $obj->$key = $row;
            }
        }
        return $obj;
    }


    /**
     * @param Model_Obect_Interface $obj
     * @return Zend_Config
     */
    public function unmapSimpleObject(Model_Object_Interface $obj)
    {
        $arr = $obj->toArray();
        $result = new Zend_Config($arr);
        return $result;
    }

    public function unmapComplexObject(Model_Object_Interface $obj)
    {
        return $this->unmapSimpleObject($obj);
    }

    public function unmapCustomObject(Model_Object_Interface $obj)
    {
        return $this->unmapSimpleObject($obj);
    }

    public function unmapSimpleCollection(Model_Collection_Interface $coll)
    {
        $arr = array();
        foreach ($coll as $obj) {
            $arr[$obj->method] = $this->unmapSimpleObject($obj)->toArray();
        }
        $result = new Zend_Config($arr, TRUE);
        return $result;
    }

    public function unmapComplexCollection(Model_Collection_Interface $coll)
    {
        $arr = array();
        foreach ($coll as $obj) {
            $arr[$obj->method] = $this->unmapComplexObject($obj)->toArray();
        }
        $result = new Zend_Config($arr, TRUE);
        return $result;
    }

    public function unmapCustomCollection(Model_Collection_Interface $coll)
    {
        $arr = array();
        foreach ($coll as $obj) {
            $arr[$obj->method] = $this->unmapCustomObject($obj)->toArray();
        }
        $result = new Zend_Config($arr, TRUE);
        return $result;
    }


}