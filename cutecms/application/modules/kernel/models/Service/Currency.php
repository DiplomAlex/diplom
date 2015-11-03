<?php


/**
 * TODO : migrate to Zend_Currency
 */

class Model_Service_Currency extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Model_Object_Currency',
        'Model_Collection_Interface' => 'Model_Collection_Currency',
        'Model_Mapper_Interface'     => 'Model_Mapper_Config_Currency',
    );

    protected static $_all = NULL;
    protected static $_default = NULL;
    protected static $_current = NULL;

    protected static $_session = NULL;
    
    protected static $_byCodes = NULL;

    protected function _session()
    {
        if (self::$_session === NULL) {
            self::$_session = new Zend_Session_Namespace(__CLASS__);
        }
        return self::$_session;
    }

    /**
     * @return Model_Collection_Interface
     */
    public function getAll()
    {
        if (self::$_all ===  NULL) {
            $cacheKey = __CLASS__.'__'.__FUNCTION__;
            $cache = Zend_Registry::get('Zend_Cache');
            if ( ! self::$_all = $cache->load($cacheKey)) {
                self::$_all = $this->getMapper()->makeSimpleCollection(
                        Model_Service::factory('config')->read('var/currency.xml')->currency
                );
                $cache->save(self::$_all, $cacheKey);
            }
        }
        return self::$_all;
    }

    public function getAllDefaultFirst()
    {
        $coll = $this->getInjector()->getObject('Model_Collection_Interface');
        $coll->add($this->getDefault());
        $all = $this->getAll();
        foreach ($all as $curr) {
            if ( ! $curr->is_default) {
                $coll->add($curr);
            }
        }
        return $coll;
    }
    
    public function getByCode($code)
    {
        if (self::$_byCodes === NULL) {
            $all = $this->getAll();
            self::$_byCodes = array();
            foreach ($all as $curr) {
                self::$_byCodes[strtolower($curr->code)] = $curr;
            }
        }
        return self::$_byCodes[strtolower($code)];
    }
    
    /**
     * get default currency
     */
    public function getDefault()
    {
        if (self::$_default === NULL) {
            self::$_default = $this->getAll()->findOneByIs_default('1');
        }
        return self::$_default;
    }

    /**
     * get currently selected currency
     * if in session - return it
     * else - return default
     */
    public function getCurrent()
    {
        if ((self::$_current === NULL) AND isset($this->_session()->currency)) {
            self::$_current = $this->_session()->currency;
        }
        else if (self::$_current === NULL) {
            $this->resetCurrent();
        }
        return self::$_current;
    }

    public function setCurrent($curr)
    {
        if ($curr instanceof Model_Object_Interface) {
            self::$_current = $curr;
            $this->_session()->currency = $curr;
        }
        else if (is_numeric($curr)) {
            $currObj = $this->getAll()->findOneById($curr);
            self::$_current = $currObj;
            $this->_session()->currency = $currObj;
        }
        else if ( ! empty($curr)) {
            $currObj = $this->getAll()->findOneByCode($curr);
            self::$_current = $currObj;
            $this->_session()->currency = $currObj;
        }
        else {
            $this->_throwException('trying to setup empty currency as current');
        }
        return $this;
    }

    public function resetCurrent()
    {
        $this->setCurrent($this->getDefault());
        return $this;
    }

}