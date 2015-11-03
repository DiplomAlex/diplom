<?php

class Catalog_Model_Collection_ItemBundle extends Model_Collection_Abstract
{
    
    
    public function __get($name)
    {
        if ( ! $obj = $this->findOneByCode($name)) {
            $obj = $this->findOneByCode($this->_getCodeByAlias($name));
        }
        return $obj;
    }

    public function __isset($name)
    {
        if ( ! $obj = $this->findOneByCode($name)) {
            $obj = $this->findOneByCode($this->_getCodeByAlias($name));
        }
        return (bool) $obj;
    }
    
    protected function _getCodeByAlias($alias)
    {
        $code = Model_Service::factory('catalog/item-bundle')->getCodeByAlias($alias);
        return $code;
    }    
    
}