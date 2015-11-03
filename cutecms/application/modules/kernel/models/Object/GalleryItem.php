<?php

class Model_Object_GalleryItem extends Model_Object_Abstract
{
    
    public function init()
    {
        $this->addElements(array(
            'id', 
            'hash',
            'status',
            'name', 'description',
            'html_title', 'meta_keywords', 'meta_description', 
            'rc_id', 'rc_id_mime', 'rc_id_source_filename',
            'rc_id_filename',  'rc_id_width', 'rc_id_height',
            'rc_id_preview', 'rc_id_prv_width', 'rc_id_prv_height',
            'rc_id_preview2', 'rc_id_prv2_width', 'rc_id_prv2_height',
            'rc_id_preview3', 'rc_id_prv3_width', 'rc_id_prv3_height',
            'rc_id_preview4', 'rc_id_prv4_width', 'rc_id_prv4_height',
            'rc_id_preview5', 'rc_id_prv5_width', 'rc_id_prv5_height',
            'rc_id_preview6', 'rc_id_prv6_width', 'rc_id_prv6_height',
            'date_added', 'date_changed', 
            'adder_id', 'adder_login', 'adder_name', 
            'changer_id', 'changer_login', 'changer_name',
            'content_type', 'content_id', 'content_seo_id', 'content_title',
        ));
        
        $langs = Model_Service::factory('language')->getAllActive();
        foreach ($langs as $lang) {
            $this->addElement('description_language_'.$lang->id.'_name');
            $this->addElement('description_language_'.$lang->id.'_description');
        }
    }
    
    public function getHash()
    {
        if ($this->_elements['hash'] === NULL) {
            $this->_elements['hash'] = uniqid('galleryItem');
        }
        return $this->_elements['hash'];
    }
    
    

    public function getName()
    {
        if ( ! $name = @$this->_elements['description_language_'.Model_Service::factory('language')->getCurrent()->id.'_name']) {
            $name = $this->_elements['name'];
        }
        return $name;
    }

    public function setName($value)
    {
        $lang = Model_Service::factory('language')->getCurrent();
        $this->_elements['name'] = $value;
        $this->_elements['description_language_'.$lang->id.'_name'] = $value;
        return $this;
    }
    
    

    public function getDescription()
    {
        if ( ! $description = @$this->_elements['description_language_'.Model_Service::factory('language')->getCurrent()->id.'_description']) {
            $description = $this->_elements['description'];
        }
        return $description;
    }

    public function setDescription($value)
    {
        $lang = Model_Service::factory('language')->getCurrent();
        $this->_elements['description'] = $value;
        $this->_elements['description_language_'.$lang->id.'_description'] = $value;
        return $this;
    }
    
        
    
}
