<?php

class Social_Model_Object_AdvertField extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'code',
            'type',
            'value',
            'set',
            'default',
        ));

        $langs = Model_Service::factory('language')->getAll();
        foreach ($langs as $lang) {
            $this->addElement('name_'.$lang->code2);
        }
    }

    public function getName()
    {
        $lang = Model_Service::factory('language')->getCurrent();
        return $this->{'name_'.$lang->code2};
    }

    public function setName($value)
    {
        $lang = Model_Service::factory('language')->getCurrent();
        $this->{'name_'.$lang->code2} = $value;
        return $this;
    }

}
