<?php

class Model_Mapper_Config_Currency extends Model_Mapper_Config_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Model_Object_Currency',
        'Model_Collection_Interface' => 'Model_Collection_Currency',
    );

    public function makeSimpleObject(Zend_Config $conf)
    {
        $lang = Model_Service::factory('language')->getCurrent()->code2;
        $curr = $this->getInjector()->getObject('Model_Object_Interface');
        $curr->id = $conf->id;
        $curr->code = $conf->code;
        $curr->rate = $conf->rate;
        $curr->rateNonCache = $conf->rateNonCache;
        $curr->is_default = $conf->is_default;
        $curr->name = $conf->name->{$lang};
        $curr->signPre = $conf->signPre->{$lang};
        $curr->signPost = $conf->signPost->{$lang};
        return $curr;
    }

}