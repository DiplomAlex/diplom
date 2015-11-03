<?php

class Catalog_Model_Collection_Attribute extends Model_Collection_Abstract
{

    public function __get($name)
    {
        return $this->findOneByCode($name);
    }

    public function __isset($name)
    {
        return (bool) $this->findOneByCode($name);
    }

}