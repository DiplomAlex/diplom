<?php

class Catalog_Model_Object_AttributeVariant extends Model_Object_Abstract
{

    protected $_hash = null;

    public function init()
    {

        $this->addElements(array(
            'id',
            'sort',
            'text',
            'value',
            'hash',
            'param1', 'param2', 'param3',
        ));
    }

    public function getHash()
    {
        if ($this->_hash === null) {
            $this->_hash = md5($this->text . '|' . time() . '|' . $this->value);
        }

        return $this->_hash;
    }

}