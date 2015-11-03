<?php

class Catalog_Model_Object_Brule extends Model_Object_Abstract
{

    protected $_hash = NULL;

    public function init()
    {

        $this->addElements(array(
            'hash',
            'code',
            'name',
            'param1','param2','param3',
        ));

    }

    public function getHash()
    {
        if ($this->_hash === NULL) {
            $this->_hash = md5($this->code . $this->name . $this->param1 . $this->param2 . $this->param3);
        }
        return $this->_hash;
    }


}

