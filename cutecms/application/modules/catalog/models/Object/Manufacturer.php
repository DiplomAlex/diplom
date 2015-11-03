<?php

class Catalog_Model_Object_Manufacturer extends Model_Object_Abstract
{ 
    
    public function init()
    {
        $this->addElements(array(
            'id',
            'name',
            'brief', 'full',
            'html_title', 'meta_keywords', 'meta_description',
            'rc_id', 'rc_id_filename', 'rc_id_preview',
        ));
    }
    
}