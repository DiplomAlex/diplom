<?php

class Checkout_Model_Collection_Brule extends Model_Collection_Abstract
{

    public function __get($name)
    {
        return $this->findOneByCode($name);
    }


}
