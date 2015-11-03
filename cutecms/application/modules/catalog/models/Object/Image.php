<?php

class Catalog_Model_Object_Image extends Model_Object_Abstract
{

    public function init()
    {

        $this->addElements(array(
            'hash',
            'sort',
            'source_filename', 'source_url',
            'mime', 'size',
            'filename', 'width', 'height',
            'preview', 'prv_width', 'prv_height',
            'preview2', 'prv2_width', 'prv2_height',
            'name', 'brief',
        ));

        $langs = Model_Service::factory('language')->getAllActive();
        foreach ($langs as $lang) {
            $this->addElement('description_language_'.$lang->code2.'_name');
        }

        $langs = Model_Service::factory('language')->getAllActive();
        foreach ($langs as $lang) {
            $this->addElement('description_language_'.$lang->code2.'_brief');
        }


    }


    public function __get($name)
    {
        $pref = 'description_language_';
        $prefLen = strlen($pref);
        if ((substr($name, 0, $prefLen) == $pref) AND (empty($this->_elements[$name]))) {
            return $this->{substr($name, $prefLen + 3)}; /* 3 = strlen('en_')*/
        }
        else {
            return parent::__get($name);
        }
    }

    public function getName()
    {
        if ( ! $name = $this->{'description_language_'.Model_Service::factory('language')->getCurrent()->code2.'_name'}) {
            $name = $this->_elements['name'];
        }
        return $name;
    }

    public function getBrief()
    {
        if ( ! $brief = $this->{'description_language_'.Model_Service::factory('language')->getCurrent()->code2.'_brief'}) {
            $brief = $this->_elements['brief'];
        }
        return $brief;
    }


    public function getHash()
    {
        if (empty($this->_elements['hash'])) {
            $this->_elements['hash'] = md5(microtime().$this->name.$this->filename.$this->preview);
        }
        return $this->_elements['hash'];
    }


}
